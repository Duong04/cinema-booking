<?php 
namespace App\Services;

use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\PermissionAction\PermissionActionRepositoryInterface;

class PermissionService {
    private $permissionRepository;
    private $permissionActionRepository;
    public function __construct(PermissionRepositoryInterface $permissionRepository, PermissionActionRepositoryInterface $permissionActionRepository) {
        $this->permissionRepository = $permissionRepository;
        $this->permissionActionRepository = $permissionActionRepository;
    }

    public function paginate($limit) {
        $permissions = $this->permissionRepository->all($limit);

        return $permissions;
    }

    public function create($data) {
        $permission = $this->permissionRepository->create($data);

        if (isset($data['actions'])) {
            foreach ($data['actions'] as $item) {
                $this->permissionActionRepository->create([
                    'permission_id' => $permission->id,
                    'action_id' => $item['action_id'],
                ]);
            }
        }
        return $permission;
    }

    public function find($id) {
        $permission = $this->permissionRepository->find($id);

        return $permission;
    }

    public function update($id, $data) {
        $permission = $this->permissionRepository->update($id, $data);

        $permission = $this->permissionRepository->update($id, $data);
        if (isset($data['actions'])) {
            $this->permissionActionRepository->deleteByCol('permission_id', $id);
            foreach ($data['actions'] as $item) {
                $this->permissionActionRepository->create([
                    'permission_id' => $id,
                    'action_id' => $item['action_id'],
                ]);
            }
        }

        return $permission;
    }

    public function delete($id) {
        return $this->permissionRepository->delete($id);
    }

}