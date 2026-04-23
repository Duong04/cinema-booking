<?php 
namespace App\Repositories\Cinema;

use App\Repositories\Base\BaseRepositoryInterface;
interface CinemaRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q, $cityId, $cinemaChainId);
}