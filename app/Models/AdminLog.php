<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id', 'action', 'description', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    // Log admin action
    public static function logAction($adminUserId, $action, $description, $metadata = null)
    {
        return static::create([
            'admin_user_id' => $adminUserId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
