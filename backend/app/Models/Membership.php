<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuidV7;

class Membership extends Model
{
    use HasFactory, UsesUuidV7;

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
