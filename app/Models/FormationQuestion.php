<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationQuestion extends Model
{
    use HasFactory;

    protected $table = 'formation_questions';

    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'points',
        'explanation',
        'order',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    public const string TYPE_SINGLE_CHOICE = 'single_choice';
    public const string TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const string TYPE_TRUE_FALSE = 'true_false';
    public const string TYPE_TEXT = 'text';

    public function quiz()
    {
        return $this->belongsTo(FormationQuiz::class, 'quiz_id');
    }

    public function answers()
    {
        return $this->hasMany(FormationAnswer::class, 'question_id')->orderBy('order');
    }

    public function correctAnswers()
    {
        return $this->answers()->where('is_correct', true);
    }

    public function isSingleChoice(): bool
    {
        return $this->type === self::TYPE_SINGLE_CHOICE;
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === self::TYPE_MULTIPLE_CHOICE;
    }

    public function isTrueFalse(): bool
    {
        return $this->type === self::TYPE_TRUE_FALSE;
    }

    public function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    public function getCorrectAnswerIds(): array
    {
        return $this->correctAnswers()->pluck('id')->toArray();
    }
}
