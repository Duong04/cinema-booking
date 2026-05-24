<?php
namespace App\Repositories\Payment;

use App\Models\Payment;
use App\Repositories\Base\BaseRepository;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function findByBookingId(string $bookingId)
    {
        return $this->model
            ->with(['booking.showtime.movie', 'booking.showtime.room.cinema', 'booking.items', 'booking.combos'])
            ->where('booking_id', $bookingId)
            ->latest()
            ->first();
    }
}
