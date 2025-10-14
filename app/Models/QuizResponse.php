<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_year',
        'faculty_id',
        'department_id',
        'education_level',
        'nim',
        'full_name',
        'gender', 'birth_place', 'birth_date', 'phone', 'address', 
        'living_arrangement', 'origin_province', 'origin_area_type', 
        'email', 'religion', 'parents_marital_status', 'child_order', 
        'siblings_count', 'scholarship', 'admission_path', 'parents_education',
        'parents_income', 'family_members_count', 'has_chronic_disease', 
        'chronic_disease_details', 'current_medication', 'medication_details',
        'head_injury_history', 'injury_details', 'substance_use', 
        'substance_details', 'psychological_treatment_history', 
        'treatment_details', 'family_mental_health_history', 
        'family_history_details', 'family_relationship_description',
        'quiz_status', 'phq9_responses', 'dass21_responses', 
        'phq9_total_score', 'phq9_category', 'dass21_total_score', 
        'dass21_category', 'overall_risk_level', 'phq9_passed_threshold',
        'needs_dass21', 'started_at', 'phq9_completed_at', 
        'dass21_completed_at', 'completed_at'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'phq9_responses' => 'array',
        'dass21_responses' => 'array',
        'has_chronic_disease' => 'boolean',
        'current_medication' => 'boolean',
        'head_injury_history' => 'boolean',
        'psychological_treatment_history' => 'boolean',
        'family_mental_health_history' => 'boolean',
        'phq9_passed_threshold' => 'boolean',
        'needs_dass21' => 'boolean',
        'started_at' => 'datetime',
        'phq9_completed_at' => 'datetime',
        'dass21_completed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function quizSession()
    {
        return $this->hasOne(QuizSession::class);
    }

    // PHQ-9 Scoring Logic
    public function calculatePhq9Score()
    {
        if (!$this->phq9_responses || count($this->phq9_responses) !== 9) {
            return ['score' => 0, 'category' => 'Invalid'];
        }

        $score = 0;
        foreach ($this->phq9_responses as $response) {
            $score += $this->convertResponseToNumeric($response);
        }

        $category = $this->getPhq9Category($score);
        
        return ['score' => $score, 'category' => $category];
    }

    // DASS-21 Extended Scoring Logic (30 questions)
    public function calculateDass21Score()
    {
        if (!$this->dass21_responses || count($this->dass21_responses) !== 30) {
            return ['score' => 0, 'category' => 'Invalid'];
        }

        $score = 0;
        foreach ($this->dass21_responses as $response) {
            $score += $this->convertResponseToNumeric($response);
        }

        $category = $this->getDass21Category($score);
        
        return ['score' => $score, 'category' => $category];
    }

    // Convert response text to numeric score (0-3)
    private function convertResponseToNumeric($response)
    {
        $scoreMap = [
            'Tidak Pernah' => 0,
            'Kadang-Kadang' => 1,
            'Sering' => 2,
            'Sering Sekali' => 3
        ];

        return $scoreMap[$response] ?? 0;
    }

    // PHQ-9 Category Classification (based on your formula)
    private function getPhq9Category($score)
    {
        if ($score <= 15.75) return 'Sangat rendah';
        if ($score <= 20.25) return 'Rendah';
        if ($score <= 24.75) return 'Sedang';
        if ($score <= 29.25) return 'Tinggi';
        return 'Sangat tinggi';
    }

    // DASS-21 Extended Category Classification (based on your formula)
    private function getDass21Category($score)
    {
        if ($score <= 51) return 'Sangat rendah';
        if ($score <= 65) return 'Rendah';
        if ($score <= 79) return 'Sedang';
        if ($score <= 93) return 'Tinggi';
        return 'Sangat tinggi';
    }

    // Check if PHQ-9 score requires DASS-21
    public function shouldContinueToDass21()
    {
        $phqResult = $this->calculatePhq9Score();
        return in_array($phqResult['category'], ['Sedang', 'Tinggi', 'Sangat tinggi']);
    }

    // Calculate overall risk level
    public function calculateOverallRiskLevel()
    {
        $phq9Result = $this->calculatePhq9Score();
        $dass21Result = $this->calculateDass21Score();

        $phq9High = in_array($phq9Result['category'], ['Tinggi', 'Sangat tinggi']);
        $dass21High = in_array($dass21Result['category'], ['Tinggi', 'Sangat tinggi']);
        
        if ($phq9High && $dass21High) return 'Critical';
        if ($phq9High || $dass21High) return 'High';
        
        $phq9Moderate = $phq9Result['category'] === 'Sedang';
        $dass21Moderate = $dass21Result['category'] === 'Sedang';
        
        if ($phq9Moderate || $dass21Moderate) return 'Moderate';
        
        return 'Low';
    }

    // Auto-calculate scores before saving
    protected static function booted()
    {
        static::saving(function ($quizResponse) {
            // Calculate PHQ-9 scores if responses exist
            if ($quizResponse->phq9_responses) {
                $phq9Result = $quizResponse->calculatePhq9Score();
                $quizResponse->phq9_total_score = $phq9Result['score'];
                $quizResponse->phq9_category = $phq9Result['category'];
                $quizResponse->phq9_passed_threshold = $quizResponse->shouldContinueToDass21();
                $quizResponse->needs_dass21 = $quizResponse->phq9_passed_threshold;
            }

            // Calculate DASS-21 scores if responses exist
            if ($quizResponse->dass21_responses) {
                $dass21Result = $quizResponse->calculateDass21Score();
                $quizResponse->dass21_total_score = $dass21Result['score'];
                $quizResponse->dass21_category = $dass21Result['category'];
            }

            // Calculate overall risk level
            $quizResponse->overall_risk_level = $quizResponse->calculateOverallRiskLevel();
        });
    }

    // Scopes for filtering
    public function scopeCompleted($query)
    {
        return $query->where('quiz_status', 'completed');
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('overall_risk_level', ['High', 'Critical']);
    }

    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('student_year', $year);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('completed_at', [$startDate, $endDate]);
    }

    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('overall_risk_level', $riskLevel);
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->quiz_status === 'completed';
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function getRiskBadgeClassAttribute()
    {
        return match($this->overall_risk_level) {
            'Low' => 'bg-success',
            'Moderate' => 'bg-warning',
            'High' => 'bg-danger',
            'Critical' => 'bg-dark',
            default => 'bg-secondary'
        };
    }
}
