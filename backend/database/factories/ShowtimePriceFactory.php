<?php

namespace Database\Factories;

use App\Models\SeatType;
use App\Models\Showtime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShowtimePrice>
 */
class ShowtimePriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'showtime_id' => Showtime::factory(),
            'seat_type_id' => SeatType::factory(),
            'price' => fake()->randomElement([85000, 95000, 115000, 145000, 190000]),
        ];
    }
}
