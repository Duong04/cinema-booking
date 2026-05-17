<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory, UsesUuidV7;

    protected $table = 'showtimes';
    protected $fillable = [
        'movie_id',
        'room_id',
        'show_date',
        'start_time',
        'end_time',
        'base_price',
        'status',
        'cancelled_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    public function movie() {
        return $this->belongsTo(Movie::class);
    }

    public function room() {
        return $this->belongsTo(Room::class);
    }

    public function cancelledBy() {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function prices() {
        return $this->hasMany(ShowtimePrice::class);
    }
}
