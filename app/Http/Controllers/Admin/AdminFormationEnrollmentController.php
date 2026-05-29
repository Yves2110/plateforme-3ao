<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\FormationEnrollment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminFormationEnrollmentController extends Controller
{
    public function index(Formation $formation)
    {
        $enrollments = FormationEnrollment::with('user')
            ->where('formation_id', $formation->id)
            ->latest('created_at')
            ->paginate(20);

        $counts = [
            'pending' => FormationEnrollment::where('formation_id', $formation->id)
                ->where('status', FormationEnrollment::STATUS_PENDING)->count(),
            'active' => FormationEnrollment::where('formation_id', $formation->id)
                ->where('status', FormationEnrollment::STATUS_ACTIVE)->count(),
            'completed' => FormationEnrollment::where('formation_id', $formation->id)
                ->where('status', FormationEnrollment::STATUS_COMPLETED)->count(),
        ];

        return view('admin.formations.enrollments.index', compact('formation', 'enrollments', 'counts'));
    }

    public function activate(Formation $formation, FormationEnrollment $enrollment)
    {
        abort_unless($enrollment->formation_id === $formation->id, 404);

        if ($enrollment->isPending()) {
            $enrollment->activate();
        }

        return back()->with('success', 'Inscription activée — l\'apprenant peut accéder au contenu.');
    }

    public function cancel(Formation $formation, FormationEnrollment $enrollment)
    {
        abort_unless($enrollment->formation_id === $formation->id, 404);

        $enrollment->update(['status' => FormationEnrollment::STATUS_CANCELLED]);

        return back()->with('success', 'Inscription annulée.');
    }

    public function export(Formation $formation): StreamedResponse
    {
        $slug = str($formation->slug)->slug('_');
        $filename = 'inscriptions-' . $slug . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($formation) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['Formation', $formation->title], ';');
            fputcsv($handle, ['Type', ucfirst($formation->type)], ';');
            fputcsv($handle, ['Organisateur', $formation->organizer ?? ''], ';');
            fputcsv($handle, ['Date d\'export', now()->format('d/m/Y H:i')], ';');
            fputcsv($handle, [], ';');

            fputcsv($handle, [
                'N°',
                'ID inscription',
                'Nom complet',
                'Adresse e-mail',
                'Organisation',
                'Pays',
                'Statut',
                'Date d\'inscription',
                'Date de complétion',
                'Progression (%)',
                'Montant payé (FCFA)',
                'N° certificat',
                'Date du certificat',
            ], ';');

            $rowNumber = 0;

            FormationEnrollment::query()
                ->with(['user', 'certificate'])
                ->where('formation_id', $formation->id)
                ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'active' THEN 2 WHEN 'completed' THEN 3 WHEN 'cancelled' THEN 4 ELSE 5 END")
                ->orderBy('enrolled_at')
                ->orderBy('id')
                ->chunk(100, function ($enrollments) use ($handle, &$rowNumber) {
                    foreach ($enrollments as $enrollment) {
                        $rowNumber++;
                        $user = $enrollment->user;
                        $certificate = $enrollment->certificate;

                        fputcsv($handle, [
                            $rowNumber,
                            $enrollment->id,
                            $user?->name ?? '',
                            $user?->email ?? '',
                            $user?->organization ?? '',
                            $user?->country ?? '',
                            $enrollment->statusLabel(),
                            $enrollment->enrolled_at?->format('d/m/Y H:i') ?? '',
                            $enrollment->completed_at?->format('d/m/Y H:i') ?? '',
                            $enrollment->progress_percentage,
                            $enrollment->paid_amount !== null
                                ? number_format((float) $enrollment->paid_amount, 0, ',', ' ')
                                : '',
                            $certificate?->certificate_number ?? '',
                            $certificate?->issued_at?->format('d/m/Y H:i') ?? '',
                        ], ';');
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
