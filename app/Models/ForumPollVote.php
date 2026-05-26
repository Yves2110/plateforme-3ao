<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPollVote extends Model
{
    protected $fillable = ['poll_id', 'user_id', 'option_index'];

    public function poll()
    {
        return $this->belongsTo(ForumPoll::class, 'poll_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
