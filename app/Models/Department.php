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
}
