<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Showtime>
 */
class ShowtimeFactory extends Factory
{
    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('-3 days', '+14 days'))->setMinute(0)->setSecond(0);

        return [
            'movie_id' => Movie::factory(),
            'room_id' => Room::factory(),
            'show_date' => $start->toDateString(),
            'start_time' => $start,
            'end_time' => $start->copy()->addMinutes(130),
            'base_price' => fake()->randomElement([85000, 95000, 105000, 115000]),
            'status' => $start->isPast() ? 'completed' : 'scheduled',
        ];
    }
}
