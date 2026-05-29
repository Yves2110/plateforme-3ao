<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\FormationCertificate;
use App\Models\FormationEnrollment;
use App\Models\FormationLesson;
use App\Models\FormationProgress;
use App\Models\FormationQuiz;
use App\Models\FormationQuizAttempt;
use App\Services\FormationCertificateService;
use App\Services\FormationCompletionService;
use App\Services\FormationEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MyLearningController extends Controller
{
    public function __construct(
        private FormationCompletionService $completionService,
        private FormationCertificateService $certificateService,
        private FormationEnrollmentService $enrollmentService,
    ) {}
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
        $completedEnrollments = FormationEnrollment::with(['formation', 'certificate'])
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

        $this->hydrateProgressPercentages($activeEnrollments, $user->id);

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

    private function hydrateProgressPercentages($enrollments, int $userId): void
    {
        if ($enrollments->isEmpty()) {
            return;
        }

        $formationIds = $enrollments->pluck('formation_id')->unique()->values();

        $totals = DB::table('formation_lessons')
            ->join('formation_modules', 'formation_modules.id', '=', 'formation_lessons.module_id')
            ->whereIn('formation_modules.formation_id', $formationIds)
            ->where('formation_modules.is_published', true)
            ->where('formation_lessons.is_published', true)
            ->groupBy('formation_modules.formation_id')
            ->selectRaw('formation_modules.formation_id as formation_id, COUNT(formation_lessons.id) as total')
            ->pluck('total', 'formation_id');

        $completed = DB::table('formation_progress')
            ->join('formation_lessons', 'formation_lessons.id', '=', 'formation_progress.lesson_id')
            ->join('formation_modules', 'formation_modules.id', '=', 'formation_lessons.module_id')
            ->where('formation_progress.user_id', $userId)
            ->whereNotNull('formation_progress.completed_at')
            ->whereIn('formation_modules.formation_id', $formationIds)
            ->where('formation_modules.is_published', true)
            ->where('formation_lessons.is_published', true)
            ->groupBy('formation_modules.formation_id')
            ->selectRaw('formation_modules.formation_id as formation_id, COUNT(formation_progress.id) as completed')
            ->pluck('completed', 'formation_id');

        foreach ($enrollments as $enrollment) {
            $total = (int) ($totals[$enrollment->formation_id] ?? 0);
            $done = (int) ($completed[$enrollment->formation_id] ?? 0);
            $percent = $total > 0 ? (int) round(($done / $total) * 100) : 0;
            $enrollment->setAttribute('progress_percentage', $percent);
        }
    }

    /**
     * Inscription à une formation
     */
    public function enroll(Request $request, Formation $formation)
    {
        $user = auth()->user();
        $result = $this->enrollmentService->enroll($user, $formation);

        return $this->enrollmentService->redirectAfterEnroll(
            $formation,
            $result['enrollment'],
            $user,
            $result['message'],
            $result['created'] ? 'success' : 'info',
        );
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

        if (! $enrollment || (! $enrollment->isActive() && ! $enrollment->isCompleted())) {
            return redirect()->route('formation.show', $formation->slug)
                ->with('info', 'Inscrivez-vous pour accéder au contenu complet.');
        }

        $modules = $formation->publishedModules()
            ->with(['publishedLessons' => function ($q) use ($user) {
                $q->with(['progresses' => function ($pq) use ($user) {
                    $pq->where('user_id', $user->id);
                }]);
            }])
            ->get();

        $progress = $this->completionService->progressFor($user, $formation);
        $certificate = $this->completionService->tryFinalize($user, $formation)
            ?? $enrollment->certificate;

        $enrollment->refresh();

        return view('membres.formation-show', compact(
            'formation',
            'modules',
            'enrollment',
            'certificate'
        ) + [
            'progressPercent' => $progress['percent'],
            'completedLessons' => $progress['completed'],
            'totalLessons' => $progress['total'],
        ]);
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

        $lesson->load('publishedQuiz');

        if ($lesson->hasQuiz()) {
            return redirect()->route('learning.quiz', [$formation->slug, $lesson]);
        }

        // Vérifier l'inscription active
        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->whereIn('status', [FormationEnrollment::STATUS_ACTIVE, FormationEnrollment::STATUS_COMPLETED])
            ->first();

        if (! $enrollment) {
            return redirect()->route('formation.show', $formation->slug)
                ->with('error', 'Accès réservé aux apprenants inscrits.');
        }

        $progress = FormationProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'time_spent_seconds' => 0,
            ]
        );

        $currentModule = $lesson->module;
        [$prevLesson, $nextLesson] = $this->adjacentLessons($formation, $lesson);

        $formation->load([
            'publishedModules.publishedLessons' => fn ($q) => $q->with(['progresses' => fn ($pq) => $pq->where('user_id', $user->id)]),
        ]);

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

        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->whereIn('status', [FormationEnrollment::STATUS_ACTIVE, FormationEnrollment::STATUS_COMPLETED])
            ->first();

        if (! $enrollment) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        FormationProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
                'time_spent_seconds' => $request->input('time_spent', 0),
            ]
        );

        $progress = $this->completionService->progressFor($user, $formation);
        $certificate = $this->completionService->tryFinalize($user, $formation);
        [, $nextLesson] = $this->adjacentLessons($formation, $lesson);

        $redirectUrl = null;
        if ($certificate) {
            $redirectUrl = route('learning.certificate.download', $formation->slug);
        } elseif ($nextLesson) {
            $redirectUrl = route($nextLesson->learningRouteName(), [$formation->slug, $nextLesson]);
        } elseif ($progress['percent'] === 100) {
            $redirectUrl = route('learning.show', $formation->slug);
        }

        return response()->json([
            'success' => true,
            'progress_percent' => $progress['percent'],
            'completed' => $progress['percent'] === 100,
            'certificate_issued' => $certificate !== null,
            'certificate_url' => $certificate
                ? route('learning.certificate.download', $formation->slug)
                : null,
            'redirect_url' => $redirectUrl,
            'next_lesson_title' => $nextLesson?->title,
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

    /** @return \Illuminate\Support\Collection<int, FormationLesson> */
    private function orderedLessonsFor(Formation $formation)
    {
        return FormationLesson::query()
            ->where('formation_lessons.is_published', true)
            ->whereHas('module', fn ($q) => $q->where('formation_id', $formation->id)->where('is_published', true))
            ->join('formation_modules', 'formation_modules.id', '=', 'formation_lessons.module_id')
            ->orderBy('formation_modules.order')
            ->orderBy('formation_lessons.order')
            ->select('formation_lessons.*')
            ->get();
    }

    /** @return array{0: ?FormationLesson, 1: ?FormationLesson} */
    private function adjacentLessons(Formation $formation, FormationLesson $lesson): array
    {
        $allLessons = $this->orderedLessonsFor($formation);
        $currentIndex = $allLessons->search(fn ($l) => $l->id === $lesson->id);

        if ($currentIndex === false) {
            return [null, null];
        }

        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        return [$prevLesson, $nextLesson];
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

        $certificate = $attempt->is_passed
            ? $this->completionService->tryFinalize($user, $formation)
            : null;

        return response()->json([
            'success' => true,
            'attempt' => [
                'score' => $attempt->score,
                'max_score' => $attempt->max_score,
                'percentage' => $attempt->percentage,
                'is_passed' => $attempt->is_passed,
            ],
            'certificate_issued' => $certificate !== null,
            'certificate_url' => $certificate
                ? route('learning.certificate.download', $formation->slug)
                : null,
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

        $certificate = $this->completionService->tryFinalize($user, $formation)
            ?? FormationEnrollment::where('user_id', $user->id)
                ->where('formation_id', $formation->id)
                ->first()
                ?->certificate;

        return view('membres.quiz-results', compact(
            'formation',
            'lesson',
            'quiz',
            'attempt',
            'certificate'
        ));
    }

    public function certificate(Formation $formation)
    {
        $certificate = $this->findUserCertificate($formation);

        return $this->certificateService->inlineResponse($certificate);
    }

    public function downloadCertificate(Formation $formation)
    {
        $certificate = $this->findUserCertificate($formation);

        return $this->certificateService->downloadResponse($certificate);
    }

    private function findUserCertificate(Formation $formation)
    {
        $user = auth()->user();

        $certificate = FormationCertificate::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->first();

        if (! $certificate) {
            abort(404, 'Certificat non disponible. Terminez toutes les leçons de la formation.');
        }

        return $certificate;
    }
}
