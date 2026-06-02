<?php

namespace App\Console\Commands;

use App\Services\SeatHoldService;
use Illuminate\Console\Command;

class CleanupExpiredSeatHolds extends Command
{
    protected $signature = 'seat-holds:cleanup {--showtime= : Only cleanup holds for a specific showtime ID}';

    protected $description = 'Delete expired seat holds.';

    public function handle(SeatHoldService $seatHoldService): int
    {
        $showtimeId = $this->option('showtime') ?: null;
        $deleted = $seatHoldService->deleteExpired($showtimeId);

        $this->info("Deleted {$deleted} expired seat holds.");

        return self::SUCCESS;
    }
}
