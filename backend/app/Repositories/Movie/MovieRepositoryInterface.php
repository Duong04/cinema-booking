<?php 
namespace App\Repositories\Movie;

use App\Repositories\Base\BaseRepositoryInterface;
interface MovieRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q, $status);
    public function getPublicMovies($limit = 15, $q = null, $status = null, $sort = null, $period = null, $genreId = null);
    public function findBySlug($slug);
}
