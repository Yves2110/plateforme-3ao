<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormationQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'formation_quiz_attempts';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'max_score',
        'percentage',
        'is_passed',
        'attempt_number',
        'started_at',
        'completed_at',
        'answers',
    ];

    protected $casts = [
        'score' => 'integer',
        'max_score' => 'integer',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'attempt_number' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(FormationQuiz::class, 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isPassed(): bool
    {
        return $this->is_passed;
    }

    public function complete(array $answers): void
    {
        // Calculer le score
        $score = 0;
        $maxScore = 0;

        $quiz = $this->quiz->load('questions.answers');

        foreach ($quiz->questions as $question) {
            $maxScore += $question->points;

            $userAnswer = $answers[$question->id] ?? null;

            if ($userAnswer === null) {
                continue;
            }

            if ($question->isText()) {
                // Pour les questions texte, vérification manuelle nécessaire
                // Par défaut on donne les points si une réponse est fournie
                // (l'administrateur doit valider manuellement)
                $score += $question->points;
            } elseif ($question->isMultipleChoice()) {
                // Pour QCM multiple, toutes les bonnes réponses doivent être sélectionnées
                $correctIds = $question->getCorrectAnswerIds();
                $selectedIds = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                sort($correctIds);
                sort($selectedIds);
                if ($correctIds === $selectedIds) {
                    $score += $question->points;
                }
            } else {
                // Single choice ou true/false
                $correctIds = $question->getCorrectAnswerIds();
                if (in_array((int) $userAnswer, $correctIds)) {
                    $score += $question->points;
                }
            }
        }

        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
        $passingScore = $quiz->passing_score;

        $this->update([
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'is_passed' => $percentage >= $passingScore,
            'completed_at' => now(),
            'answers' => $answers,
        ]);

        // Si le quiz est réussi, marquer la leçon comme complétée
        if ($percentage >= $passingScore) {
            FormationProgress::firstOrCreate(
                [
                    'user_id' => $this->user_id,
                    'lesson_id' => $quiz->lesson_id,
                ],
                [
                    'completed_at' => now(),
                    'time_spent_seconds' => 0,
                ]
            );
        }
    }
}
