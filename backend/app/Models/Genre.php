<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use UsesUuidV7;

    protected $table = 'genres';
    protected $fillable = [
        'name'
    ];
}
