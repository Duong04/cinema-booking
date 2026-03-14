<?php 
namespace App\Services;

use App\Repositories\Permission\PermissionRepositoryInterface;

class PermissionService {
    private $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository) {
        $this->permissionRepository = $permissionRepository;
    }

    public function paginate($limit) {
        $permissions = $this->permissionRepository->all($limit);

        return $permissions;
    }

    public function create($date) {
        $permission = $this->permissionRepository->create($date);

        return $permission;
    }

    public function find($id) {
        $permission = $this->permissionRepository->find($id);

        return $permission;
    }

    public function update($id, $data) {
        $permission = $this->permissionRepository->update($id, $data);

        return $permission;
    }

    public function delete($id) {
        return $this->permissionRepository->delete($id);
    }

}