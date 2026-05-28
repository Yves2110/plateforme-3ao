<?php

namespace App\Models;

use App\Support\EventSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'type', 'description',
        'start_date', 'end_date', 'location', 'country',
        'lat', 'lng', 'is_online', 'registration_url',
        'thumbnail', 'is_validated', 'user_id',
    ];

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'is_online'    => 'boolean',
        'is_validated' => 'boolean',
    ];

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedule(): EventSchedule
    {
        return EventSchedule::for($this);
    }
}

