<?php 
namespace App\Services;

use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\RolePermission\RolePermissionRepositoryInterface;

class RoleService {
    private $roleRepository;
    private $rolePermissionRepository;

    public function __construct(RoleRepositoryInterface $roleRepository, RolePermissionRepositoryInterface $rolePermissionRepository) {
        $this->roleRepository = $roleRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function paginate($limit, $q) {
        $roles = $this->roleRepository->paginate($limit, $q);

        return $roles;
    }

    public function create($data) {
        $role = $this->roleRepository->create($data);
        $rows = $this->mapRolePermissions($data, $role->id);

        if (!empty($rows)) {
            $this->rolePermissionRepository->insert($rows);
        }

        return $role;
    }

    public function find($id) {
        $role = $this->roleRepository->find($id);

        return $role;
    }

    public function update($id, $data) {
        $role = $this->roleRepository->update($id, $data);

        if (isset($data['permissions'])) {
            $this->rolePermissionRepository->deleteByCol('role_id', $id);
            $rows = $this->mapRolePermissions($data, $id);

            if (!empty($rows)) {
                $this->rolePermissionRepository->insert($rows);
            }
        }
        return $role;
    }

    public function delete($id) {
        return $this->roleRepository->delete($id);
    }

    private function mapRolePermissions(array $data, $roleId): array
    {
        $rows = [];

        if (empty($data['permissions'])) {
            return $rows;
        }

        foreach ($data['permissions'] as $permission) {

            if (empty($permission['actions'])) {
                continue;
            }

            foreach ($permission['actions'] as $action) {
                $rows[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permission['id'],
                    'action_id' => $action['id'],
                ];
            }
        }

        return $rows;
    }

}