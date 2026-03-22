<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use UsesUuidV7;

    protected $table = 'seats';

    protected $fillable = [
        'room_id',
        'base_multiplier',
        'seat_type_id',
        'row_label',
        'seat_number',
    ];

    public function seatType() {
        return $this->belongsTo(SeatType::class);
    }
}
