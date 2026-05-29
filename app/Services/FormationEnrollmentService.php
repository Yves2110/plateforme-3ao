<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\FormationEnrollment;
use App\Models\FormationLesson;
use App\Models\FormationProgress;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class FormationEnrollmentService
{
    /**
     * @return array{enrollment: FormationEnrollment, created: bool, message: string}
     */
    public function enroll(User $user, Formation $formation): array
    {
        $hasLmsContent = $formation->hasLmsContent();

        $existing = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->first();

        if ($existing) {
            return [
                'enrollment' => $existing,
                'created' => false,
                'message' => 'Vous êtes déjà inscrit à cette formation.',
            ];
        }

        $enrollment = FormationEnrollment::create([
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'status' => $formation->price > 0 ? FormationEnrollment::STATUS_PENDING : FormationEnrollment::STATUS_ACTIVE,
            'paid_amount' => $formation->price > 0 ? 0 : null,
            'enrolled_at' => $formation->price > 0 ? null : now(),
        ]);

        if ($formation->price > 0) {
            $message = 'Inscription enregistrée. Paiement ou validation requis pour accéder au contenu.';
        } elseif ($hasLmsContent) {
            $message = 'Inscription confirmée ! Bienvenue dans le parcours.';
        } elseif ($formation->registration_url) {
            $message = 'Inscription confirmée. Vous allez accéder au lien de la session.';
        } else {
            $message = 'Inscription confirmée sur la plateforme 3AO.';
        }

        return [
            'enrollment' => $enrollment,
            'created' => true,
            'message' => $message,
        ];
    }

    public function redirectAfterEnroll(
        Formation $formation,
        FormationEnrollment $enrollment,
        User $user,
        string $message,
        string $flashKey = 'success',
    ): RedirectResponse {
        $hasLmsContent = $formation->hasLmsContent();

        if ($enrollment->isPending()) {
            return redirect()
                ->route('formation.show', $formation->slug)
                ->with($flashKey, $message);
        }

        if ($hasLmsContent && ($enrollment->isActive() || $enrollment->isCompleted())) {
            return redirect()
                ->to($this->courseEntryUrl($formation, $user))
                ->with($flashKey, $message);
        }

        if ($formation->registration_url) {
            return redirect()
                ->away($formation->registration_url)
                ->with($flashKey, $message);
        }

        return redirect()
            ->route('formation.show', $formation->slug)
            ->with($flashKey, $message);
    }

    public function courseEntryUrl(Formation $formation, User $user): string
    {
        if (! $formation->hasLmsContent()) {
            return route('formation.show', $formation->slug);
        }

        $lessons = $this->orderedLessonsFor($formation);

        if ($lessons->isEmpty()) {
            return route('learning.show', $formation->slug);
        }

        $completedLessonIds = FormationProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');

        $lesson = $lessons->first(fn ($l) => ! $completedLessonIds->contains($l->id)) ?? $lessons->first();

        return route($lesson->learningRouteName(), [$formation->slug, $lesson]);
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
}
