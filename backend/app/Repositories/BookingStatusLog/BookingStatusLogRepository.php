<?php
namespace App\Repositories\BookingStatusLog;

use App\Models\BookingStatusLog;
use App\Repositories\BookingStatusLog\BookingStatusLogRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class BookingStatusLogRepository extends BaseRepository implements BookingStatusLogRepositoryInterface {
    public function __construct(BookingStatusLog $model)
    {
        $this->model = $model;
    }
}