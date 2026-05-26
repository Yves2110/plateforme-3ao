<?php

namespace App\Services;

use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;

class NewsletterCampaignSender
{
    public function __construct(
        protected NewsletterContentBuilder $contentBuilder,
    ) {}

    public function dispatch(NewsletterCampaign $campaign): void
    {
        if (! in_array($campaign->status, [
            NewsletterCampaign::STATUS_DRAFT,
            NewsletterCampaign::STATUS_SCHEDULED,
        ], true)) {
            throw new \InvalidArgumentException('Cette campagne ne peut plus être envoyée.');
        }

        $activeCount = NewsletterSubscriber::active()->count();

        if ($activeCount === 0) {
            throw new \InvalidArgumentException('Aucun abonné actif pour envoyer la newsletter.');
        }

        $campaign->update([
            'status'             => NewsletterCampaign::STATUS_SENDING,
            'sent_success_count' => 0,
            'sent_failed_count'  => 0,
            'last_error'         => null,
        ]);

        if (config('newsletter.dispatch_sync', true)) {
            SendNewsletterCampaignJob::dispatchSync($campaign->id);
        } else {
            SendNewsletterCampaignJob::dispatch($campaign->id);
        }
    }

    public function recoverStuckCampaigns(): int
    {
        $minutes = config('newsletter.stuck_minutes', 15);

        $stuck = NewsletterCampaign::query()
            ->where('status', NewsletterCampaign::STATUS_SENDING)
            ->where('updated_at', '<', now()->subMinutes($minutes))
            ->get();

        foreach ($stuck as $campaign) {
            SendNewsletterCampaignJob::dispatchSync($campaign->id);
        }

        return $stuck->count();
    }

    public function processDueScheduled(): int
    {
        $this->recoverStuckCampaigns();

        $due = NewsletterCampaign::query()
            ->where('status', NewsletterCampaign::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($due as $campaign) {
            try {
                $this->dispatch($campaign);
            } catch (\InvalidArgumentException) {
                // skip empty lists or invalid state
            }
        }

        return $due->count();
    }
}
