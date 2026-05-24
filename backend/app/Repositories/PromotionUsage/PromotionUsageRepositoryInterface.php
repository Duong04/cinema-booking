<?php
namespace App\Repositories\PromotionUsage;

use App\Repositories\Base\BaseRepositoryInterface;

interface PromotionUsageRepositoryInterface extends BaseRepositoryInterface
{
    public function countByPromotion(string $promotionId): int;

    public function countByPromotionAndUser(string $promotionId, string $userId): int;
}
