<?php 
namespace App\Services;

use App\Repositories\Role\RoleRepositoryInterface;

class RoleService {
    private $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository) {
        $this->roleRepository = $roleRepository;
    }

    public function paginate($limit, $q) {
        $roles = $this->roleRepository->paginate($limit, $q);

        return $roles;
    }

    public function create($data) {
        $role = $this->roleRepository->create($data);

        return $role;
    }

    public function find($id) {
        $role = $this->roleRepository->find($id);

        return $role;
    }

    public function update($id, $data) {
        $role = $this->roleRepository->update($id, $data);

        return $role;
    }

    public function delete($id) {
        return $this->roleRepository->delete($id);
    }

}