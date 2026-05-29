<?php

namespace Tests\Unit;

use App\Services\MembershipService;
use App\Repositories\Membership\MembershipRepositoryInterface;
use PHPUnit\Framework\TestCase;

class MembershipServiceTest extends TestCase
{
    public function test_it_calculates_points_from_booking_amount(): void
    {
        $service = $this->makeService();

        $this->assertSame(0, $service->calculatePoints(9999));
        $this->assertSame(1, $service->calculatePoints(10000));
        $this->assertSame(18, $service->calculatePoints(180000));
    }

    public function test_it_calculates_tier_from_points(): void
    {
        $service = $this->makeService();

        $this->assertSame('bronze', $service->tierForPoints(999));
        $this->assertSame('silver', $service->tierForPoints(1000));
        $this->assertSame('gold', $service->tierForPoints(3000));
        $this->assertSame('platinum', $service->tierForPoints(6000));
    }

    private function makeService(): MembershipService
    {
        return new MembershipService($this->createMock(MembershipRepositoryInterface::class));
    }
}
