<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingItem>
 */
class BookingItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'seat_id' => Seat::factory(),
            'price' => fake()->randomElement([85000, 115000, 145000, 190000]),
            'seat_type_name' => fake()->randomElement(['Standard', 'VIP', 'Couple', 'Sweetbox']),
            'movie_title' => fake()->sentence(3),
            'room_name' => 'Phong ' . fake()->numberBetween(1, 12),
            'seat_label' => fake()->randomElement(range('A', 'H')) . fake()->numberBetween(1, 12),
        ];
    }
}
