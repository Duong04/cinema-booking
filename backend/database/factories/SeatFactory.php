<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\SeatType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'seat_type_id' => SeatType::factory(),
            'row_label' => fake()->randomElement(range('A', 'H')),
            'seat_number' => fake()->numberBetween(1, 16),
        ];
    }
}
