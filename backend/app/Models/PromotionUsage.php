<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class PromotionUsage extends Model
{
    use UsesUuidV7;
    protected $table = 'booking_promotions';
    protected $fillable = [
        'booking_id',
        'promotion_id',
        'discount_amount',
    ];
}
