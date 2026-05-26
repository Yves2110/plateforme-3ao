<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $fillable = [
        'thread_id', 'user_id', 'body',
        'is_validated', 'is_solution', 'parent_id',
    ];

    protected $casts = [
        'is_validated' => 'boolean',
        'is_solution'  => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }
}
