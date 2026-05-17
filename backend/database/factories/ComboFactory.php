<?php

namespace Database\Factories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Combo>
 */
class ComboFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cinema_id' => Cinema::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(12),
            'price' => fake()->randomElement([89000, 119000, 149000, 199000]),
            'status' => 'active',
            'image' => 'https://loremflickr.com/900/600/popcorn,soda?lock=' . fake()->unique()->numberBetween(1000, 9999),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
