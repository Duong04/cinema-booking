<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Promotion extends Model
{
    use UsesUuidV7;
    protected $table = 'promotions';
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'usage_limit',
        'per_user_limit',
        'status',
        'applicable_to'
    ];
}
