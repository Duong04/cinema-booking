<?php 
namespace App\Repositories\Genre;

use App\Repositories\Base\BaseRepositoryInterface;
interface GenreRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
}