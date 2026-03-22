<?php 
namespace App\Repositories\Seat;

use App\Repositories\Base\BaseRepository;
use App\Models\Seat;
use App\Repositories\Seat\SeatRepositoryInterface;

class SeatRepository extends BaseRepository implements SeatRepositoryInterface
{
    public function __construct(Seat $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $roles = $this->model->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $roles->orderByDesc('created_at')->paginate($limit);
    }

    public function getSeatByRoom($roomId) {
        $seats = $this->model->where('room_id', $roomId)
            ->with('seatType')
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get()
            ->groupBy('row_label');
        
        return $seats;
    }

    public function insert($data) {
        return $this->model->insert($data);
    }

    public function checkRoomId($roomId) {
        return $this->model->where('room_id', $roomId)->exists();
    }

}