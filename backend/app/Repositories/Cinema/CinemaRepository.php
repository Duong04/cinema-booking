<?php 
namespace App\Repositories\Cinema;

use App\Repositories\Base\BaseRepository;
use App\Models\Cinema;
use App\Repositories\Cinema\CinemaRepositoryInterface;

class CinemaRepository extends BaseRepository implements CinemaRepositoryInterface
{
    public function __construct(Cinema $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $cinemas = $this->model
                ->with(['city:id,name', 'cinemaChain:id,name,logo'])
                ->when($q, fn ($query) => $query
                    ->where('name', 'like', "%$q%")
                    ->orWhere('address', 'like', "%$q%")
                );

        return $cinemas->orderByDesc('created_at')->paginate($limit);
    }

}