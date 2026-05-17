<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuidV7;

class SeatHold extends Model
{
    use HasFactory, UsesUuidV7;

    protected $table = 'seat_holds';
    protected $fillable = [
        'user_id',
        'showtime_id',
        'seat_id',
        'expired_at'
    ];

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
