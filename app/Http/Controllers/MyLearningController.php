<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationEnrollment;
use App\Models\FormationLesson;
use App\Models\FormationProgress;
use App\Models\FormationQuiz;
use App\Models\FormationQuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MyLearningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard "Mes formations" - Liste des formations de l'utilisateur
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Formations en cours
        $activeEnrollments = FormationEnrollment::with('formation')
            ->where('user_id', $user->id)
            ->whereIn('status', [FormationEnrollment::STATUS_ACTIVE, FormationEnrollment::STATUS_PENDING])
            ->latest('enrolled_at')
            ->get();

        // Formations terminées
        $completedEnrollments = FormationEnrollment::with('formation')
            ->where('user_id', $user->id)
            ->where('status', FormationEnrollment::STATUS_COMPLETED)
            ->latest('completed_at')
            ->get();

        // Toutes les formations disponibles (pour découverte)
        $availableFormations = Formation::validated()
            ->whereNotIn('id', $activeEnrollments->pluck('formation_id')->merge($completedEnrollments->pluck('formation_id')))
            ->latest()
            ->take(6)
            ->get();

        // Stats globales
        $stats = [
            'in_progress' => $activeEnrollments->count(),
            'completed' => $completedEnrollments->count(),
            'total_hours' => $this->getTotalLearningHours($user->id),
        ];

        return view('membres.learning-dashboard', compact(
            'activeEnrollments',
            'completedEnrollments',
            'availableFormations',
            'stats'
        ));
    }

    /**
     * Inscription à une formation
     */
    public function enroll(Request $request, Formation $formation)
    {
        $user = auth()->user();

        // Vérifier si déjà inscrit
        $existing = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->first();

        if ($existing) {
            return redirect()->route('learning.show', $formation->slug)
                ->with('info', 'Vous êtes déjà inscrit à cette formation.');
        }

        // Créer l'inscription
        $enrollment = FormationEnrollment::create([
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => $formation->price > 0 ? FormationEnrollment::STATUS_PENDING : FormationEnrollment::STATUS_ACTIVE,
            'paid_amount' => $formation->price > 0 ? 0 : null,
            'enrolled_at' => $formation->price > 0 ? null : now(),
        ]);

        $message = $formation->price > 0
            ? 'Inscription enregistrée. Paiement requis pour accéder au contenu.'
            : 'Inscription confirmée ! Vous pouvez maintenant accéder au contenu.';

        return redirect()->route('learning.show', $formation->slug)
            ->with('success', $message);
    }

    /**
     * Afficher une formation avec ses modules/leçons
     */
    public function show(Formation $formation)
    {
        $user = auth()->user();

        // Vérifier l'inscription
        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->first();

        if (!$enrollment || !$enrollment->isActive()) {
            // Rediriger vers la page publique avec option d'inscription
            return redirect()->route('formation.show', $formation->slug)
                ->with('info', 'Inscrivez-vous pour accéder au contenu complet.');
        }

        // Charger les modules avec leurs leçons
        $modules = $formation->publishedModules()
            ->with(['publishedLessons' => function ($q) use ($user) {
                $q->with(['progresses' => function ($pq) use ($user) {
                    $pq->where('user_id', $user->id);
                }]);
            }])
            ->get();

        // Calculer la progression globale
        $totalLessons = $formation->total_lessons_count;
        $completedLessons = FormationProgress::where('user_id', $user->id)
            ->whereHas('lesson.module', function ($q) use ($formation) {
                $q->where('formation_id', $formation->id);
            })
            ->whereNotNull('completed_at')
            ->count();

        $progressPercent = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        // Mettre à jour le statut si 100% complété
        if ($progressPercent === 100 && !$enrollment->isCompleted()) {
            $enrollment->complete();
        }

        return view('membres.formation-show', compact(
            'formation',
            'modules',
            'enrollment',
            'progressPercent',
            'completedLessons',
            'totalLessons'
        ));
    }

    /**
     * Afficher une leçon spécifique
     */
    public function lesson(Formation $formation, FormationLesson $lesson)
    {
        $user = auth()->user();

        // Vérifier que la leçon appartient bien à la formation
        if ($lesson->module->formation_id !== $formation->id) {
            abort(404);
        }

        // Vérifier l'inscription active
        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->where('status', FormationEnrollment::STATUS_ACTIVE)
            ->first();

        if (!$enrollment) {
            return redirect()->route('formation.show', $formation->slug)
                ->with('error', 'Accès réservé aux apprenants inscrits.');
        }

        // Récupérer ou créer la progression
        $progress = FormationProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'time_spent_seconds' => 0,
            ]
        );

        // Leçons précédente/suivante pour navigation
        $currentModule = $lesson->module;
        $allLessons = FormationLesson::whereHas('module', function ($q) use ($formation) {
            $q->where('formation_id', $formation->id)
              ->where('is_published', true);
        })
        ->where('is_published', true)
        ->orderByRaw('(SELECT `order` FROM formation_modules WHERE formation_modules.id = formation_lessons.module_id)')
        ->orderBy('order')
        ->get();

        $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        return view('membres.lesson-show', compact(
            'formation',
            'lesson',
            'currentModule',
            'progress',
            'prevLesson',
            'nextLesson',
            'enrollment'
        ));
    }

    /**
     * Marquer une leçon comme complétée
     */
    public function completeLesson(Request $request, Formation $formation, FormationLesson $lesson)
    {
        $user = auth()->user();

        // Vérifier l'inscription
        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->where('status', FormationEnrollment::STATUS_ACTIVE)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Mettre à jour la progression
        $progress = FormationProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
                'time_spent_seconds' => $request->input('time_spent', 0),
            ]
        );

        // Calculer la nouvelle progression
        $totalLessons = $formation->total_lessons_count;
        $completedLessons = FormationProgress::where('user_id', $user->id)
            ->whereHas('lesson.module', function ($q) use ($formation) {
                $q->where('formation_id', $formation->id);
            })
            ->whereNotNull('completed_at')
            ->count();

        $percent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Compléter la formation si 100%
        if ($percent === 100 && !$enrollment->isCompleted()) {
            $enrollment->complete();
        }

        return response()->json([
            'success' => true,
            'progress_percent' => $percent,
            'completed' => $percent === 100,
        ]);
    }

    /**
     * Mettre à jour le temps passé sur une leçon
     */
    public function trackTime(Request $request, FormationLesson $lesson)
    {
        $user = auth()->user();
        $seconds = $request->input('seconds', 0);

        FormationProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->increment('time_spent_seconds', $seconds);

        return response()->json(['success' => true]);
    }

    private function getTotalLearningHours(int $userId): int
    {
        return Cache::remember("user.{$userId}.learning_hours", now()->addHours(1), function () use ($userId) {
            $totalSeconds = FormationProgress::where('user_id', $userId)
                ->sum('time_spent_seconds') ?? 0;

            return (int) floor($totalSeconds / 3600);
        });
    }

    /**
     * Afficher le quiz d'une leçon
     */
    public function quiz(Formation $formation, FormationLesson $lesson)
    {
        $user = auth()->user();

        // Vérifier que la leçon appartient bien à la formation
        if ($lesson->module->formation_id !== $formation->id) {
            abort(404);
        }

        // Vérifier l'inscription active
        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->where('status', FormationEnrollment::STATUS_ACTIVE)
            ->first();

        if (!$enrollment) {
            return redirect()->route('formation.show', $formation->slug)
                ->with('error', 'Accès réservé aux apprenants inscrits.');
        }

        // Récupérer le quiz publié de la leçon
        $quiz = FormationQuiz::where('lesson_id', $lesson->id)
            ->where('is_published', true)
            ->with('questions.answers')
            ->first();

        if (!$quiz) {
            return redirect()->route('learning.lesson', [$formation->slug, $lesson])
                ->with('info', 'Aucun quiz disponible pour cette leçon.');
        }

        // Récupérer les tentatives de l'utilisateur
        $attempts = $quiz->userAttempts($user->id)
            ->orderBy('attempt_number')
            ->get();

        $bestAttempt = $attempts->where('is_passed', true)->first();
        $attemptsCount = $attempts->count();
        $remainingAttempts = $quiz->getRemainingAttempts($user->id);
        $canAttempt = $remainingAttempts > 0 && !$bestAttempt;

        return view('membres.quiz-show', compact(
            'formation',
            'lesson',
            'quiz',
            'attempts',
            'bestAttempt',
            'attemptsCount',
            'remainingAttempts',
            'canAttempt'
        ));
    }

    /**
     * Démarrer une tentative de quiz
     */
    public function startQuiz(Formation $formation, FormationLesson $lesson, FormationQuiz $quiz)
    {
        $user = auth()->user();

        // Vérifications
        if ($lesson->module->formation_id !== $formation->id || $quiz->lesson_id !== $lesson->id) {
            abort(404);
        }

        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->where('status', FormationEnrollment::STATUS_ACTIVE)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Vérifier si l'utilisateur peut tenter le quiz
        if (!$quiz->canAttempt($user->id)) {
            return response()->json(['error' => 'Nombre maximum de tentatives atteint'], 403);
        }

        // Vérifier si une tentative est en cours
        $existingAttempt = FormationQuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'success' => true,
                'attempt_id' => $existingAttempt->id,
                'started_at' => $existingAttempt->started_at,
            ]);
        }

        // Compter les tentatives précédentes
        $attemptNumber = FormationQuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->count() + 1;

        // Créer une nouvelle tentative
        $attempt = FormationQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptNumber,
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'started_at' => $attempt->started_at,
        ]);
    }

    /**
     * Soumettre les réponses du quiz
     */
    public function submitQuiz(Request $request, Formation $formation, FormationLesson $lesson, FormationQuiz $quiz)
    {
        $user = auth()->user();

        // Vérifications
        if ($lesson->module->formation_id !== $formation->id || $quiz->lesson_id !== $lesson->id) {
            abort(404);
        }

        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->where('status', FormationEnrollment::STATUS_ACTIVE)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Récupérer la tentative en cours
        $attempt = FormationQuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'Aucune tentative en cours'], 400);
        }

        // Vérifier le temps si limité
        if ($quiz->hasTimeLimit()) {
            $elapsedMinutes = now()->diffInMinutes($attempt->started_at);
            if ($elapsedMinutes > $quiz->time_limit_minutes) {
                return response()->json(['error' => 'Temps écoulé'], 403);
            }
        }

        // Traiter les réponses
        $answers = $request->input('answers', []);
        $attempt->complete($answers);

        return response()->json([
            'success' => true,
            'attempt' => [
                'score' => $attempt->score,
                'max_score' => $attempt->max_score,
                'percentage' => $attempt->percentage,
                'is_passed' => $attempt->is_passed,
            ],
        ]);
    }

    /**
     * Afficher les résultats d'une tentative de quiz
     */
    public function quizResults(Formation $formation, FormationLesson $lesson, FormationQuiz $quiz, FormationQuizAttempt $attempt)
    {
        $user = auth()->user();

        // Vérifications
        if ($lesson->module->formation_id !== $formation->id
            || $quiz->lesson_id !== $lesson->id
            || $attempt->quiz_id !== $quiz->id
            || $attempt->user_id !== $user->id) {
            abort(404);
        }

        $quiz->load('questions.answers');

        return view('membres.quiz-results', compact(
            'formation',
            'lesson',
            'quiz',
            'attempt'
        ));
    }
}
