<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function adminLogs()
    {
        return $this->hasMany(AdminLog::class);
    }

    // Check if user is active
    public function isActive()
    {
        return $this->is_active;
    }

    // Get full display name with NIP
    public function getDisplayNameAttribute(): string
    {
        return $this->nip ? "{$this->name} ({$this->nip})" : $this->name;
    }

    // Override authentication to check active status
    public function getAuthPassword()
    {
        if (! $this->is_active) {
            return null; // Prevent login for inactive users
        }

        return $this->password;
    }

    // Check if user has specific role
    // public function hasRole($role)
    // {
    //     return $this->role === $role;
    // }

    // public function canManageUsers()
    // {
    //     return in_array($this->role, ['super_admin', 'admin']);
    // }

    // public function canViewReports()
    // {
    //     return in_array($this->role, ['super_admin', 'admin', 'viewer']);
    // }
}
