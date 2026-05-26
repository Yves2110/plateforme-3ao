<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category', 'body',
        'user_id', 'is_pinned', 'is_locked', 'is_validated',
        'views', 'last_reply_at',
    ];

    protected $casts = [
        'is_pinned'    => 'boolean',
        'is_locked'    => 'boolean',
        'is_validated' => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function poll()
    {
        return $this->hasOne(ForumPoll::class, 'thread_id');
    }

    public function scopeValidated($q)
    {
        return $q->where('is_validated', true);
    }

    public function scopeByCategory($q, string $cat)
    {
        return $q->where('category', $cat);
    }
}
