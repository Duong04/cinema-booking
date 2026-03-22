<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Model;

class CinemaChain extends Model
{
    use UsesUuidV7;
    protected $table = 'cinema_chains';
    protected $fillable = [
        'name',
        'logo'
    ];

    public function cinemas() {
        return $this->hasMany(Cinema::class);
    }
}
