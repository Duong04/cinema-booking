<?php
namespace App\Repositories\Promotion;

use App\Repositories\Base\BaseRepositoryInterface;

interface PromotionRepositoryInterface extends BaseRepositoryInterface
{
    public function findActiveByCodeForUpdate(string $code);
}
