<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Disable timestamps if not using them
    public $timestamps = false;

    // Relationship with cities
    public function cities()
    {
        return $this->hasMany(City::class)->whereNull('removed_at');
    }

    // Scope for active provinces
    public function scopeActive($query)
    {
        return $query->whereNull('removed_at');
    }
}