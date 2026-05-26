<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Actor;
use App\Models\Event;
use App\Models\ForumThread;
use App\Models\Resource;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $staticUrls = collect([
            ['url' => url('/'),                          'priority' => '1.0',  'freq' => 'daily'],
            ['url' => route('actualites.index'),         'priority' => '0.9',  'freq' => 'daily'],
            ['url' => route('bibliotheque.index'),       'priority' => '0.9',  'freq' => 'weekly'],
            ['url' => route('evenements.index'),         'priority' => '0.8',  'freq' => 'weekly'],
            ['url' => route('carte.index'),              'priority' => '0.8',  'freq' => 'monthly'],
            ['url' => route('forum.index'),              'priority' => '0.7',  'freq' => 'daily'],
            ['url' => route('multimedia.index'),         'priority' => '0.7',  'freq' => 'weekly'],
        ]);

        $actualites = Actualite::where('is_published', true)
            ->select('slug', 'updated_at')
            ->latest('published_at')->get()
            ->map(fn($a) => [
                'url'      => route('actualites.show', $a->slug),
                'lastmod'  => $a->updated_at->toAtomString(),
                'priority' => '0.8',
                'freq'     => 'monthly',
            ]);

        $ressources = Resource::where('is_validated', true)
            ->select('slug', 'updated_at')
            ->latest()->get()
            ->map(fn($r) => [
                'url'      => route('bibliotheque.show', $r->slug),
                'lastmod'  => $r->updated_at->toAtomString(),
                'priority' => '0.7',
                'freq'     => 'monthly',
            ]);

        $events = Event::where('is_validated', true)
            ->where('start_date', '>=', now()->subMonths(3))
            ->select('slug', 'updated_at')
            ->get()
            ->map(fn($e) => [
                'url'      => route('evenements.show', $e->slug),
                'lastmod'  => $e->updated_at->toAtomString(),
                'priority' => '0.7',
                'freq'     => 'monthly',
            ]);

        $threads = ForumThread::where('is_validated', true)
            ->select('slug', 'category', 'updated_at')
            ->latest('last_reply_at')->limit(200)->get()
            ->map(fn($t) => [
                'url'      => route('thread.show', [$t->category, $t->slug]),
                'lastmod'  => $t->updated_at->toAtomString(),
                'priority' => '0.5',
                'freq'     => 'weekly',
            ]);

        $urls = $staticUrls
            ->merge($actualites)
            ->merge($ressources)
            ->merge($events)
            ->merge($threads);

        $content = view('sitemap', compact('urls'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
