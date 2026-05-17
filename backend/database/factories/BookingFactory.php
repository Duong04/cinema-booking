<?php

namespace Database\Factories;

use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'showtime_id' => Showtime::factory(),
            'booking_code' => 'BK' . now()->format('ymd') . fake()->unique()->numerify('#####'),
            'total_amount' => 0,
            'status' => 'pending',
            'expired_at' => now()->addMinutes(15),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
            'cancellation_reason' => 'Khach hang doi lich xem phim.',
            'cancelled_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'expired',
            'expired_at' => now()->subMinutes(5),
        ]);
    }
}
