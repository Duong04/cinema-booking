<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['created', 'updated', 'approved', 'cancelled']),
            'entity_type' => fake()->randomElement(['movies', 'showtimes', 'bookings', 'promotions']),
            'entity_id' => (string) Str::uuid7(),
            'metadata' => json_encode(['ip' => fake()->ipv4(), 'user_agent' => fake()->userAgent()]),
        ];
    }
}
