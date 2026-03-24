<?php 
namespace App\Repositories\SeatType;

use App\Repositories\Base\BaseRepository;
use App\Models\SeatType;
use App\Repositories\SeatType\SeatTypeRepositoryInterface;

class SeatTypeRepository extends BaseRepository implements SeatTypeRepositoryInterface
{
    public function __construct(SeatType $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $seatTypes = $this->model->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $seatTypes->orderByDesc('created_at')->paginate($limit);
    }

}