<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieGenre extends Model
{
    protected $table = 'movie_genres';
    protected $fillable = [
        'movie_id',
        'genre_id'
    ];
}
