<?php 
namespace App\Repositories\User;

use App\Repositories\Base\BaseRepositoryInterface;
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getByToken(string $token,  array $columns = ['*']);

    public function getByEmail(string $email,  array $columns = ['*']);
}