<?php
namespace App\Repositories\Combo;

use App\Repositories\Base\BaseRepository;
use App\Repositories\Combo\ComboRepositoryInterface;
use App\Models\Combo;

class ComboRepository extends BaseRepository implements ComboRepositoryInterface 
{
    public function __construct(Combo $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q, $cinema, $status)
    {
        $combos = $this->model->with('cinema:id,name,address')->when($q, fn ($query) => $query->where('name', 'like', "%$q%"))
            ->when($cinema, fn ($query) => $query->where('cinema_id', $cinema))
            ->when($status, fn ($query) => $query->where('status', $status));

        return $combos->orderByDesc('created_at')->paginate($limit);
    }

}