<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory, UsesUuidV7;

    protected $table = 'movies';
    protected $fillable = [
        'title',
        'slug',
        'duration_minutes',
        'poster_url',
        'banner_url',
        'trailer_url',
        'description',
        'content',
        'release_date',
        'rating',
        'rating_score',
        'rating_count',
        'language',
        'status'
    ];

    public function genres() {
        return $this->belongsToMany(Genre::class, 'movie_genres', 'movie_id', 'genre_id');
    }
}
