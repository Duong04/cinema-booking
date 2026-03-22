<?php 
namespace App\Repositories\SeatType;

use App\Repositories\Base\BaseRepositoryInterface;
interface SeatTypeRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
}