<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationQuiz extends Model
{
    use HasFactory;

    protected $table = 'formation_quizzes';

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'passing_score',
        'time_limit_minutes',
        'max_attempts',
        'is_published',
        'show_correct_answers',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit_minutes' => 'integer',
        'max_attempts' => 'integer',
        'is_published' => 'boolean',
        'show_correct_answers' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(FormationLesson::class, 'lesson_id');
    }

    public function questions()
    {
        return $this->hasMany(FormationQuestion::class, 'quiz_id')->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(FormationQuizAttempt::class, 'quiz_id');
    }

    public function userAttempts(int $userId)
    {
        return $this->attempts()->where('user_id', $userId);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    public function hasTimeLimit(): bool
    {
        return $this->time_limit_minutes !== null;
    }

    public function canAttempt(int $userId): bool
    {
        $attemptsCount = $this->userAttempts($userId)->count();
        return $attemptsCount < $this->max_attempts;
    }

    public function getRemainingAttempts(int $userId): int
    {
        $attemptsCount = $this->userAttempts($userId)->count();
        return max(0, $this->max_attempts - $attemptsCount);
    }
}
