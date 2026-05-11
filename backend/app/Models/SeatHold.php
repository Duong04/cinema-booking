<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class SeatHold extends Model
{
    use UsesUuidV7;

    protected $table = 'seat_holds';
    protected $fillable = [
        'user_id',
        'showtime_id',
        'seat_id',
        'expired_at'
    ];
}
