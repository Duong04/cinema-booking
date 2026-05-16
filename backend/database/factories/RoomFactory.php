<?php

namespace Database\Factories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cinema_id' => Cinema::factory(),
            'name' => 'Phong ' . fake()->unique()->numberBetween(1, 99),
            'type' => fake()->randomElement(['2D', '3D', 'IMAX', '4DX', 'VIP']),
        ];
    }
}
