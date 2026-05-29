<?php

namespace App\Services;

use App\Models\FormationCertificate;
use App\Models\FormationEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormationCertificateService
{
    public function issueForEnrollment(FormationEnrollment $enrollment): FormationCertificate
    {
        $existing = FormationCertificate::where('enrollment_id', $enrollment->id)->first();
        if ($existing) {
            if (! $existing->pdfExists()) {
                $existing->update(['pdf_path' => $this->generateAndStorePdf($existing)]);
            }

            return $existing;
        }

        $enrollment->loadMissing(['user', 'formation']);
        $user = $enrollment->user;
        $formation = $enrollment->formation;

        $certificate = FormationCertificate::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $user->id,
            'formation_id' => $formation->id,
            'certificate_number' => $this->generateCertificateNumber(),
            'learner_name' => $user->name,
            'learner_email' => $user->email,
            'learner_organization' => $user->organization,
            'formation_title' => $formation->title,
            'issued_at' => $enrollment->completed_at ?? now(),
        ]);

        $certificate->update([
            'pdf_path' => $this->generateAndStorePdf($certificate),
        ]);

        return $certificate->fresh();
    }

    public function generateCertificateNumber(): string
    {
        do {
            $number = '3AO-'.now()->format('Y').'-'.strtoupper(Str::random(8));
        } while (FormationCertificate::where('certificate_number', $number)->exists());

        return $number;
    }

    public function generateAndStorePdf(FormationCertificate $certificate): string
    {
        $pdf = Pdf::loadView('certificates.formation', [
            'certificate' => $certificate,
            'logoDataUri' => $this->logoDataUri(),
            'brand' => config('brand'),
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true);

        $relativePath = sprintf(
            'certificates/%s/%s.pdf',
            $certificate->issued_at->format('Y'),
            $certificate->certificate_number
        );

        Storage::disk('local')->put($relativePath, $pdf->output());

        return $relativePath;
    }

    public function downloadResponse(FormationCertificate $certificate)
    {
        if (! $certificate->pdfExists()) {
            $certificate->update(['pdf_path' => $this->generateAndStorePdf($certificate)]);
        }

        $filename = Str::slug($certificate->formation_title).'-certificat-'.$certificate->certificate_number.'.pdf';

        return response()->download(
            $certificate->pdf_full_path,
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function inlineResponse(FormationCertificate $certificate)
    {
        if (! $certificate->pdfExists()) {
            $certificate->update(['pdf_path' => $this->generateAndStorePdf($certificate)]);
        }

        return response()->file(
            $certificate->pdf_full_path,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline']
        );
    }

    public function logoDataUri(): ?string
    {
        $path = public_path(config('brand.logo', 'images/logo-3ao.jpeg'));

        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
