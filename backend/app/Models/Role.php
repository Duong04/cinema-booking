<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesUuidV7;

class Role extends Model
{
    use HasFactory, UsesUuidV7;
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot('role_id', 'permission_id')
            ->with('roleActions');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'role_permissions')
        ->withPivot('role_id', 'permission_id');
    }

}
