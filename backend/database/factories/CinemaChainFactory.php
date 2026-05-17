<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CinemaChain>
 */
class CinemaChainFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company() . ' Cinema';

        return [
            'name' => $name,
            'logo' => 'https://placehold.co/360x160/222222/ffffff.png?text=' . urlencode($name),
        ];
    }
}
