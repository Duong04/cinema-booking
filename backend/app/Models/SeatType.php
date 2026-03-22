<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Model;

class SeatType extends Model
{
    use UsesUuidV7;

    protected $table = 'seat_types';

    protected $fillable = [
        'name',
        'base_multiplier'
    ];
}
