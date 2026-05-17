<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowtimePrice extends Model
{
    use HasFactory, UsesUuidV7;

    protected $table = 'showtime_prices';
    protected $fillable = [
        'showtime_id',
        'seat_type_id',
        'price'
    ];

    public $timestamps = false;

    public function seatType() {
        return $this->belongsTo(SeatType::class);
    }
}
