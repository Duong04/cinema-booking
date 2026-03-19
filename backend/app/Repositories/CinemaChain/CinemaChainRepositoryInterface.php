<?php 
namespace App\Repositories\CinemaChain;

use App\Repositories\Base\BaseRepositoryInterface;
interface CinemaChainRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
}