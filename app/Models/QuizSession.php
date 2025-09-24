<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_token', 'quiz_response_id', 'current_step', 
        'temp_data', 'expires_at'
    ];

    protected $casts = [
        'temp_data' => 'array',
        'expires_at' => 'datetime',
    ];

    public function quizResponse()
    {
        return $this->belongsTo(QuizResponse::class);
    }

    // Check if session is expired
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    // Generate unique session token
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }
}
