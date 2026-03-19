<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CinemaChain extends Model
{
    protected $table = 'cinema_chains';
    protected $fillable = [
        'name',
        'logo'
    ];
}
