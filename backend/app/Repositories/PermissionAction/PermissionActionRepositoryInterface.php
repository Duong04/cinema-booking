<?php
namespace App\Repositories\PermissionAction;

use App\Repositories\Base\BaseRepositoryInterface;

interface PermissionActionRepositoryInterface extends BaseRepositoryInterface {
    public function deleteByCol($col, $id);
}