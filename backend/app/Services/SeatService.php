<?php 
namespace App\Services;

use App\Repositories\Room\RoomRepository;
use App\Repositories\Room\RoomRepositoryInterface;
use App\Repositories\Seat\SeatRepositoryInterface;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $existingLabels = $this->seatRepository->getExistingRowLabels($roomId);

        $incomingLabels = array_map(
            fn($row) => strtoupper($row['label']),
            $data['rows']
        );

        $duplicates = array_intersect($existingLabels, $incomingLabels);

        if (!empty($duplicates)) {
            throw new HttpException(422, 'Các hàng đã tồn tại: ' . implode(', ', $duplicates));
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

    public function update(string $roomId, string $rowLabel, array $data): void
    {
        $room = $this->roomRepository->find($roomId);
        if (!$room) {
            throw new HttpException(404, 'Phòng không tồn tại.');
        }

        $existingLabels = $this->seatRepository->getExistingRowLabels($roomId);
        if (!in_array(strtoupper($rowLabel), $existingLabels)) {
            throw new HttpException(422, "Hàng {$rowLabel} không tồn tại trong phòng này.");
        }

        $rowLabel  = strtoupper($rowLabel);
        $newCount  = $data['seats_per_row'];
        $current   = $this->seatRepository->countByRow($roomId, $rowLabel);

        $this->seatRepository->updateSeatTypeForRow($roomId, $rowLabel, $data['seat_type_id']);

        if ($newCount > $current) {
            $seats = [];
            $now   = now();
            for ($i = $current + 1; $i <= $newCount; $i++) {
                $seats[] = [
                    'id'           => Str::uuid7(),
                    'room_id'      => $roomId,
                    'seat_type_id' => $data['seat_type_id'],
                    'row_label'    => $rowLabel,
                    'seat_number'  => $i,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
            $this->seatRepository->insert($seats);

        } elseif ($newCount < $current) {
            $this->seatRepository->deleteByRowFrom($roomId, $rowLabel, $newCount);
        }
    }

    public function deleteRow(string $roomId, string $rowLabel): void
    {
        $room = $this->roomRepository->find($roomId);
        if (!$room) {
            throw new HttpException(404, 'Phòng không tồn tại.');
        }

        $existingLabels = $this->seatRepository->getExistingRowLabels($roomId);
        if (!in_array(strtoupper($rowLabel), $existingLabels)) {
            throw new HttpException(422, "Hàng {$rowLabel} không tồn tại trong phòng này.");
        }

        $this->seatRepository->deleteRow($roomId, strtoupper($rowLabel));
    }
}