<?php
namespace App\Repositories\PermissionAction;

use App\Models\PermissionAction;
use App\Repositories\PermissionAction\PermissionActionRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class PermissionActionRepository extends BaseRepository implements PermissionActionRepositoryInterface {
    public function __construct(PermissionAction $model)
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
