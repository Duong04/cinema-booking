<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Action>
 */
class ActionFactory extends Factory
{
    public function definition(): array
    {
        $name = 'action-' . fake()->unique()->bothify('???-###');

        return [
            'name' => Str::headline($name),
            'key' => $name,
        ];
    }
}
