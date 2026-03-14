<?php 
namespace App\Services;

use App\Repositories\Action\ActionRepositoryInterface;

class ActionService {
    private $actionRepository;

    public function __construct(ActionRepositoryInterface $actionRepository) {
        $this->actionRepository = $actionRepository;
    }

    public function paginate($limit) {
        $actions = $this->actionRepository->all($limit);

        return $actions;
    }

    public function create($date) {
        $action = $this->actionRepository->create($date);

        return $action;
    }

    public function find($id) {
        $action = $this->actionRepository->find($id);

        return $action;
    }

    public function update($id, $data) {
        $action = $this->actionRepository->update($id, $data);

        return $action;
    }

    public function delete($id) {
        return $this->actionRepository->delete($id);
    }

}