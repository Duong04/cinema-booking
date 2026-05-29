<?php
namespace App\Services;

use App\Models\Booking;
use App\Repositories\Membership\MembershipRepositoryInterface;

class MembershipService
{
    private const AMOUNT_PER_POINT = 10000;

    private const TIER_THRESHOLDS = [
        'platinum' => 6000,
        'gold' => 3000,
        'silver' => 1000,
        'bronze' => 0,
    ];

    public function __construct(
        private MembershipRepositoryInterface $membershipRepository
    ) {
    }

    public function createDefaultForUser(string $userId)
    {
        return $this->membershipRepository->firstOrCreateForUser($userId, [
            'tier' => 'bronze',
            'points' => 0,
        ]);
    }

    public function addPointsForConfirmedBooking(Booking $booking)
    {
        $earnedPoints = $this->calculatePoints($booking->total_amount);

        $membership = $this->membershipRepository->findByUserIdForUpdate($booking->user_id);

        if (! $membership) {
            $membership = $this->createDefaultForUser($booking->user_id);
        }

        $membership->points += $earnedPoints;
        $membership->tier = $this->tierForPoints($membership->points);
        $membership->save();

        return $membership;
    }

    public function calculatePoints(float|int|string $amount): int
    {
        return max((int) floor((float) $amount / self::AMOUNT_PER_POINT), 0);
    }

    public function tierForPoints(int $points): string
    {
        foreach (self::TIER_THRESHOLDS as $tier => $threshold) {
            if ($points >= $threshold) {
                return $tier;
            }
        }

        return 'bronze';
    }
}
