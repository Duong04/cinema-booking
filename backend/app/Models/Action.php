<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Action extends Model
{
    use UsesUuidV7;

    protected $table = 'actions';
    protected $fillable = [
        'name',
        'key'
    ];
}
