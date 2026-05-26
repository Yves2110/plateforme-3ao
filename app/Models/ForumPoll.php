<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPoll extends Model
{
    protected $fillable = ['thread_id', 'question', 'options', 'closes_at'];

    protected $casts = [
        'options'   => 'array',
        'closes_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    public function votes()
    {
        return $this->hasMany(ForumPollVote::class, 'poll_id');
    }
}
