<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuidV7;

class Permission extends Model
{
    use UsesUuidV7;

    protected $table = 'permissions';
    protected $fillable = [
        'name',
        'key'
    ];

    public function permissionActions() {
        return $this->hasMany(PermissionAction::class, 'permission_id');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'role_permissions', 'permission_id', 'action_id')->withPivot('role_id', 'permission_id','action_id');
    }
}
