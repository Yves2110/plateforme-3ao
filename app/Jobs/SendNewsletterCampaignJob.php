<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public readonly int $campaignId,
    ) {}

    public function handle(): void
    {
        $campaign = NewsletterCampaign::with('items')->find($this->campaignId);

        if (! $campaign) {
            return;
        }

        if ($campaign->status !== NewsletterCampaign::STATUS_SENDING) {
            return;
        }

        $subscribers = NewsletterSubscriber::active()->get();
        $success = 0;
        $failed = 0;
        $lastError = null;
        $sendSynchronously = config('newsletter.send_mails_sync', true)
            || config('queue.default') === 'sync';

        foreach ($subscribers as $subscriber) {
            try {
                $mailable = new NewsletterCampaignMail($campaign, $subscriber);

                if ($sendSynchronously) {
                    Mail::to($subscriber->email)->send($mailable);
                } else {
                    Mail::to($subscriber->email)->queue($mailable);
                }

                $success++;
            } catch (Throwable $e) {
                $failed++;
                $lastError = $e->getMessage();
                Log::channel('security')->error('newsletter.send_failed', [
                    'campaign_id' => $campaign->id,
                    'email'       => $subscriber->email,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        $total = $subscribers->count();

        if ($total === 0) {
            $campaign->update([
                'status'             => NewsletterCampaign::STATUS_FAILED,
                'last_error'         => 'Aucun abonné actif.',
                'sent_success_count' => 0,
                'sent_failed_count'  => 0,
                'recipients_count'   => 0,
            ]);

            return;
        }

        $campaign->update([
            'status'             => $failed === $total ? NewsletterCampaign::STATUS_FAILED : NewsletterCampaign::STATUS_SENT,
            'sent_at'            => now(),
            'recipients_count'   => $total,
            'sent_success_count' => $success,
            'sent_failed_count'  => $failed,
            'last_error'         => $failed > 0 ? $lastError : null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $campaign = NewsletterCampaign::find($this->campaignId);

        if ($campaign && $campaign->status === NewsletterCampaign::STATUS_SENDING) {
            $campaign->update([
                'status'     => NewsletterCampaign::STATUS_FAILED,
                'last_error' => $exception?->getMessage() ?? 'Échec de l\'envoi.',
            ]);
        }
    }
}
