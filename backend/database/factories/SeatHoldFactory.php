<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeatHold>
 */
class SeatHoldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'showtime_id' => Showtime::factory(),
            'seat_id' => Seat::factory(),
            'expired_at' => now()->addMinutes(10),
        ];
    }
}
