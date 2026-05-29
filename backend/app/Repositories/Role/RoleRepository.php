<?php 
namespace App\Repositories\Role;

use App\Repositories\Base\BaseRepository;
use App\Models\Role;
use App\Repositories\Role\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $roles = $this->model
            ->with([
                'permissions.roleActions',
                'users:id,role_id,name,avatar',
            ])
            ->withCount('users')
            ->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $roles->orderByDesc('created_at')->paginate($limit);
    }


}
