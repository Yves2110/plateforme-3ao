<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'category'];

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_tag');
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'actor_themes', 'tag_id', 'actor_id');
    }
}
