<?php 
namespace App\Repositories\Movie;

use App\Repositories\Base\BaseRepositoryInterface;
interface MovieRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q, $status);
}