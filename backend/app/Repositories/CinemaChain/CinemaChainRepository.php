<?php 
namespace App\Repositories\CinemaChain;

use App\Repositories\Base\BaseRepository;
use App\Models\CinemaChain;
use App\Repositories\CinemaChain\CinemaChainRepositoryInterface;

class CinemaChainRepository extends BaseRepository implements CinemaChainRepositoryInterface
{
    public function __construct(CinemaChain $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $roles = $this->model->with(['cinemas:id,name,address,cinema_chain_id,city_id', 'cinemas.city:id,name'])->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $roles->orderByDesc('created_at')->paginate($limit);
    }


}