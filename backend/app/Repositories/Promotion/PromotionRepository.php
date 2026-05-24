<?php
namespace App\Repositories\Promotion;

use App\Models\Promotion;
use App\Repositories\Base\BaseRepository;

class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    public function __construct(Promotion $model)
    {
        $this->model = $model;
    }

    public function findActiveByCodeForUpdate(string $code)
    {
        return $this->model
            ->where('code', $code)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->lockForUpdate()
            ->first();
    }
}
