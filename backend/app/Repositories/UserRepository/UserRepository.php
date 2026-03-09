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
}