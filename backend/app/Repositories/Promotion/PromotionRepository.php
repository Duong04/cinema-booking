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

    public function paginate($limit = 15, $q = null, $status = null, $applicableTo = null)
    {
        $promotions = $this->model
            ->when($q, fn ($query) => $query->where('code', 'like', "%$q%"))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($applicableTo, fn ($query) => $query->where('applicable_to', $applicableTo));

        return $promotions->orderByDesc('created_at')->paginate($limit);
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
