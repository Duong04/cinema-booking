<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tier' => fake()->randomElement(['bronze', 'silver', 'gold', 'platinum']),
            'points' => fake()->numberBetween(0, 10000),
        ];
    }
}
