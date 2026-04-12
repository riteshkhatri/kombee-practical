<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'contact_number',
        'postcode',
        'gender',
        'hobbies',
        'state_id',
        'city_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'hobbies' => 'array',
        ];
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Scope a query to only exclude the logged-in user.
     */
    public function scopeExcludeLoggedIn($query)
    {
        if (auth('api')->check()) {
            return $query->where('id', '!=', auth('api')->id());
        }

        return $query;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermissionTo($permission)
    {
        if ($this->hasRole('Admin')) {
            return true;
        }

        $hasPermission = false;
        foreach ($this->roles as $role) {
            if ($role->permissions()->where('name', $permission)->exists()) {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }

    public function getAllPermissions()
    {
        if ($this->hasRole('Admin')) {
            return Permission::pluck('slug');
        }

        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('slug')->unique()->values();
    }
}
