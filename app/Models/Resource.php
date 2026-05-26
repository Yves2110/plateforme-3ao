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
}
