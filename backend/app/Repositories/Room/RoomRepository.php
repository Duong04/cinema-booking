<?php 
namespace App\Repositories\Room;

use App\Repositories\Base\BaseRepository;
use App\Models\Room;
use App\Repositories\Room\RoomRepositoryInterface;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    public function __construct(Room $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $roles = $this->model->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $roles->orderByDesc('created_at')->paginate($limit);
    }


}