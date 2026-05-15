<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class ActivityLog extends Model
{
    use UsesUuidV7;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'metadata'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
