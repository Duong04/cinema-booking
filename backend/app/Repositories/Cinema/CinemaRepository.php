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

    public function paginate($limit = 15, $q, $cityId, $cinemaChainId)
    {
        $cinemas = $this->model
            ->with(['city:id,name', 'cinemaChain:id,name,logo'])
            ->when(
                $q,
                fn($query) => $query
                    ->where('name', 'like', "%$q%")
                    ->orWhere('address', 'like', "%$q%")
            )
            ->when($cityId, fn($query) => $query->where('city_id', $cityId))
            ->when($cinemaChainId, fn($query) => $query->where('cinema_chain_id', $cinemaChainId));

        return $cinemas->orderByDesc('created_at')->paginate($limit);
    }

}