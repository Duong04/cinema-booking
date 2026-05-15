<?php
namespace App\Services;

use App\Repositories\Booking\BookingRepositoryInterface;
use App\Repositories\BookingStatusLog\BookingStatusLogRepositoryInterface;
use App\Repositories\SeatHold\SeatHoldRepositoryInterface;
use App\Repositories\Showtime\ShowtimeRepositoryInterface;
use App\Repositories\ShowtimePrice\ShowtimePriceRepositoryInterface;
use App\Repositories\BookingItem\BookingItemRepositoryInterface;
use App\Repositories\Seat\SeatRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    private $bookingRepository;
    private $bookingStatusLogRepository;
    private $seatHoldRepository;
    private $showtimeRepository;
    private $showtimePriceRepository;
    private $seatRepository;
    private $bookingItemRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository, BookingStatusLogRepositoryInterface $bookingStatusLogRepository, SeatHoldRepositoryInterface $seatHoldRepository, ShowtimeRepositoryInterface $showtimeRepository, ShowtimePriceRepositoryInterface $showtimePriceRepository, SeatRepositoryInterface $seatRepository, BookingItemRepositoryInterface $bookingItemRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->bookingStatusLogRepository = $bookingStatusLogRepository;
        $this->seatHoldRepository = $seatHoldRepository;
        $this->showtimeRepository = $showtimeRepository;
        $this->showtimePriceRepository = $showtimePriceRepository;
        $this->seatRepository = $seatRepository;
        $this->bookingItemRepository = $bookingItemRepository;
    }

    public function paginate($limit, $q, $status)
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

    public function create($data)
    {
        DB::beginTransaction();
        $user = auth()->user();
        $showtimeId = $data['showtime_id'];
        $seatIds = $data['seat_ids'];
        try {
            DB::beginTransaction();
 
            $holds = $this->seatHoldRepository->checkHoldTransaction($seatIds, $showtimeId, $user->id);
 
            if ($holds->count() !== count($seatIds)) {
                DB::rollBack();
                throw new \Exception('Một số ghế không còn được giữ hoặc đã hết hạn. Vui lòng chọn lại.');
            }
 
            $showtime = $this->showtimeRepository->find($showtimeId, ['*'], ['movie', 'room']);
 

            $seats = $this->seatRepository->getKeyById($seatIds, ['seatType']);
 
            $showtimePrices = $this->showtimePriceRepository->getKeyBySeatTypeId($showtimeId);
 
            $totalAmount = 0;
            $items = [];
 
            foreach ($seatIds as $seatId) {
                $seat      = $seats[$seatId];
                $seatType  = $seat->seatType;
                $price     = $showtimePrices[$seatType->id]->price
                    ?? ($showtime->base_price * $seatType->base_multiplier);
 
                $totalAmount += $price;
 
                $items[] = [
                    'id'             => Str::uuid(),
                    'showtime_id'    => $showtimeId,
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
 
            $booking = $this->bookingRepository->create([
                'user_id'      => $user->id,
                'showtime_id'  => $showtimeId,
                'total_amount' => $totalAmount,
                'status'       => 'pending',
                'expired_at'   => now()->addMinutes(15),
            ]);
 
            $bookingItems = collect($items)->map(fn($item) => array_merge($item, [
                'booking_id' => $booking->id,
            ]))->toArray();
 
            $this->bookingItemRepository->insert($bookingItems);
 
            $this->seatHoldRepository->deleteByMixCol($seatIds, $showtimeId, $user->id);

            $this->bookingStatusLogRepository->create([
                'booking_id' => $booking->id,
                'old_status' => null,
                'new_status' => 'pending',
                'changed_at' => now(),
            ]);
 
            DB::commit();
 
            return $booking->load('items');
 
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel($data, string $id): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);
 
        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            throw new \Exception("Không thể huỷ booking ở trạng thái {$booking->status}.");    
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
                // TODO: dispatch(new ProcessRefundJob($booking));
            }
 
            DB::commit();
 
            return $booking->fresh();
 
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}