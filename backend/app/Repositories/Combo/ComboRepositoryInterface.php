<?php
namespace App\Repositories\Combo;

use App\Repositories\Base\BaseRepositoryInterface;

interface ComboRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q, $cinema);
}