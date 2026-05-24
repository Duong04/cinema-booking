<?php
namespace App\Repositories\Payment;

use App\Repositories\Base\BaseRepositoryInterface;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function findByBookingId(string $bookingId);
}
