<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoviePreview extends Model
{
    protected $table = 'movie_previews';

    protected $fillable = [
        'movie_id',
        'preview_url',
        'user_id',
        'rating_score',
        'comment',
        'status',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
