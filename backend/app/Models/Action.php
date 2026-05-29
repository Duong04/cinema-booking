<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuidV7;

class Action extends Model
{
    use HasFactory, UsesUuidV7;

    protected $table = 'actions';
    protected $fillable = [
        'name',
        'key'
    ];

    public function permissionActions() {
        return $this->hasMany(PermissionAction::class, 'action_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_actions', 'action_id', 'permission_id');
    }
}
