<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationProgress extends Model
{
    use HasFactory;

    protected $table = 'formation_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed_at',
        'time_spent_seconds',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(FormationLesson::class, 'lesson_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function markAsCompleted(): void
    {
        if (!$this->completed_at) {
            $this->update(['completed_at' => now()]);
        }
    }
}
