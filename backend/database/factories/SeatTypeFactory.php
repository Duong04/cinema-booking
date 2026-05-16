<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeatType>
 */
class SeatTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Seat Type ' . fake()->unique()->bothify('??##'),
            'base_multiplier' => fake()->randomFloat(2, 1, 2.5),
        ];
    }
}
