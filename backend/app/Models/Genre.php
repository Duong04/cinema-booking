<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory, UsesUuidV7;

    protected $table = 'genres';
    protected $fillable = [
        'name'
    ];
}
