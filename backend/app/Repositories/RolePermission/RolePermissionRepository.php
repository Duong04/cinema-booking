<?php
namespace App\Repositories\RolePermission;

use App\Models\RolePermission;
use App\Repositories\RolePermission\RolePermissionRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class RolePermissionRepository extends BaseRepository implements RolePermissionRepositoryInterface {
    public function __construct(RolePermission $model)
    {
        $this->model = $model;
    }

    public function deleteByCol($col, $id) {
        return $this->model->where($col, $id)->delete();
    }

    public function insert(array $data) {
        return $this->model->insert($data);
    }
}