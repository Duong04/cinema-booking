<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Role extends Model
{
    use UsesUuidV7;
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
    ];
}
