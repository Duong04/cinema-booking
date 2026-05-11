<?php
namespace App\Services;

use App\Repositories\Booking\BookingRepositoryInterface;

class BookingService
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

}