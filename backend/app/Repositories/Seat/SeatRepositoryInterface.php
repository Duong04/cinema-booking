<?php
namespace App\Repositories\Seat;

use App\Repositories\Base\BaseRepositoryInterface;
interface SeatRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
    public function getSeatByRoom($roomId);
    public function insert($data);
    public function checkRoomId($roomId);
    public function getKeyById(array $seatIds, array $with = []);
    public function getExistingRowLabels($roomId);
    public function updateSeatTypeForRow(string $roomId, string $rowLabel, string $seatTypeId);
    public function countByRow(string $roomId, string $rowLabel);
    public function deleteByRowFrom(string $roomId, string $rowLabel, int $fromSeatNumber);
    public function deleteRow(string $roomId, string $rowLabel);
}