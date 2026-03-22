<?php 
namespace App\Services;

use App\Repositories\Room\RoomRepository;
use App\Repositories\Room\RoomRepositoryInterface;
use App\Repositories\Seat\SeatRepositoryInterface;
use Illuminate\Support\Str;

class SeatService {
    private $seatRepository;
    private $roomRepository;

    public function __construct(SeatRepositoryInterface $seatRepository, RoomRepositoryInterface $roomRepository) {
        $this->seatRepository = $seatRepository;
        $this->roomRepository = $roomRepository;
    }

    public function getSeatByRoom($roomId) {
        $seats = $this->seatRepository->getSeatByRoom($roomId);

        return $seats;
    }

    public function create($data, $roomId) {
        $room = $this->roomRepository->find($roomId);

        $hasSeats = $this->seatRepository->checkRoomId($roomId);
        if ($hasSeats) {
            throw new \Exception('Phòng này đã có ghế, vui lòng xóa trước khi tạo mới.');
        }

        $seats = [];
        $now   = now();

        foreach ($data['rows'] as $row) {
            for ($i = 1; $i <= $row['seats_per_row']; $i++) {
                $seats[] = [
                    'id' => Str::uuid7(),
                    'room_id'      => $roomId,
                    'seat_type_id' => $row['seat_type_id'],
                    'row_label'    => strtoupper($row['label']),
                    'seat_number'  => $i,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }

        $resSeats = $this->seatRepository->insert($seats);
        
        return $resSeats;
    }
}