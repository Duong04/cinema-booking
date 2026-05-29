<?php
namespace App\Repositories\BookingItem;

use App\Models\BookingItem;
use App\Repositories\BookingItem\BookingItemRepositoryInterface;
use App\Repositories\Base\BaseRepository;

class BookingItemRepository extends BaseRepository implements BookingItemRepositoryInterface {
    public function __construct(BookingItem $model)
    {
        $this->model = $model;
    }

    public function insert(array $data) {
        return $this->model->insert($data);
    }

    public function getBookedSeatsByShowtime(array $seatIds, string $showtimeId)
    {
        return $this->model->with(['booking:id,user_id,showtime_id,booking_code,status,total_amount', 'booking.user:id,name,email'])
            ->whereIn('seat_id', $seatIds)
            ->whereHas(
                'booking',
                fn($query) => $query
                    ->where('showtime_id', $showtimeId)
                    ->whereIn('status', ['pending', 'confirmed'])
            )
            ->get()
            ->keyBy('seat_id');
    }
}
