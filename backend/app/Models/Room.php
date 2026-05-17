<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes, UsesUuidV7;
    protected $table = 'rooms';
    protected $fillable = [
        'name',
        'type',
        'cinema_id'
    ];

    public function cinema() {
        return $this->belongsTo(Cinema::class);
    }
}
