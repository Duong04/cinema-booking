<?php 
namespace App\Repositories\City;

use App\Repositories\Base\BaseRepository;
use App\Models\City;
use App\Repositories\City\CityRepositoryInterface;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $cities = $this->model->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $cities->orderByDesc('created_at')->paginate($limit);
    }


}