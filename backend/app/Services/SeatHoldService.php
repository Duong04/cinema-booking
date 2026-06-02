<?php
namespace App\Services;

use App\Events\SeatStatusChanged;
use App\Repositories\SeatHold\SeatHoldRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SeatHoldService
{

    public function __construct(
        private SeatHoldRepositoryInterface $seatHoldRepository
    ) {
    }

    public function getListShowtime(string $showtimeId)
    {
        $this->seatHoldRepository->deleteExpired($showtimeId);

        return $this->seatHoldRepository->getListShowtime($showtimeId);
    }

    public function deleteExpired(?string $showtimeId = null)
    {
        $expiredHolds = $this->seatHoldRepository->getExpired($showtimeId);
        $deleted = $this->seatHoldRepository->deleteExpired($showtimeId);

        $expiredHolds
            ->groupBy('showtime_id')
            ->each(function ($holds, string $expiredShowtimeId) {
                event(new SeatStatusChanged(
                    $expiredShowtimeId,
                    $holds->pluck('seat_id')->values()->all(),
                    'available'
                ));
            });

        return $deleted;
    }

    public function hold($data)
    {
        $user      = auth()->user();
        $showtimeId = $data['showtime_id'];
        $seatIds    = $data['seat_ids'];
        $expiredAt  = now()->addMinutes(10);
        $releasedSeatIds = [];
 
        try {
            DB::beginTransaction();

            $this->seatHoldRepository->deleteExpired($showtimeId);
 
            $existing = $this->seatHoldRepository->checkHoldTransaction($seatIds, $showtimeId);
            $conflicting = $existing->reject(fn($hold) => $hold->user_id === $user->id);
 
            if ($conflicting->isNotEmpty()) {
                DB::rollBack();
 
                throw new HttpException(422, 'Một số ghế đã được giữ bởi người khác.');
            }
 
            $bookedSeatIds = $this->seatHoldRepository->getBookedSeatIds($seatIds, $showtimeId);
 
            if ($bookedSeatIds->isNotEmpty()) {
                DB::rollBack();
 
                throw new HttpException(422, 'Một số ghế đã được đặt.');
            }
 
            $releasedSeatIds = $this->seatHoldRepository
                ->getActiveByUser($showtimeId, $user->id)
                ->pluck('seat_id')
                ->diff($seatIds)
                ->values()
                ->all();

            $this->seatHoldRepository->deleteByUser($showtimeId, $user->id);
 
            $holds = collect($seatIds)->map(fn($seatId) => [
                'id'          => Str::uuid(),
                'user_id'     => $user->id,
                'showtime_id' => $showtimeId,
                'seat_id'     => $seatId,
                'expired_at'  => $expiredAt,
                'created_at'  => now(),
                'updated_at'  => now(),
            ])->toArray();
 
            $this->seatHoldRepository->insert($holds);
 
            DB::commit();

            if (! empty($releasedSeatIds)) {
                event(new SeatStatusChanged($showtimeId, $releasedSeatIds, 'available'));
            }

            event(new SeatStatusChanged(
                $showtimeId,
                $seatIds,
                'held',
                $user->id,
                $expiredAt->toISOString()
            ));
 
            return $holds;
 
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function release($data)
    {
        $showtimeId = $data['showtime_id'];
        $seatIds = $this->seatHoldRepository
            ->getActiveByUser($showtimeId, auth()->id())
            ->pluck('seat_id')
            ->values()
            ->all();
 
        $deleted = $this->seatHoldRepository->deleteByUser($showtimeId, auth()->id());

        if (! empty($seatIds)) {
            event(new SeatStatusChanged($showtimeId, $seatIds, 'available'));
        }

        return $deleted;
    }
}
