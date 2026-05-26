<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RssFeedItem extends Model
{
    protected $fillable = [
        'rss_source_id',
        'guid',
        'title',
        'link',
        'description',
        'published_at',
        'status',
        'actualite_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(RssSource::class, 'rss_source_id');
    }

    public function actualite(): BelongsTo
    {
        return $this->belongsTo(Actualite::class);
    }
}
