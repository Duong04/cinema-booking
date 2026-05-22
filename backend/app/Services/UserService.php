<?php 
namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;

class UserService {
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function paginate($limit, $q, $roleId, $ignoreRoleId, $isActive) {
        $users = $this->userRepository->paginate($limit, $q, $roleId, $ignoreRoleId, $isActive);

        return $users;
    }

    public function create($data) {
        $user = $this->userRepository->create($data);

        return $user;
    }

    public function find($id) {
        $user = $this->userRepository->find($id);

        return $user;
    }

    public function update($id, $data) {
        $user = $this->userRepository->update($id, $data);

        return $user;
    }
}