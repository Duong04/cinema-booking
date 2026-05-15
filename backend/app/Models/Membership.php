<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Membership extends Model
{
    use UsesUuidV7;

    protected $table = 'memberships';

    protected $fillable = [
        'user_id',
        'tier',
        'points'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
