<?php
namespace App\Repositories\SeatHold;

use App\Repositories\Base\BaseRepositoryInterface;

interface SeatHoldRepositoryInterface extends BaseRepositoryInterface {
    public function checkHoldTransaction(string $seatIds, string $showtimeId, string $userId);
    public function deleteByMixCol(array $seatIds, string $showtimeId, string $userId);
}