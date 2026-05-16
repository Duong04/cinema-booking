<?php
namespace App\Services;

use App\Repositories\SeatHoldRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeatHoldService
{
    protected $seatHoldRepository;

    public function __construct(SeatHoldRepository $seatHoldRepository)
    {
        $this->seatHoldRepository = $seatHoldRepository;
    }

    public function getListShowtime(string $showtimeId)
    {
        return $this->seatHoldRepository->getListShowtime($showtimeId);
    }

    public function hold($data)
    {
        $user      = auth()->user();
        $showtimeId = $data['showtime_id'];
        $seatIds    = $data['seat_ids'];
        $expiredAt  = now()->addMinutes(15);
 
        try {
            DB::beginTransaction();
 
            $existing = $this->seatHoldRepository->checkHoldTransaction($seatIds, $showtimeId);
 
            if ($existing->isNotEmpty()) {
                DB::rollBack();
 
                throw new \Exception('Một số ghế đã được giữ bởi người khác.');
            }
 
            $bookedSeatIds = $this->seatHoldRepository->getBookedSeatIds($seatIds, $showtimeId);
 
            if ($bookedSeatIds->isNotEmpty()) {
                DB::rollBack();
 
                throw new \Exception('Một số ghế đã được đặt.');
            }
 
            $this->seatHoldRepository->deleteByMixCol($seatIds, $showtimeId, $user->id);
 
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
 
            return $holds;
 
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function release($data)
    {
        $showtimeId = $data['showtime_id'];
 
        return $this->seatHoldRepository->deleteByUser($showtimeId, auth()->id());
    }
}