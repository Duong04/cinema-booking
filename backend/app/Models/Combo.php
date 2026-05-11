<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Combo extends Model
{
    use UsesUuidV7;
    protected $table = 'combos';
    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'image',
        'cinema_id',
    ];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }
}
