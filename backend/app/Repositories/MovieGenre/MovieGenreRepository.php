<?php 
namespace App\Repositories\MovieGenre;

use App\Repositories\Base\BaseRepository;
use App\Models\MovieGenre;
use App\Repositories\MovieGenre\MovieGenreRepositoryInterface;

class MovieGenreRepository extends BaseRepository implements MovieGenreRepositoryInterface
{
    public function __construct(MovieGenre $model)
    {
        $this->model = $model;
    }

    public function insert($data) {
        return $this->model->insert($data);
    }

    public function deleteByMovie($movieId) {
        return $this->model->where('movie_id', $movieId)->delete();
    }
}