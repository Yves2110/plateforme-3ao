<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actualite;
use App\Models\Resource;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $actualites = Actualite::where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $ressources = Resource::where('is_validated', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $evenements = Event::where('is_validated', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        return view('home', compact('actualites', 'ressources', 'evenements'));
    }

    public function widgetNews(Request $request)
    {
        $limit = min((int) $request->get('limit', 5), 10);

        $news = Actualite::where('is_published', true)
            ->latest('published_at')
            ->take($limit)
            ->get(['id', 'title', 'slug', 'category', 'thumbnail', 'published_at']);

        return response()->json($news);
    }

    public function widgetEvents(Request $request)
    {
        $limit = min((int) $request->get('limit', 5), 10);

        $events = Event::where('is_validated', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take($limit)
            ->get(['id', 'title', 'slug', 'type', 'start_date', 'location', 'country', 'is_online']);

        return response()->json($events);
    }
}
