<?php 
namespace App\Services;

use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\PermissionAction\PermissionActionRepositoryInterface;

class PermissionService {
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository,
        private PermissionActionRepositoryInterface $permissionActionRepository
    ) {
    }

    public function paginate($limit) {
        $permissions = $this->permissionRepository->all($limit, ['actions']);

        return $permissions;
    }

    public function create($data) {
        $permission = $this->permissionRepository->create($this->permissionPayload($data));

        $rows = $this->mapPermissionActions($data, $permission->id);

        if (! empty($rows)) {
            $this->permissionActionRepository->insert($rows);
        }

        return $permission->fresh('actions');
    }

    public function find($id) {
        $permission = $this->permissionRepository->find($id, ['*'], ['actions']);

        return $permission;
    }

    public function update($id, $data) {
        $permission = $this->permissionRepository->update($id, $this->permissionPayload($data));

        if (isset($data['actions'])) {
            $this->permissionActionRepository->deleteByCol('permission_id', $id);

            $rows = $this->mapPermissionActions($data, $id);

            if (! empty($rows)) {
                $this->permissionActionRepository->insert($rows);
            }
        }

        return $permission->fresh('actions');
    }

    public function delete($id) {
        return $this->permissionRepository->delete($id);
    }

    private function permissionPayload(array $data): array
    {
        return collect($data)
            ->only(['name', 'key'])
            ->toArray();
    }

    private function mapPermissionActions(array $data, string $permissionId): array
    {
        if (empty($data['actions'])) {
            return [];
        }

        return collect($data['actions'])
            ->unique('action_id')
            ->map(fn($item) => [
                'permission_id' => $permissionId,
                'action_id' => $item['action_id'],
            ])
            ->values()
            ->toArray();
    }

}
