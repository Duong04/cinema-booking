<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class BookingCombo extends Model
{
    use UsesUuidV7;
    protected $table = 'booking_combos';
    protected $fillable = [
        'booking_id',
        'combo_id',
        'quantity',
        'price'
    ];
}
