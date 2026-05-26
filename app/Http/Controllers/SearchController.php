<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Actualite;
use App\Models\Event;
use App\Models\ForumThread;
use App\Models\Media;
use App\Models\Resource;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $results = collect();

        if (strlen($q) >= 2) {
            $term = '%' . $q . '%';

            $results = $results
                ->merge($this->searchResources($term))
                ->merge($this->searchActualites($term))
                ->merge($this->searchEvents($term))
                ->merge($this->searchActors($term))
                ->merge($this->searchForum($term))
                ->merge($this->searchMedia($term));
        }

        return view('search.index', compact('q', 'results'));
    }

    public function suggest(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $term = '%' . $q . '%';
        $suggestions = collect()
            ->merge(Resource::where('is_validated', true)->where('title', 'like', $term)->take(3)->pluck('title'))
            ->merge(Actor::where('is_validated', true)->where('name', 'like', $term)->take(3)->pluck('name'))
            ->merge(Actualite::where('is_published', true)->where('title', 'like', $term)->take(3)->pluck('title'))
            ->unique()
            ->take(8)
            ->values();

        return response()->json($suggestions);
    }

    protected function searchResources(string $term)
    {
        return Resource::where('is_validated', true)
            ->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('abstract', 'like', $term))
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                '_type' => 'ressource',
                'title' => $r->title,
                'slug'  => $r->slug,
                'excerpt' => $r->abstract,
            ]);
    }

    protected function searchActualites(string $term)
    {
        return Actualite::where('is_published', true)
            ->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('content', 'like', $term))
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                '_type' => 'actualite',
                'title' => $r->title,
                'slug'  => $r->slug,
                'excerpt' => strip_tags($r->content),
            ]);
    }

    protected function searchEvents(string $term)
    {
        return Event::where('is_validated', true)
            ->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('description', 'like', $term))
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                '_type' => 'evenement',
                'title' => $r->title,
                'slug'  => $r->slug,
                'excerpt' => $r->description,
            ]);
    }

    protected function searchActors(string $term)
    {
        return Actor::where('is_validated', true)
            ->where(fn ($query) => $query
                ->where('name', 'like', $term)
                ->orWhere('description', 'like', $term)
                ->orWhere('country', 'like', $term)
                ->orWhere('city', 'like', $term))
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                '_type' => 'acteur',
                'title' => $r->name,
                'slug'  => $r->slug,
                'excerpt' => strip_tags($r->description ?? ''),
            ]);
    }

    protected function searchForum(string $term)
    {
        return ForumThread::where('is_validated', true)
            ->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('body', 'like', $term))
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                '_type' => 'forum',
                'title' => $r->title,
                'slug'  => $r->slug,
                'category' => $r->category,
                'excerpt' => strip_tags($r->body ?? ''),
            ]);
    }

    protected function searchMedia(string $term)
    {
        return Media::where('is_published', true)
            ->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('description', 'like', $term))
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                '_type' => 'multimedia',
                'title' => $r->title,
                'slug'  => $r->slug,
                'excerpt' => strip_tags($r->description ?? ''),
            ]);
    }
}
