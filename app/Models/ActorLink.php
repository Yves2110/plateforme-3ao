<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActorLink extends Model
{
    protected $fillable = ['actor_id_from', 'actor_id_to', 'relation_type', 'project_name'];

    public function actorFrom()
    {
        return $this->belongsTo(Actor::class, 'actor_id_from');
    }

    public function actorTo()
    {
        return $this->belongsTo(Actor::class, 'actor_id_to');
    }
}
