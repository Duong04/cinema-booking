<?php
namespace App\Repositories\BookingCombo;

use App\Repositories\Base\BaseRepositoryInterface;

interface BookingComboRepositoryInterface extends BaseRepositoryInterface
{
    public function insert(array $data);
}
