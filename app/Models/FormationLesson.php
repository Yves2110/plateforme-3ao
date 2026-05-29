<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'type',
        'content',
        'file_path',
        'video_url',
        'duration_minutes',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(FormationModule::class, 'module_id');
    }

    public function getFormationModelAttribute(): ?Formation
    {
        return $this->module?->formation;
    }

    public function progresses()
    {
        return $this->hasMany(FormationProgress::class, 'lesson_id');
    }

    public function quizzes()
    {
        return $this->hasMany(FormationQuiz::class, 'lesson_id');
    }

    public function publishedQuiz()
    {
        return $this->hasOne(FormationQuiz::class, 'lesson_id')->where('is_published', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isPdf(): bool
    {
        return $this->type === 'pdf';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function isCompletedByUser(int $userId): bool
    {
        return $this->progresses()->where('user_id', $userId)->whereNotNull('completed_at')->exists();
    }

    public function hasQuiz(): bool
    {
        return $this->isQuiz() || $this->quizzes()->where('is_published', true)->exists();
    }

    public function learningRouteName(): string
    {
        return $this->hasQuiz() ? 'learning.quiz' : 'learning.lesson';
    }
}
