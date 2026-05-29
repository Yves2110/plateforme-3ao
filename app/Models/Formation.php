<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Formation extends Model
{
    use HasFactory, HasUuid;

    private ?bool $cachedHasLmsContent = null;

    protected $fillable = [
        'title', 'slug', 'type', 'organizer', 'country', 'location',
        'is_online', 'start_date', 'end_date', 'duration', 'description',
        'objectives', 'audience', 'language', 'price', 'registration_url',
        'thumbnail', 'is_validated', 'user_id',
    ];

    protected $casts = [
        'is_online'    => 'boolean',
        'is_validated' => 'boolean',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'price'        => 'decimal:2',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function modules()
    {
        return $this->hasMany(FormationModule::class)->orderBy('order');
    }

    public function publishedModules()
    {
        return $this->modules()->where('is_published', true);
    }

    public function enrollments()
    {
        return $this->hasMany(FormationEnrollment::class);
    }

    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', FormationEnrollment::STATUS_ACTIVE);
    }

    public function completedEnrollments()
    {
        return $this->enrollments()->where('status', FormationEnrollment::STATUS_COMPLETED);
    }

    public function certificates()
    {
        return $this->hasMany(FormationCertificate::class);
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_validated', true);
    }

    public function getTotalLessonsCountAttribute(): int
    {
        return FormationLesson::whereHas('module', function ($q) {
            $q->where('formation_id', $this->id)
              ->where('is_published', true);
        })->where('is_published', true)->count();
    }

    public function getTotalDurationMinutesAttribute(): int
    {
        return FormationLesson::whereHas('module', function ($q) {
            $q->where('formation_id', $this->id)
              ->where('is_published', true);
        })->where('is_published', true)->sum('duration_minutes') ?? 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->total_duration_minutes;
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        if ($remainingMinutes === 0) {
            return $hours . 'h';
        }
        return $hours . 'h ' . $remainingMinutes . 'min';
    }

    public function hasLmsContent(): bool
    {
        if ($this->cachedHasLmsContent !== null) {
            return $this->cachedHasLmsContent;
        }

        return $this->cachedHasLmsContent = $this->modules()
            ->where('is_published', true)
            ->whereHas('lessons', fn ($q) => $q->where('is_published', true))
            ->exists();
    }

    public function pendingEnrollmentsCount(): int
    {
        return $this->enrollments()
            ->where('status', FormationEnrollment::STATUS_PENDING)
            ->count();
    }
}
