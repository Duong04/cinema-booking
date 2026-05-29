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

    public function paginate($limit = 15, $q)
    {
        $seats = $this->model->when($q, fn($query) => $query->where('name', 'like', "%$q%"));

        return $seats->orderByDesc('created_at')->paginate($limit);
    }

    public function getSeatByRoom($roomId)
    {
        $seats = $this->model->where('room_id', $roomId)
            ->with('seatType')
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get()
            ->groupBy('row_label');

        return $seats;
    }

    public function getSeatsByRoom(string $roomId)
    {
        return $this->model->with('seatType')
            ->where('room_id', $roomId)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();
    }

    public function insert($data)
    {
        return $this->model->insert($data);
    }

    public function checkRoomId($roomId)
    {
        return $this->model->where('room_id', $roomId)->exists();
    }

    public function getKeyById(array $seatIds, array $with = []) {
        return $this->model->with($with)->whereIn('id', $seatIds)
            ->get()
            ->keyBy('id');
    }

    public function getExistingRowLabels($roomId)
    {
        return $this->model->where('room_id', $roomId)
            ->distinct()
            ->pluck('row_label')
            ->toArray();
    }

    public function updateSeatTypeForRow(string $roomId, string $rowLabel, string $seatTypeId)
    {
        return $this->model->where('room_id', $roomId)
            ->where('row_label', $rowLabel)
            ->update(['seat_type_id' => $seatTypeId]);
    }

    public function countByRow(string $roomId, string $rowLabel): int
    {
        return $this->model->where('room_id', $roomId)
            ->where('row_label', $rowLabel)
            ->count();
    }

    public function deleteByRowFrom(string $roomId, string $rowLabel, int $fromSeatNumber)
    {
        return $this->model->where('room_id', $roomId)
            ->where('row_label', $rowLabel)
            ->where('seat_number', '>', $fromSeatNumber)
            ->delete();
    }

    public function deleteRow(string $roomId, string $rowLabel)
    {
        return $this->model->where('room_id', $roomId)
            ->where('row_label', $rowLabel)
            ->delete();
    }

}
