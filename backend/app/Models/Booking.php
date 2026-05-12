<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;
use Illuminate\Support\Str;

class Booking extends Model
{
    use UsesUuidV7; 
    protected $table = 'bookings';
    protected $fillable = [
        'user_id',
        'showtime_id',
        'booking_code',
        'total_amount',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'expired_at',
        'confirmed_at'
    ];

    protected static function booted(): void
    {
        static::creating(function ($booking) {
            $booking->booking_code = self::generateBookingCode();
        });
    }

    private static function generateBookingCode(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('booking_code', $code)->exists());

        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'booking_combos')->withPivot('quantity');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_usages');
    }
}
