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

    public function paginate(int $limit, ?string $q, ?string $status, ?string $provider, ?string $fromDate, ?string $toDate)
    {
        $payments = $this->model
            ->with([
                'booking:id,user_id,showtime_id,booking_code,total_amount,status,confirmed_at,created_at',
                'booking.user:id,name,email,avatar',
                'booking.showtime.movie:id,title',
                'booking.showtime.room.cinema:id,name',
            ])
            ->when(
                $q,
                fn ($query) => $query->where(
                    fn ($query) => $query
                        ->where('transaction_code', 'like', "%{$q}%")
                        ->orWhereHas(
                            'booking',
                            fn ($query) => $query->where('booking_code', 'like', "%{$q}%")
                        )
                        ->orWhereHas(
                            'booking.user',
                            fn ($query) => $query->where('name', 'like', "%{$q}%")
                        )
                )
            )
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($provider, fn ($query) => $query->where('provider', $provider))
            ->when($fromDate, fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('created_at', '<=', $toDate));

        return $payments
            ->orderByDesc('created_at')
            ->paginate($limit);
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
