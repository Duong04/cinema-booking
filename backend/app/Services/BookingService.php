<?php
namespace App\Services;

use App\Repositories\Booking\BookingRepositoryInterface;
use App\Repositories\BookingStatusLog\BookingStatusLogRepositoryInterface;
use App\Repositories\SeatHold\SeatHoldRepositoryInterface;
use App\Repositories\Showtime\ShowtimeRepositoryInterface;
use App\Repositories\ShowtimePrice\ShowtimePriceRepositoryInterface;
use App\Repositories\BookingItem\BookingItemRepositoryInterface;
use App\Repositories\BookingCombo\BookingComboRepositoryInterface;
use App\Repositories\Seat\SeatRepositoryInterface;
use App\Repositories\Combo\ComboRepositoryInterface;
use App\Repositories\Promotion\PromotionRepositoryInterface;
use App\Repositories\PromotionUsage\PromotionUsageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BookingService
{
    private $bookingRepository;
    private $bookingStatusLogRepository;
    private $seatHoldRepository;
    private $showtimeRepository;
    private $showtimePriceRepository;
    private $seatRepository;
    private $bookingItemRepository;
    private $bookingComboRepository;
    private $comboRepository;
    private $promotionRepository;
    private $promotionUsageRepository;

    public function __construct(
        BookingRepositoryInterface $bookingRepository, 
        BookingStatusLogRepositoryInterface $bookingStatusLogRepository, 
        SeatHoldRepositoryInterface $seatHoldRepository, 
        ShowtimeRepositoryInterface $showtimeRepository, 
        ShowtimePriceRepositoryInterface $showtimePriceRepository, 
        SeatRepositoryInterface $seatRepository, 
        BookingItemRepositoryInterface $bookingItemRepository, 
        BookingComboRepositoryInterface $bookingComboRepository, 
        ComboRepositoryInterface $comboRepository, 
        PromotionRepositoryInterface $promotionRepository, 
        PromotionUsageRepositoryInterface $promotionUsageRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->bookingStatusLogRepository = $bookingStatusLogRepository;
        $this->seatHoldRepository = $seatHoldRepository;
        $this->showtimeRepository = $showtimeRepository;
        $this->showtimePriceRepository = $showtimePriceRepository;
        $this->seatRepository = $seatRepository;
        $this->bookingItemRepository = $bookingItemRepository;
        $this->bookingComboRepository = $bookingComboRepository;
        $this->comboRepository = $comboRepository;
        $this->promotionRepository = $promotionRepository;
        $this->promotionUsageRepository = $promotionUsageRepository;
    }

    public function paginate(int $limit, ?string $q, ?string $status): LengthAwarePaginator
    {
        return $this->bookingRepository->paginate($limit, $q, $status);
    }

    public function find($id) {
        $booking = $this->bookingRepository->find($id, ['*'], [
            'user:id,name,email,avatar',
            'showtime.movie',
            'showtime.room.cinema',
            'items.seat.seatType',
            'statusLogs',
            'payment',
            'combos',
            'promotions',
        ]);

        return $booking;
    }

    private function detailRelations(): array
    {
        return [
            'user:id,name,email,avatar',
            'showtime.movie',
            'showtime.room.cinema',
            'items.seat.seatType',
            'statusLogs',
            'payment',
            'combos',
            'promotions',
        ];
    }

    public function create($data)
    {
        $user = auth()->user();
        $showtimeId = $data['showtime_id'];
        $seatIds = $data['seat_ids'];
        $comboItems = collect($data['combos'] ?? [])
            ->groupBy('combo_id')
            ->map(fn($items) => (int) $items->sum('quantity'))
            ->filter(fn($quantity) => $quantity > 0);

        try {
            DB::beginTransaction();
 
            $holds = $this->seatHoldRepository->checkHoldTransaction($seatIds, $showtimeId, $user->id);
 
            if ($holds->count() !== count($seatIds)) {
                DB::rollBack();
                throw new HttpException(422, 'Một số ghế không còn được giữ hoặc đã hết hạn. Vui lòng chọn lại.');
            }
 
            $showtime = $this->showtimeRepository->find($showtimeId, ['*'], ['movie', 'room.cinema']);
 

            $seats = $this->seatRepository->getKeyById($seatIds, ['seatType']);
 
            $showtimePrices = $this->showtimePriceRepository->getKeyBySeatTypeId($showtimeId);
 
            $ticketAmount = 0;
            $items = [];
 
            foreach ($seatIds as $seatId) {
                $seat      = $seats[$seatId];
                $seatType  = $seat->seatType;
                $price     = $showtimePrices[$seatType->id]->price
                    ?? ($showtime->base_price * $seatType->base_multiplier);
 
                $ticketAmount += $price;
 
                $items[] = [
                    'id'             => Str::uuid(),
                    'seat_id'        => $seatId,
                    'price'          => $price,
                    'seat_type_name' => $seatType->name,
                    'movie_title'    => $showtime->movie->title,
                    'room_name'      => $showtime->room->name,
                    'seat_label'     => $seat->row_label . $seat->seat_number,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            $comboAmount = 0;
            $bookingCombos = [];

            if ($comboItems->isNotEmpty()) {
                $combos = $this->comboRepository->getActiveByIds($comboItems->keys()->all());

                if ($combos->count() !== $comboItems->count()) {
                    DB::rollBack();
                    throw new HttpException(422, 'Một số combo không tồn tại hoặc đang ngừng bán.');
                }

                foreach ($comboItems as $comboId => $quantity) {
                    $combo = $combos[$comboId];

                    if ($combo->cinema_id !== $showtime->room->cinema_id) {
                        DB::rollBack();
                        throw new HttpException(422, "Combo {$combo->name} không áp dụng cho rạp này.");
                    }

                    $unitPrice = (float) $combo->price;
                    $totalPrice = $unitPrice * $quantity;
                    $comboAmount += $totalPrice;

                    $bookingCombos[] = [
                        'id' => Str::uuid(),
                        'combo_id' => $combo->id,
                        'combo_name' => $combo->name,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $subtotal = $ticketAmount + $comboAmount;
            $discountAmount = 0;
            $promotion = null;

            if (! empty($data['promotion_code'])) {
                $promotion = $this->promotionRepository->findActiveByCodeForUpdate($data['promotion_code']);

                if (! $promotion) {
                    DB::rollBack();
                    throw new HttpException(422, 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn.');
                }

                if ($promotion->applicable_to && ! in_array($promotion->applicable_to, ['booking', 'ticket', 'combo'])) {
                    DB::rollBack();
                    throw new HttpException(422, 'Mã khuyến mãi không áp dụng cho đặt vé.');
                }

                if ($promotion->usage_limit !== null && $this->promotionUsageRepository->countByPromotion($promotion->id) >= $promotion->usage_limit) {
                    DB::rollBack();
                    throw new HttpException(422, 'Mã khuyến mãi đã hết lượt sử dụng.');
                }

                if ($promotion->per_user_limit !== null && $this->promotionUsageRepository->countByPromotionAndUser($promotion->id, $user->id) >= $promotion->per_user_limit) {
                    DB::rollBack();
                    throw new HttpException(422, 'Bạn đã dùng mã khuyến mãi này quá số lần cho phép.');
                }

                $discountBase = match ($promotion->applicable_to) {
                    'ticket' => $ticketAmount,
                    'combo' => $comboAmount,
                    default => $subtotal,
                };

                $discountAmount = $promotion->discount_type === 'percentage'
                    ? $discountBase * ((float) $promotion->discount_value / 100)
                    : (float) $promotion->discount_value;

                $discountAmount = min($discountAmount, $discountBase);
            }
 
            $booking = $this->bookingRepository->create([
                'user_id'      => $user->id,
                'showtime_id'  => $showtimeId,
                'total_amount' => max($subtotal - $discountAmount, 0),
                'status'       => 'pending',
                'expired_at'   => now()->addMinutes(15),
            ]);
 
            $bookingItems = collect($items)->map(fn($item) => array_merge($item, [
                'booking_id' => $booking->id,
            ]))->toArray();
 
            $this->bookingItemRepository->insert($bookingItems);

            if (! empty($bookingCombos)) {
                $bookingCombos = collect($bookingCombos)->map(fn($item) => array_merge($item, [
                    'booking_id' => $booking->id,
                ]))->toArray();

                $this->bookingComboRepository->insert($bookingCombos);
            }

            if ($promotion) {
                $this->promotionUsageRepository->create([
                    'promotion_id' => $promotion->id,
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'discount_amount' => $discountAmount,
                    'used_at' => now(),
                ]);
            }
 
            $this->seatHoldRepository->deleteByMixCol($seatIds, $showtimeId, $user->id);

            $this->bookingStatusLogRepository->create([
                'booking_id' => $booking->id,
                'old_status' => null,
                'new_status' => 'pending',
                'changed_at' => now(),
            ]);
 
            DB::commit();
 
            return $booking->load($this->detailRelations());
 
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($data, $id)
    {
        $booking = $this->bookingRepository->find($id);
        $oldStatus = $booking->status;
        $payload = collect($data)
            ->only(['status', 'cancellation_reason', 'expired_at', 'confirmed_at'])
            ->filter(fn($value) => $value !== null)
            ->toArray();

        if (empty($payload)) {
            return $booking->load($this->detailRelations());
        }

        if (($payload['status'] ?? null) === 'confirmed' && empty($payload['confirmed_at'])) {
            $payload['confirmed_at'] = now();
        }

        if (in_array($payload['status'] ?? null, ['cancelled', 'refunded']) && empty($payload['cancelled_at'])) {
            $payload['cancelled_at'] = now();
        }

        try {
            DB::beginTransaction();

            $booking->update($payload);

            if (isset($payload['status']) && $payload['status'] !== $oldStatus) {
                $this->bookingStatusLogRepository->create([
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $payload['status'],
                    'changed_at' => now(),
                ]);
            }

            DB::commit();

            return $booking->fresh()->load($this->detailRelations());
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel($data, $id)
    {
        $booking = $this->bookingRepository->find($id);
 
        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            throw new HttpException(422, "Không thể huỷ booking ở trạng thái {$booking->status}.");    
        }
 
        try {
            DB::beginTransaction();
 
            $oldStatus = $booking->status;
            $newStatus = $oldStatus === 'confirmed' ? 'refunded' : 'cancelled';
 
            $booking->update([
                'status'               => $newStatus,
                'cancellation_reason'  => $data['cancellation_reason'] ?? null,
                'cancelled_at'         => now(),
            ]);
 
            $this->bookingStatusLogRepository->create([
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
            ]);
 
            if ($newStatus === 'refunded') {
                
            }
 
            DB::commit();
 
            return $booking->fresh();
 
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
