<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function quizResponses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    // Get statistics for this faculty
    public function getStatsAttribute()
    {
        return [
            'total_responses' => $this->quizResponses()->completed()->count(),
            'high_risk_count' => $this->quizResponses()->highRisk()->count(),
            'departments_count' => $this->departments()->count(),
        ];
    }
}
