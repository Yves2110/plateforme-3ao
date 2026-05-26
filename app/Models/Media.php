<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'type', 'description',
        'file_path', 'url', 'thumbnail', 'duration',
        'user_id', 'is_published', 'published_at',
        'views', 'source',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function photos()
    {
        return $this->hasMany(GalleryPhoto::class, 'media_id')->orderBy('order');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeByType($q, string $type)
    {
        return $q->where('type', $type);
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->url) return null;

        if (str_contains($this->url, 'youtube.com') || str_contains($this->url, 'youtu.be')) {
            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->url, $m);
            return isset($m[1]) ? 'https://www.youtube.com/embed/' . $m[1] : null;
        }

        if (str_contains($this->url, 'vimeo.com')) {
            preg_match('/vimeo\.com\/(\d+)/', $this->url, $m);
            return isset($m[1]) ? 'https://player.vimeo.com/video/' . $m[1] : null;
        }

        return $this->url;
    }
}
