<?php 
namespace App\Repositories\Movie;

use App\Repositories\Base\BaseRepository;
use App\Models\Movie;
use App\Repositories\Movie\MovieRepositoryInterface;

class MovieRepository extends BaseRepository implements MovieRepositoryInterface
{
    public function __construct(Movie $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $movies = $this->model->with('genres:id,name')->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $movies->orderByDesc('created_at')->paginate($limit);
    }

}