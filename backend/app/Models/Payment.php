<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Payment extends Model
{
    use UsesUuidV7;

    protected $table = 'payments';

    protected $fillable = [
        'booking_id',
        'provider',
        'transaction_code',
        'amount',
        'status',
        'paid_at',
        'idempotency_key',
        'refunded_amount',
        'refund_status',
    ];
}
