<?php
namespace App\Repositories\Membership;

use App\Repositories\Base\BaseRepositoryInterface;

interface MembershipRepositoryInterface extends BaseRepositoryInterface
{
    public function firstOrCreateForUser(string $userId, array $defaults = []);
    public function findByUserIdForUpdate(string $userId);
}
