<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Event;
use App\Models\Resource;
use App\Services\HomePageService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected HomePageService $homePage
    ) {}

    public function index()
    {
        $actualites = Actualite::where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $ressources = Resource::where('is_validated', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $evenements = Event::where('is_validated', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $heroSlides       = $this->homePage->heroSlides();
        $urgentHeroEvents = $this->homePage->eventsStartingWithinDays(7)->take(3)->get();
        $partners         = $this->homePage->homePartners();
        $stats      = $this->homePage->platformStats();
        $statsLinks = $this->homePage->statsLinks();
        return view('home', compact(
            'actualites',
            'ressources',
            'evenements',
            'heroSlides',
            'urgentHeroEvents',
            'partners',
            'stats',
            'statsLinks'
        ));
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
