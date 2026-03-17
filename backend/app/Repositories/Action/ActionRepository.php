<?php
namespace App\Repositories\Action;

use App\Models\Action;
use App\Repositories\Action\ActionRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class ActionRepository extends BaseRepository implements ActionRepositoryInterface {
    public function __construct(Action $model)
    {
        $this->model = $model;
    }
}