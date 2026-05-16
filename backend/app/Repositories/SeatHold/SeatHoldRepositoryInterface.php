<?php
namespace App\Repositories\SeatHold;

use App\Repositories\Base\BaseRepositoryInterface;

interface SeatHoldRepositoryInterface extends BaseRepositoryInterface {
    public function getListShowtime(string $showtimeId);
    public function insert(array $data);
    public function checkHoldTransaction(string $seatIds, string $showtimeId, string $userId = null);
    public function deleteByMixCol(array $seatIds, string $showtimeId, string $userId);
    public function deleteByUser(string $showtimeId, string $userId);
    public function getBookedSeatIds(array $seatIds, string $showtimeId);
}