<?php
namespace App\Repositories\SeatHold;

use App\Repositories\Base\BaseRepositoryInterface;

interface SeatHoldRepositoryInterface extends BaseRepositoryInterface {
    public function getListShowtime(string $showtimeId);
    public function insert(array $data);
    public function checkHoldTransaction(array $seatIds, string $showtimeId, string $userId = null);
    public function deleteByMixCol(array $seatIds, string $showtimeId, string $userId);
    public function deleteByUser(string $showtimeId, string $userId);
    public function deleteExpired(?string $showtimeId = null);
    public function getBookedSeatIds(array $seatIds, string $showtimeId);
    public function getActiveHoldsByShowtime(string $showtimeId);
}
