<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class PaymentAttempt extends Model
{
    use UsesUuidV7;

    protected $table = 'payment_attempts';
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'provider',
        'request_payload',
        'response_payload',
        'status',
    ];
}
