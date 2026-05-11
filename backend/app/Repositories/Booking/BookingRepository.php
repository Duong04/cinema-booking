<?php 
namespace App\Repositories\Booking;

use App\Repositories\Base\BaseRepository;
use App\Models\Booking;
use App\Repositories\Booking\BookingRepositoryInterface;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        $this->model = $model;
    }

}