<?php
namespace App\Repositories\Booking;

use App\Repositories\Base\BaseRepository;
use App\Models\Booking;
use App\Repositories\Booking\BookingRepositoryInterface;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        $this->model = $model;
    }

    public function paginate(int $limit, ?string $q, ?string $status, ?string $fromDate, ?string $toDate)
    {
        $bookings = $this->model
            ->with([
                'user:id,name,email,avatar',
                'showtime.movie',
                'showtime.room.cinema',
                'items',
                'combos',
                'promotions',
                'payment',
            ])
            ->when(
                $q,
                fn($query) => $query->where(
                    fn($query) => $query
                        ->where('booking_code', 'like', "%{$q}%")
                        ->orWhereHas(
                            'user',
                            fn($query) => $query->where('name', 'like', "%{$q}%")
                        )
                        ->orWhereHas(
                            'showtime.movie',
                            fn($query) => $query->where('title', 'like', "%{$q}%")
                        )
                )
            )
            ->when(
                $status,
                fn($query) => $query->where('status', $status)
            )
            ->when($fromDate, fn($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($query) => $query->whereDate('created_at', '<=', $toDate));

        return $bookings
            ->orderByDesc('created_at')
            ->paginate($limit);
    }
}
