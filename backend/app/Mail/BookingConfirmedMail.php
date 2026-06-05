<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $ticket;
    public string $qrUrl;

    public function __construct(Booking $booking)
    {
        $booking->loadMissing([
            'user:id,name,email',
            'showtime.movie',
            'showtime.room.cinema',
            'items',
            'combos',
            'payment',
        ]);

        $showtime = $booking->showtime;
        $room = $showtime?->room;
        $cinema = $room?->cinema;
        $movie = $showtime?->movie;

        $this->ticket = [
            'booking_code' => $booking->booking_code,
            'customer_name' => $booking->user?->name ?? 'Customer',
            'customer_email' => $booking->user?->email,
            'movie_title' => $movie?->title ?? '-',
            'movie_poster' => $movie?->poster_url,
            'showtime' => $showtime?->start_time,
            'show_date' => $showtime?->show_date,
            'cinema_name' => $cinema?->name ?? '-',
            'cinema_address' => $cinema?->address ?? '-',
            'room_name' => $room?->name ?? '-',
            'seats' => $booking->items->pluck('seat_label')->values()->all(),
            'ticket_count' => $booking->items->count(),
            'combos' => $booking->combos->map(fn ($combo) => [
                'name' => $combo->pivot?->combo_name ?? $combo->name,
                'quantity' => (int) ($combo->pivot?->quantity ?? 1),
                'total_price' => (float) ($combo->pivot?->total_price ?? 0),
            ])->values()->all(),
            'payment_provider' => $booking->payment?->provider,
            'payment_code' => $booking->payment?->transaction_code,
            'paid_at' => $booking->payment?->paid_at,
            'total_amount' => (float) $booking->total_amount,
        ];

        $qrPayload = [
            'booking_code' => $this->ticket['booking_code'],
            'movie' => $this->ticket['movie_title'],
            'showtime' => $this->ticket['showtime'],
            'cinema' => $this->ticket['cinema_name'],
            'room' => $this->ticket['room_name'],
            'seats' => $this->ticket['seats'],
            'amount' => $this->ticket['total_amount'],
        ];

        $this->qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data='
            . rawurlencode(json_encode($qrPayload, JSON_UNESCAPED_UNICODE));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Vé xem phim của bạn - ' . $this->ticket['booking_code'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
