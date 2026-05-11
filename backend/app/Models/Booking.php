<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

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
}
