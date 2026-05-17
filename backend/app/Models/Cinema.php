<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    use HasFactory, SoftDeletes, UsesUuidV7;
    protected $table = 'cinemas';
    protected $fillable = [
        'name',
        'address',
        'city_id',
        'cinema_chain_id'
    ];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function cinemaChain() {
        return $this->belongsTo(CinemaChain::class);
    }
}
