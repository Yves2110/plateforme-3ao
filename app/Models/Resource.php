<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'abstract',
        'file_path',
        'video_url',
        'thumbnail',
        'language',
        'country',
        'author',
        'is_validated',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_validated'  => 'boolean',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'resource_tag');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $url = $this->video_url;

        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m);

            return isset($m[1]) ? 'https://www.youtube.com/embed/'.$m[1] : null;
        }

        if (str_contains($url, 'vimeo.com')) {
            preg_match('/vimeo\.com\/(\d+)/', $url, $m);

            return isset($m[1]) ? 'https://player.vimeo.com/video/'.$m[1] : null;
        }

        return $url;
    }

    public function isVideoType(): bool
    {
        return $this->type === 'Vidéo';
    }
}
