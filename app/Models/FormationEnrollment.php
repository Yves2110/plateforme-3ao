<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationEnrollment extends Model
{
    use HasFactory;

    protected $table = 'formation_enrollments';

    protected $fillable = [
        'user_id',
        'formation_id',
        'status',
        'paid_amount',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => 'string',
        'paid_amount' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function progresses()
    {
        return $this->hasMany(FormationProgress::class, 'user_id', 'user_id')
            ->whereIn('lesson_id', function ($query) {
                $query->select('id')
                    ->from('formation_lessons')
                    ->whereIn('module_id', function ($sub) {
                        $sub->select('id')
                            ->from('formation_modules')
                            ->where('formation_id', $this->formation_id);
                    });
            });
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function activate(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE, 'enrolled_at' => now()]);
    }

    public function complete(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED, 'completed_at' => now()]);
    }

    public function getProgressPercentageAttribute(): int
    {
        $totalLessons = FormationLesson::whereHas('module', function ($q) {
            $q->where('formation_id', $this->formation_id)
              ->where('is_published', true);
        })->where('is_published', true)->count();

        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = FormationProgress::where('user_id', $this->user_id)
            ->whereHas('lesson.module', function ($q) {
                $q->where('formation_id', $this->formation_id);
            })
            ->whereNotNull('completed_at')
            ->count();

        return (int) round(($completedLessons / $totalLessons) * 100);
    }
}
