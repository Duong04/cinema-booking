<?php 
namespace App\Services;

use App\Repositories\Action\ActionRepositoryInterface;
use App\Repositories\PermissionAction\PermissionActionRepositoryInterface;

class ActionService {
    private $actionRepository;
    private $permissionActionRepository;

    public function __construct(ActionRepositoryInterface $actionRepository, PermissionActionRepositoryInterface $permissionActionRepository) {
        $this->actionRepository = $actionRepository;
        $this->permissionActionRepository = $permissionActionRepository;
    }

    public function paginate($limit) {
        $actions = $this->actionRepository->all($limit);

        return $actions;
    }

    public function create($data) {
        $action = $this->actionRepository->create($data);

        if (isset($data['permissions'])) {
            foreach ($data['permissions'] as $item) {
                $this->permissionActionRepository->create([
                    'action_id' => $action->id,
                    'permission_id' => $item['permission_id'],
                ]);
            }
        }

        return $action;
    }

    public function find($id) {
        $action = $this->actionRepository->find($id);

        return $action;
    }

    public function update($id, $data) {
        $action = $this->actionRepository->update($id, $data);

        if (isset($data['permissions'])) {
            $this->permissionActionRepository->deleteByCol('action_id', $id);
            foreach ($data['permissions'] as $item) {
                $this->permissionActionRepository->create([
                    'action_id' => $id,
                    'permission_id' => $item['permission_id'],
                ]);
            }
        }

        return $action;
    }

    public function delete($id) {
        return $this->actionRepository->delete($id);
    }

}