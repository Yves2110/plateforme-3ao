<?php

namespace App\Console\Commands;

use App\Services\RssFetcherService;
use Illuminate\Console\Command;

class FetchRssFeeds extends Command
{
    protected $signature = 'rss:fetch {--source= : ID d\'une source spécifique}';

    protected $description = 'Importe les nouveaux articles depuis les flux RSS partenaires';

    public function handle(RssFetcherService $fetcher): int
    {
        if ($id = $this->option('source')) {
            $source = \App\Models\RssSource::findOrFail($id);
            $count = $fetcher->fetchSource($source);
            $this->info("{$count} nouvel(s) élément(s) importé(s) depuis {$source->name}.");

            return self::SUCCESS;
        }

        $count = $fetcher->fetchAll();
        $this->info("{$count} nouvel(s) élément(s) importé(s) au total.");

        return self::SUCCESS;
    }
}
