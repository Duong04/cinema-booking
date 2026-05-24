<?php
namespace App\Repositories\PromotionUsage;

use App\Models\PromotionUsage;
use App\Repositories\Base\BaseRepository;

class PromotionUsageRepository extends BaseRepository implements PromotionUsageRepositoryInterface
{
    public function __construct(PromotionUsage $model)
    {
        $this->model = $model;
    }

    public function countByPromotion(string $promotionId): int
    {
        return $this->model->where('promotion_id', $promotionId)->count();
    }

    public function countByPromotionAndUser(string $promotionId, string $userId): int
    {
        return $this->model
            ->where('promotion_id', $promotionId)
            ->where('user_id', $userId)
            ->count();
    }
}
