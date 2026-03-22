<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use UsesUuidV7;
    protected $table = 'cities';
    protected $fillable = [
        'name'
    ];

}
