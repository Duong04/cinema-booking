<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $provider = fake()->randomElement(['vnpay', 'momo', 'zalopay', 'cashier']);

        return [
            'booking_id' => Booking::factory(),
            'provider' => $provider,
            'transaction_code' => strtoupper($provider) . '-' . fake()->unique()->numerify('########'),
            'amount' => fake()->randomElement([180000, 260000, 350000, 480000]),
            'status' => 'pending',
            'idempotency_key' => 'idem-' . Str::uuid(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'paid_at' => now()->subDays(1),
            'refunded_amount' => $attributes['amount'] ?? 0,
            'refund_status' => 'completed',
        ]);
    }
}
