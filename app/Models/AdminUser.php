<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function adminLogs()
    {
        return $this->hasMany(AdminLog::class);
    }

    // Check if user has specific role
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function canManageUsers()
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canViewReports()
    {
        return in_array($this->role, ['super_admin', 'admin', 'viewer']);
    }
}
