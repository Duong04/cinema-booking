<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Permission extends Model
{
    use UsesUuidV7;

    protected $table = 'permissions';
    protected $fillable = [
        'name',
        'key'
    ];
}
