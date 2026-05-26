<?php

namespace App\Services;

use App\Models\RssFeedItem;
use App\Models\RssSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RssFetcherService
{
    public function fetchSource(RssSource $source): int
    {
        if (! $source->is_active) {
            return 0;
        }

        $response = Http::timeout(20)
            ->withHeaders(['User-Agent' => 'Plateforme3AO-RSS/1.0'])
            ->get($source->url);

        if (! $response->successful()) {
            return 0;
        }

        $xml = @simplexml_load_string($response->body());
        if ($xml === false) {
            return 0;
        }

        $items = $xml->channel->item ?? $xml->item ?? [];
        $imported = 0;

        foreach ($items as $item) {
            $guid = (string) ($item->guid ?? $item->link ?? $item->title);
            if ($guid === '') {
                continue;
            }

            $exists = RssFeedItem::where('rss_source_id', $source->id)
                ->where('guid', $guid)
                ->exists();

            if ($exists) {
                continue;
            }

            RssFeedItem::create([
                'rss_source_id' => $source->id,
                'guid'          => Str::limit($guid, 500, ''),
                'title'         => Str::limit(strip_tags((string) $item->title), 255, ''),
                'link'          => Str::limit((string) $item->link, 500, ''),
                'description'   => Str::limit(strip_tags((string) ($item->description ?? '')), 5000, ''),
                'published_at'  => isset($item->pubDate) ? date('Y-m-d H:i:s', strtotime((string) $item->pubDate)) : now(),
                'status'        => 'pending',
            ]);

            $imported++;
        }

        $source->update(['last_fetched_at' => now()]);

        return $imported;
    }

    public function fetchAll(): int
    {
        return RssSource::where('is_active', true)
            ->get()
            ->sum(fn (RssSource $source) => $this->fetchSource($source));
    }
}
