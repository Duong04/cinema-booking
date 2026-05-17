<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PROMO##??')),
            'description' => fake()->sentence(10),
            'discount_type' => fake()->randomElement(['percentage', 'fixed_amount']),
            'discount_value' => fake()->randomElement([10, 15, 20, 50000, 80000]),
            'start_date' => now()->subDays(7),
            'end_date' => now()->addDays(30),
            'usage_limit' => fake()->numberBetween(100, 1000),
            'per_user_limit' => fake()->numberBetween(1, 3),
            'applicable_to' => fake()->randomElement(['booking', 'ticket', 'combo']),
            'status' => 'active',
        ];
    }
}
