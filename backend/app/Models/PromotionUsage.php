<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class PromotionUsage extends Model
{
    use UsesUuidV7;

    protected $table = 'promotion_usages';

    protected $fillable = [
        'promotion_id',
        'user_id',
        'booking_id',
        'used_at',
        'discount_amount'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
