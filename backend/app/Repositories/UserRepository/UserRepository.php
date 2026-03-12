<?php 
namespace App\Repositories\UserRepository;

use App\Repositories\Base\BaseRepository;
use App\Repositories\BaseRepositoryInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getByToken(string $token, array $columns = ['*']) {
        return $this->model->where('email_verify_token', $token)
                ->first($columns);
    }

    public function getByEmail(string $email, array $columns = ['*']) {
        return $this->model->where('email', $email)
                ->first($columns);
    }
}