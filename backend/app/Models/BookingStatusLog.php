<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class BookingStatusLog extends Model
{
    use UsesUuidV7; 
    protected $table = 'booking_status_logs';
    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'changed_at'
    ];
}
