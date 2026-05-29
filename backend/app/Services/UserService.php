<?php 
namespace App\Services;

use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserService {

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private MembershipService $membershipService
    ) {
    }

    public function paginate($limit, $q, $roleId, $ignoreRoleId, $isActive) {
        $users = $this->userRepository->paginate($limit, $q, $roleId, $ignoreRoleId, $isActive);

        return $users;
    }

    public function create($data) {
        $user = DB::transaction(function () use ($data) {
            $user = $this->userRepository->create($data);
            $this->createMembershipIfCustomer($user->id, $data['role_id'] ?? null);

            return $user;
        });

        return $user;
    }

    public function find($id) {
        $user = $this->userRepository->findWithMembershipStats($id);

        return $user;
    }

    public function update($id, $data) {
        $user = DB::transaction(function () use ($id, $data) {
            $user = $this->userRepository->update($id, $data);
            $this->createMembershipIfCustomer($user->id, $data['role_id'] ?? $user->role_id);

            return $user;
        });

        return $user;
    }

    private function createMembershipIfCustomer(string $userId, ?string $roleId): void
    {
        if (! $roleId) {
            return;
        }

        $role = $this->roleRepository->find($roleId);

        if (strtolower($role->name) === 'customer') {
            $this->membershipService->createDefaultForUser($userId);
        }
    }
}
