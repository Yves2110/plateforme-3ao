<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'token',
        'is_active',
        'source',
        'ip',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'subscribed_at'    => 'datetime',
        'unsubscribed_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            if (empty($subscriber->token)) {
                $subscriber->token = Str::random(48);
            }
            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function unsubscribeUrl(): string
    {
        return route('newsletter.unsubscribe', ['token' => $this->token]);
    }
}
