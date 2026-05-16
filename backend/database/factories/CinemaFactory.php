<?php

namespace Database\Factories;

use App\Models\CinemaChain;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cinema>
 */
class CinemaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'cinema_chain_id' => CinemaChain::factory(),
            'name' => fake()->unique()->company() . ' Cineplex',
            'address' => fake()->streetAddress(),
        ];
    }
}
