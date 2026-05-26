<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RssSource extends Model
{
    protected $fillable = [
        'name',
        'url',
        'is_active',
        'last_fetched_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_fetched_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RssFeedItem::class);
    }
}
