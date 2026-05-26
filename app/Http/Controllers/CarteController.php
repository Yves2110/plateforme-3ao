<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\ActorLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CarteController extends Controller
{
    public function index()
    {
        $actorCount = Cache::remember('carte.actors_count', now()->addMinutes(10),
            fn() => Actor::where('is_validated', true)->count());

        $types = Cache::remember('carte.types', now()->addMinutes(10), fn() => Actor::where('is_validated', true)
            ->whereNotNull('type')->distinct()->pluck('type')
            ->filter(fn($t) => is_string($t) && strlen($t) > 0)
            ->sort()->values()->all());

        $countries = Cache::remember('carte.countries', now()->addMinutes(10), fn() => Actor::where('is_validated', true)
            ->whereNotNull('country')->distinct()->pluck('country')
            ->filter(fn($c) => is_string($c) && strlen($c) > 0)
            ->sort()->values()->all());

        $typeColorMap = [
            'ONG' => '#2D6A4F', 'Réseau' => '#D4A017', 'Réseau OP' => '#D4A017',
            'Institution' => '#3B82F6', 'Institution publique' => '#3B82F6',
            'Entreprise' => '#52B788', 'Université' => '#8B5CF6', 'Recherche' => '#8B5CF6',
            'Coopérative' => '#84CC16', 'Fondation' => '#F97316',
            'Organisation paysanne' => '#D4A017', 'OP' => '#D4A017',
        ];

        $legendTypes = collect($types)->mapWithKeys(fn($t) => [
            $t => $typeColorMap[$t] ?? '#52B788',
        ])->all();

        return view('carte.index', compact('actorCount', 'types', 'countries', 'legendTypes'));
    }

    public function acteurs(Request $request)
    {
        $cacheKey = 'carte.actors.' . md5(json_encode($request->only(['type', 'country', 'q'])));

        $actors = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            return Actor::where('is_validated', true)
                ->whereNotNull('lat')->whereNotNull('lng')
                ->when($request->type,    fn($q) => $q->where('type', $request->type))
                ->when($request->country, fn($q) => $q->where('country', $request->country))
                ->when($request->q, function ($q) use ($request) {
                    $term = '%' . $request->q . '%';
                    $q->where(fn($w) => $w->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('city', 'like', $term));
                })
                ->get(['id', 'name', 'type', 'country', 'city', 'region', 'lat', 'lng', 'logo', 'slug', 'description'])
                ->map(fn($a) => [
                    'id'      => $a->id,
                    'slug'    => $a->slug,
                    'name'    => $a->name,
                    'type'    => $a->type,
                    'country' => $a->country,
                    'city'    => $a->city,
                    'lat'     => (float) $a->lat,
                    'lng'     => (float) $a->lng,
                    'logo'    => $a->logo ? asset('storage/' . $a->logo) : null,
                    'excerpt' => $a->description ? \Illuminate\Support\Str::limit(strip_tags($a->description), 120) : null,
                    'url'     => route('carte.acteur', $a->slug),
                ])
                ->values();
        });

        return response()->json($actors)
            ->header('Cache-Control', 'public, max-age=300');
    }

    public function network()
    {
        $actors = Actor::where('is_validated', true)
            ->select('id', 'name', 'type', 'country', 'slug')
            ->get();

        $links = ActorLink::with(['actorFrom:id', 'actorTo:id'])
            ->whereHas('actorFrom', fn($q) => $q->where('is_validated', true))
            ->whereHas('actorTo',   fn($q) => $q->where('is_validated', true))
            ->get();

        // Calcul du nombre de liens par acteur (degré)
        $degrees = [];
        foreach ($links as $link) {
            $degrees[$link->actor_id_from] = ($degrees[$link->actor_id_from] ?? 0) + 1;
            $degrees[$link->actor_id_to]   = ($degrees[$link->actor_id_to]   ?? 0) + 1;
        }

        $nodes = $actors->map(fn($a) => [
            'id'      => $a->id,
            'name'    => $a->name,
            'type'    => $a->type,
            'country' => $a->country,
            'links'   => $degrees[$a->id] ?? 0,
            'url'     => route('carte.acteur', $a->slug),
        ]);

        $linksData = $links->map(fn($l) => [
            'source' => $l->actor_id_from,
            'target' => $l->actor_id_to,
            'type'   => $l->relation_type,
        ]);

        return view('carte.network', [
            'nodesJson' => json_encode($nodes->values()),
            'linksJson' => json_encode($linksData->values()),
            'nodeCount' => $actors->count(),
            'linkCount' => $links->count(),
        ]);
    }

    public function acteur(Request $request, string $slugOrId)
    {
        $actor = is_numeric($slugOrId)
            ? Actor::where('id', $slugOrId)->where('is_validated', true)->firstOrFail()
            : Actor::where('slug', $slugOrId)->where('is_validated', true)->firstOrFail();

        // Si format=html, retourner seulement le contenu principal (sans layout)
        if ($request->query('format') === 'html') {
            return view('carte._acteur-detail', compact('actor'));
        }

        return view('carte.acteur', compact('actor'));
    }
}
