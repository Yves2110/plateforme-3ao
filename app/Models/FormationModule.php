<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'title',
        'description',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function lessons()
    {
        return $this->hasMany(FormationLesson::class, 'module_id')->orderBy('order');
    }

    public function publishedLessons()
    {
        return $this->lessons()->where('is_published', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getLessonsCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    public function getPublishedLessonsCountAttribute(): int
    {
        return $this->publishedLessons()->count();
    }
}
