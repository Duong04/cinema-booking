<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\UsesUuidV7;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UsesUuidV7, HasApiTokens;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'date_of_birth',
        'gender',
        'is_active',
        'role_id',
        'email_verify_token',
        'email_verified_at',
        'token_expired_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verify_token',
        'token_expired_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function confirmedBookingItems()
    {
        return $this->hasManyThrough(BookingItem::class, Booking::class)
            ->where('bookings.status', 'confirmed');
    }

    public function permissions()
    {
        return $this->role()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    public function actions()
    {
        return $this->role->actions();
    }

    public function hasPermission($permissionKey)
    {
        return $this->permissions()->contains('key', $permissionKey);
    }

    public function hasAction($permissionKey, $actionKey, $role_id)
    {
        $permission = $this->permissions()->where('key', $permissionKey)->first();

        if (!$permission) {
            return false; 
        }

        $filteredActions = $permission->roleActions->filter(function ($action) use ($role_id, $permission) {
            return $action->pivot->role_id == $role_id && $action->pivot->permission_id == $permission->id;
        })->values();

        return $filteredActions->contains('key', $actionKey);
    }
}
