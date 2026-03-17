<?php
namespace App\Repositories\RolePermission;

use App\Repositories\Base\BaseRepositoryInterface;

interface RolePermissionRepositoryInterface extends BaseRepositoryInterface {
    public function deleteByCol($col, $id);
    public function insert(array $data);
}