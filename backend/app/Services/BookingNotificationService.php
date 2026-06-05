<?php

namespace App\Services;

use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingNotificationService
{
    public function sendConfirmedTicket(Booking $booking): void
    {
        $booking->loadMissing('user:id,name,email');

        if (! $booking->user?->email) {
            return;
        }

        try {
            Mail::to($booking->user->email)->queue(new BookingConfirmedMail($booking));
        } catch (\Throwable $e) {
            Log::warning('Failed to queue booking confirmed mail.', [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'email' => $booking->user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
