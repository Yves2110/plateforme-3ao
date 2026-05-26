<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Event;
use App\Models\ForumThread;
use App\Models\Resource;
use Illuminate\Http\Response;

class RssController extends Controller
{
    public function actualites(): Response
    {
        $items = Actualite::where('is_published', true)
            ->latest('published_at')
            ->limit(30)
            ->get();

        return $this->xmlResponse('rss.actualites', [
            'items'       => $items,
            'feedTitle'   => 'Actualités — Plateforme 3AO',
            'feedDesc'    => 'Dernières actualités agroécologiques en Afrique de l\'Ouest',
            'feedUrl'     => route('rss.actualites'),
        ]);
    }

    public function ressources(): Response
    {
        $items = Resource::where('is_validated', true)
            ->latest()
            ->limit(30)
            ->get();

        return $this->xmlResponse('rss.ressources', [
            'items'       => $items,
            'feedTitle'   => 'Bibliothèque — Plateforme 3AO',
            'feedDesc'    => 'Nouvelles ressources documentaires agroécologiques',
            'feedUrl'     => route('rss.ressources'),
        ]);
    }

    public function evenements(): Response
    {
        $items = Event::where('is_validated', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(30)
            ->get();

        return $this->xmlResponse('rss.evenements', [
            'items'       => $items,
            'feedTitle'   => 'Événements — Plateforme 3AO',
            'feedDesc'    => 'Prochains événements agroécologiques en Afrique de l\'Ouest',
            'feedUrl'     => route('rss.evenements'),
        ]);
    }

    public function forum(): Response
    {
        $items = ForumThread::where('is_validated', true)
            ->with('author')
            ->latest('last_reply_at')
            ->limit(30)
            ->get();

        return $this->xmlResponse('rss.forum', [
            'items'       => $items,
            'feedTitle'   => 'Forum — Plateforme 3AO',
            'feedDesc'    => 'Nouvelles discussions du forum agroécologie',
            'feedUrl'     => route('rss.forum'),
        ]);
    }

    private function xmlResponse(string $view, array $data): Response
    {
        $content = view($view, $data)->render();
        return response($content, 200, ['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }
}
