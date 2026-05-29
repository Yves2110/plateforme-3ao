<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Support\BrandAssets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'title', 'slug', 'type', 'description',
        'file_path', 'url', 'thumbnail', 'duration',
        'user_id', 'is_published', 'featured_in_gallery', 'gallery_sort_order',
        'published_at', 'views', 'source',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'featured_in_gallery' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function coverImageUrl(): string
    {
        if ($this->thumbnail) {
            return asset('storage/'.$this->thumbnail);
        }

        if ($this->type === 'photo' && $this->file_path) {
            return asset('storage/'.$this->file_path);
        }

        if ($this->type === 'gallery') {
            $photo = $this->relationLoaded('photos')
                ? $this->photos->first()
                : $this->photos()->orderBy('order')->first();

            if ($photo?->file_path) {
                return asset('storage/'.$photo->file_path);
            }
        }

        return BrandAssets::logoUrl();
    }

    /** cover = plein cadre, logo = logo 3AO ou vignette centrée (podcasts). */
    public function cardDisplayMode(): string
    {
        if ($this->type === 'podcast') {
            return 'logo';
        }

        if ($this->thumbnail || ($this->type === 'photo' && $this->file_path)) {
            return 'cover';
        }

        if ($this->type === 'gallery' && $this->photos()->exists()) {
            return 'cover';
        }

        return 'logo';
    }

    public function scopeFeaturedGallery($query)
    {
        return $query->where('featured_in_gallery', true)
            ->orderBy('gallery_sort_order')
            ->orderByDesc('published_at');
    }

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
