<?php
namespace App\Repositories\PromotionUsage;

use App\Repositories\Base\BaseRepositoryInterface;

interface PromotionUsageRepositoryInterface extends BaseRepositoryInterface
{
    public function countByPromotion(string $promotionId);

    public function countByPromotionAndUser(string $promotionId, string $userId);
}
