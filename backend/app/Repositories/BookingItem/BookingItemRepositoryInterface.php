<?php
namespace App\Repositories\BookingItem;

use App\Repositories\Base\BaseRepositoryInterface;

interface BookingItemRepositoryInterface extends BaseRepositoryInterface {
    public function insert(array $data);
}