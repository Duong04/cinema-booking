<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuidV7;

class Booking extends Model
{
    use HasFactory, UsesUuidV7;
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
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'BK-' . now()->format('Ymd') . '-' . strtoupper(substr($booking->id, -6));
            }
        });
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
        return $this->belongsToMany(Combo::class, 'booking_combos', 'booking_id', 'combo_id')
            ->withPivot(['combo_name', 'quantity', 'unit_price', 'total_price'])
            ->withTimestamps();
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_usages')
            ->withPivot(['discount_amount', 'used_at']);
    }

    public function promotionUsages()
    {
        return $this->hasMany(PromotionUsage::class);
    }
}
