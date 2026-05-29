<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\FormationCertificate;
use App\Models\FormationEnrollment;
use App\Models\FormationProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FormationCompletionService
{
    public function __construct(
        private FormationCertificateService $certificateService,
    ) {}

    /**
     * @return array{total: int, completed: int, percent: int}
     */
    public function progressFor(User $user, Formation $formation): array
    {
        $total = $formation->total_lessons_count;
        $completed = FormationProgress::where('user_id', $user->id)
            ->whereHas('lesson.module', fn ($q) => $q->where('formation_id', $formation->id))
            ->whereNotNull('completed_at')
            ->count();

        $percent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return compact('total', 'completed', 'percent');
    }

    /**
     * Finalise l'inscription à 100 % et délivre le certificat si éligible.
     */
    public function tryFinalize(User $user, Formation $formation): ?FormationCertificate
    {
        if (! $formation->hasLmsContent()) {
            return null;
        }

        $enrollment = FormationEnrollment::where('user_id', $user->id)
            ->where('formation_id', $formation->id)
            ->whereIn('status', [
                FormationEnrollment::STATUS_ACTIVE,
                FormationEnrollment::STATUS_COMPLETED,
            ])
            ->first();

        if (! $enrollment) {
            return null;
        }

        $progress = $this->progressFor($user, $formation);
        if ($progress['percent'] < 100) {
            return null;
        }

        return DB::transaction(function () use ($enrollment) {
            if (! $enrollment->isCompleted()) {
                $enrollment->complete();
            }

            return $this->certificateService->issueForEnrollment(
                $enrollment->fresh(['user', 'formation'])
            );
        });
    }
}
