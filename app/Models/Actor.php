<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'type', 'country', 'region',
        'address', 'city', 'phone',
        'lat', 'lng', 'description', 'website', 'email',
        'logo', 'is_validated',
    ];

    protected $casts = [
        'is_validated' => 'boolean',
        'lat'          => 'float',
        'lng'          => 'float',
    ];

    public function themes()
    {
        return $this->belongsToMany(Tag::class, 'actor_themes', 'actor_id', 'tag_id');
    }

    public function linksFrom()
    {
        return $this->hasMany(ActorLink::class, 'actor_id_from');
    }

    public function linksTo()
    {
        return $this->hasMany(ActorLink::class, 'actor_id_to');
    }
}
