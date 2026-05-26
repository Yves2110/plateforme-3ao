<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationAnswer extends Model
{
    use HasFactory;

    protected $table = 'formation_answers';

    protected $fillable = [
        'question_id',
        'answer',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(FormationQuestion::class, 'question_id');
    }
}
