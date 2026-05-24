<?php
namespace App\Repositories\BookingCombo;

use App\Models\BookingCombo;
use App\Repositories\Base\BaseRepository;

class BookingComboRepository extends BaseRepository implements BookingComboRepositoryInterface
{
    public function __construct(BookingCombo $model)
    {
        $this->model = $model;
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }
}
