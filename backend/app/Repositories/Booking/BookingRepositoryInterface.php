<?php 
namespace App\Repositories\Booking;

use App\Repositories\Base\BaseRepositoryInterface;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate(int $limit, ?string $q, ?string $status);
}
