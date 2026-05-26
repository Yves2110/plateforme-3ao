<?php

namespace App\Console\Commands;

use App\Services\NewsletterCampaignSender;
use Illuminate\Console\Command;

class SendScheduledNewslettersCommand extends Command
{
    protected $signature = 'newsletter:send-scheduled';

    protected $description = 'Envoie les campagnes newsletter dont la date programmée est atteinte';

    public function handle(NewsletterCampaignSender $sender): int
    {
        $recovered = $sender->recoverStuckCampaigns();
        $count = $sender->processDueScheduled();

        $this->info("Campagnes programmées déclenchées : {$count}");
        if ($recovered > 0) {
            $this->warn("Campagnes bloquées relancées : {$recovered}");
        }

        return self::SUCCESS;
    }
}
