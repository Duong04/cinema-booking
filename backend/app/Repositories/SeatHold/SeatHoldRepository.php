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

    public function getListShowtime(string $showtimeId)
    {
        return $this->model->with(['seat', 'user:id,name,email,avatar'])
            ->where('showtime_id', $showtimeId)
            ->where('expired_at', '>', now())
            ->get();
    }

    public function insert(array $data) {
        return $this->model->insert($data);
    }

    public function checkHoldTransaction(string $seatIds, string $showtimeId, string $userId = null)
    {
        return $this->model->whereIn('seat_id', $seatIds)
            ->where('showtime_id', $showtimeId)
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('expired_at', '>', now())
            ->lockForUpdate()
            ->get();
    }

    public function deleteByMixCol(array $seatIds, string $showtimeId, string $userId) {
        return $this->model->whereIn('seat_id', $seatIds)
                ->where('showtime_id', $showtimeId)
                ->where('user_id', $userId)
                ->delete();
    }

    public function deleteByUser(string $showtimeId, string $userId) {
        return $this->model->where('showtime_id', $showtimeId)
                ->where('user_id', $userId)
                ->delete();
    }

    public function getBookedSeatIds(array $seatIds, string $showtimeId)
    {
        return $this->model->query()
            ->whereIn('seat_id', $seatIds)
            ->where('showtime_id', $showtimeId)
            ->whereHas('booking', fn($q) => $q->whereIn('status', ['pending', 'confirmed']))
            ->pluck('seat_id');
    }
}