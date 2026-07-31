<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->nama_role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->nama_role, $roles, true);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->role?->permissions()
            ->where('nama_permission', $permission)
            ->exists() ?? false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->role?->permissions()
            ->whereIn('nama_permission', $permissions)
            ->exists() ?? false;
    }
}
