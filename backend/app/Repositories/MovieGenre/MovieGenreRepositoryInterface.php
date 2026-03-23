<?php 
namespace App\Repositories\MovieGenre;

use App\Repositories\Base\BaseRepositoryInterface;
interface MovieGenreRepositoryInterface extends BaseRepositoryInterface
{
    public function insert($data);
    public function deleteByMovie($movieId);
}