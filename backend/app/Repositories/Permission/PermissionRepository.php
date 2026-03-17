<?php
namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface {
    public function __construct(Permission $model)
    {
        $this->model = $model;
    }
}