<?php 
namespace App\Services;

use App\Repositories\Action\ActionRepositoryInterface;
use App\Repositories\PermissionAction\PermissionActionRepositoryInterface;

class ActionService {

    public function __construct(
        private ActionRepositoryInterface $actionRepository, 
        private PermissionActionRepositoryInterface $permissionActionRepository
    ) {
    }

    public function paginate($limit) {
        $actions = $this->actionRepository->all($limit);

        return $actions;
    }

    public function create($data) {
        $action = $this->actionRepository->create($this->actionPayload($data));

        $rows = $this->mapActionPermissions($data, $action->id);

        if (! empty($rows)) {
            $this->permissionActionRepository->insert($rows);
        }

        return $action->fresh('permissions');
    }

    public function find($id) {
        $action = $this->actionRepository->find($id, ['*'], ['permissions']);

        return $action;
    }

    public function update($id, $data) {
        $action = $this->actionRepository->update($id, $this->actionPayload($data));

        if (isset($data['permissions'])) {
            $this->permissionActionRepository->deleteByCol('action_id', $id);

            $rows = $this->mapActionPermissions($data, $id);

            if (! empty($rows)) {
                $this->permissionActionRepository->insert($rows);
            }
        }

        return $action->fresh('permissions');
    }

    public function delete($id) {
        return $this->actionRepository->delete($id);
    }

    private function actionPayload(array $data): array
    {
        return collect($data)
            ->only(['name', 'key'])
            ->toArray();
    }

    private function mapActionPermissions(array $data, string $actionId): array
    {
        if (empty($data['permissions'])) {
            return [];
        }

        return collect($data['permissions'])
            ->unique('permission_id')
            ->map(fn($item) => [
                'action_id' => $actionId,
                'permission_id' => $item['permission_id'],
            ])
            ->values()
            ->toArray();
    }

}
