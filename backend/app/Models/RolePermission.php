<?php

namespace App\Models;

use App\Traits\UsesUuidV7;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use UsesUuidV7;
    protected $table = 'role_permissions';
    protected $fillable = [
        'role_id',
        'permission_id',
        'action_id',
    ];
}
