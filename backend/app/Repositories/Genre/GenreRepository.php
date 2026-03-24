<?php 
namespace App\Repositories\Genre;

use App\Repositories\Base\BaseRepository;
use App\Models\Genre;
use App\Repositories\Genre\GenreRepositoryInterface;

class GenreRepository extends BaseRepository implements GenreRepositoryInterface
{
    public function __construct(Genre $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $genres = $this->model->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $genres->orderByDesc('created_at')->paginate($limit);
    }

}