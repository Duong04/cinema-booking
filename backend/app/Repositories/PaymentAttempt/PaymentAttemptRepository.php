<?php
namespace App\Repositories\PaymentAttempt;

use App\Models\PaymentAttempt;
use App\Repositories\Base\BaseRepository;

class PaymentAttemptRepository extends BaseRepository implements PaymentAttemptRepositoryInterface
{
    public function __construct(PaymentAttempt $model)
    {
        $this->model = $model;
    }
}
