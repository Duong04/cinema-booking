<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => Str::headline(rtrim($title, '.')),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'duration_minutes' => fake()->numberBetween(85, 180),
            'poster_url' => 'https://picsum.photos/seed/movie-' . fake()->unique()->uuid() . '/500/750',
            'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => fake()->sentence(16),
            'content' => fake()->paragraph(4),
            'release_date' => fake()->dateTimeBetween('-2 years', '+8 months')->format('Y-m-d'),
            'rating' => fake()->randomElement(['P', 'T13', 'T16', 'T18']),
            'language' => fake()->randomElement(['Vietnamese', 'English', 'Korean', 'Japanese']),
            'status' => fake()->randomElement(['coming_soon', 'now_showing', 'ended']),
        ];
    }

    public function nowShowing(): static
    {
        return $this->state(fn () => ['status' => 'now_showing']);
    }

    public function comingSoon(): static
    {
        return $this->state(fn () => ['status' => 'coming_soon']);
    }
}
