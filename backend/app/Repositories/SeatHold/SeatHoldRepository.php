<?php
namespace App\Repositories\SeatHold;

use App\Models\SeatHold;
use App\Repositories\SeatHold\SeatHoldRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class SeatHoldRepository extends BaseRepository implements SeatHoldRepositoryInterface {
    public function __construct(SeatHold $model)
    {
        $this->model = $model;
    }

    public function checkHoldTransaction(string $seatIds, string $showtimeId, string $userId)
    {
        return SeatHold::whereIn('seat_id', $seatIds)
            ->where('showtime_id', $showtimeId)
            ->where('user_id', $userId)
            ->where('expired_at', '>', now())
            ->lockForUpdate()
            ->get();
    }

    public function deleteByMixCol(array $seatIds, string $showtimeId, string $userId) {
        return SeatHold::whereIn('seat_id', $seatIds)
                ->where('showtime_id', $showtimeId)
                ->where('user_id', $userId)
                ->delete();
    }
}