<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Support\ActualiteCategories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actualite extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'syndicated_source',
        'source_url',
        'thumbnail',
        'is_published',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published'  => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rssFeedItem()
    {
        return $this->hasOne(RssFeedItem::class);
    }

    public function isSyndicated(): bool
    {
        return filled($this->syndicated_source);
    }

    public function displayCategory(): string
    {
        return ActualiteCategories::normalizeLabel($this->category);
    }

    public function categoryBadgeClass(): string
    {
        return ActualiteCategories::badgeClass($this->category);
    }

    public function renderedContent(): string
    {
        if (! $this->content) {
            return '';
        }

        if ($this->isSyndicated()) {
            return strip_tags($this->content, '<p><a><br><strong><em><ul><ol><li><h2><h3><blockquote>');
        }

        return nl2br(e($this->content));
    }
}
