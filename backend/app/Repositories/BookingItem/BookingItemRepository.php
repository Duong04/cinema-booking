<?php
namespace App\Repositories\BookingItem;

use App\Models\BookingItem;
use App\Repositories\BookingItem\BookingItemRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class BookingItemRepository extends BaseRepository implements BookingItemRepositoryInterface {
    public function __construct(BookingItem $model)
    {
        $this->model = $model;
    }

    public function insert(array $data) {
        return $this->model->insert($data);
    }
}