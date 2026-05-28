<?php

namespace App\Services;

use App\Mail\NewUserRegistrationMail;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationNotificationService
{
    /**
     * Comptes à alerter lors d'une nouvelle demande d'inscription.
     */
    public function recipients(): Collection
    {
        $byRole = User::role(['super_admin', 'moderateur'])
            ->where('approval_status', 'approved')
            ->get();

        $byPermission = User::permission(['valider-inscriptions', 'administrer-utilisateurs'])
            ->where('approval_status', 'approved')
            ->get();

        return $byRole->merge($byPermission)->unique('id')->values();
    }

    public function notifyNewRegistration(User $applicant): void
    {
        $recipients = $this->recipients();

        if ($recipients->isEmpty()) {
            Log::warning('registration.no_alert_recipients', [
                'user_id' => $applicant->id,
                'email'   => $applicant->email,
            ]);

            return;
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new NewUserRegistrationMail($applicant));
            } catch (\Throwable $e) {
                Log::error('registration.alert_mail_failed', [
                    'recipient_id' => $recipient->id,
                    'applicant_id' => $applicant->id,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $fallback = config('mail.registration_alert_address');
        if ($fallback && ! $recipients->contains(fn (User $u) => $u->email === $fallback)) {
            try {
                Mail::to($fallback)->send(new NewUserRegistrationMail($applicant));
            } catch (\Throwable $e) {
                Log::error('registration.alert_mail_fallback_failed', [
                    'address' => $fallback,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }
}
