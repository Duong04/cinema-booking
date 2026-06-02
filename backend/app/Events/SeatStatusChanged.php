<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $showtimeId,
        public array $seatIds,
        public string $status,
        public ?string $userId = null,
        public ?string $expiredAt = null,
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("showtime.{$this->showtimeId}.seats");
    }

    public function broadcastAs(): string
    {
        return 'seat.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'showtime_id' => $this->showtimeId,
            'seat_ids' => $this->seatIds,
            'status' => $this->status,
            'user_id' => $this->userId,
            'expired_at' => $this->expiredAt,
        ];
    }
}
