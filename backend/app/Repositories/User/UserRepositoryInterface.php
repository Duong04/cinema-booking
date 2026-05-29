<?php 
namespace App\Repositories\User;

use App\Repositories\Base\BaseRepositoryInterface;
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit =  15, $q, $roleId, $ignoreRoleId, $isActive);
    public function getByToken(string $token,  array $columns = ['*']);

    public function getByEmail(string $email,  array $columns = ['*']);

    public function findWithMembershipStats(string $id);
}
