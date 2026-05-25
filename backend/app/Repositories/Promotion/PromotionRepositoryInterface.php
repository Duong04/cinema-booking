<?php
namespace App\Repositories\Promotion;

use App\Repositories\Base\BaseRepositoryInterface;

interface PromotionRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q = null, $status = null, $applicableTo = null);

    public function findActiveByCodeForUpdate(string $code);
}
