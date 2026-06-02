<?php
namespace App\Repositories\Payment;

use App\Repositories\Base\BaseRepositoryInterface;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate(int $limit, ?string $q, ?string $status, ?string $provider, ?string $fromDate, ?string $toDate);
    public function findByBookingId(string $bookingId);
}
