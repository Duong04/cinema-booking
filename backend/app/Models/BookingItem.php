<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class BookingItem extends Model
{
    use UsesUuidV7; 
    protected $table = 'booking_items';
    protected $fillable = [
        'booking_id',
        'seat_id',
        'price',
        'seat_type_name',
        'movie_title',
        'room_name',
        'seat_label',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
