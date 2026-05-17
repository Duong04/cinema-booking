<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        $name = 'role-' . fake()->unique()->bothify('???-###');

        return [
            'name' => $name,
            'description' => fake()->sentence(),
        ];
    }
}
