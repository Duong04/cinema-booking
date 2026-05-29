<?php
namespace App\Repositories\Membership;

use App\Models\Membership;
use App\Repositories\Base\BaseRepository;

class MembershipRepository extends BaseRepository implements MembershipRepositoryInterface
{
    public function __construct(Membership $model)
    {
        $this->model = $model;
    }

    public function firstOrCreateForUser(string $userId, array $defaults = [])
    {
        return $this->model->firstOrCreate(
            ['user_id' => $userId],
            $defaults
        );
    }

    public function findByUserIdForUpdate(string $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();
    }
}
