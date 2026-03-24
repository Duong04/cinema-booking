<?php 
namespace App\Repositories\ShowtimePrice;

use App\Repositories\Base\BaseRepositoryInterface;
interface ShowtimePriceRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
    public function insert($data);
    public function deleteByShowtime($showtimeId);
}