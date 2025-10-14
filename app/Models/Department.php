<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['faculty_id', 'name', 'code', 'level'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function quizResponses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    /**
     * Get available education levels for this department
     * Converts database level format (S1,S2,S3, D IV, etc.) to simplified format
     * 
     * @return array ['D4', 'S1', 'Pascasarjana']
     */
    public function getAvailableLevels()
    {
        if (!$this->level) {
            return ['S1']; // Default to S1 if no level specified
        }

        $levels = array_map('trim', explode(',', $this->level));
        $simplified = [];

        foreach ($levels as $level) {
            if (stripos($level, 'D IV') !== false || $level === 'D4') {
                $simplified[] = 'D4';
            } elseif ($level === 'S1') {
                $simplified[] = 'S1';
            } elseif (in_array($level, ['S2', 'S3']) || stripos($level, 'PROFESI') !== false) {
                $simplified[] = 'Pascasarjana';
            }
        }

        // Return unique values in order: D4, S1, Pascasarjana
        $order = ['D4', 'S1', 'Pascasarjana'];
        $simplified = array_unique($simplified);

        usort($simplified, function ($a, $b) use ($order) {
            return array_search($a, $order) <=> array_search($b, $order);
        });

        return $simplified;
    }

    /**
     * Check if department has a specific level
     * 
     * @param string $level
     * @return bool
     */
    public function hasLevel($level)
    {
        return in_array($level, $this->getAvailableLevels());
    }

    /**
     * Get display name for level
     * 
     * @param string $level
     * @return string
     */
    public static function getLevelDisplayName($level)
    {
        return match ($level) {
            'D4' => 'D4 (Diploma 4)',
            'S1' => 'S1 (Sarjana)',
            'Pascasarjana' => 'Pascasarjana (S2/S3)',
            default => $level
        };
    }
}