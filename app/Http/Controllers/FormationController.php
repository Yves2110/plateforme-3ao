<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Support\PublicContentGate;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index(Request $request)
    {
        $canManage = PublicContentGate::can(['gerer-formations', 'administrer-utilisateurs']);

        $listQuery = Formation::query()->when(! $canManage, fn ($q) => $q->validated());

        $formations = (clone $listQuery)
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->country, fn ($q) => $q->where('country', $request->country))
            ->when($request->language, fn ($q) => $q->where('language', $request->language))
            ->when($request->q, fn ($q) => $q->where('title', 'like', "%{$request->q}%")
                ->orWhere('organizer', 'like', "%{$request->q}%"))
            ->orderByRaw('start_date IS NULL, start_date ASC')
            ->paginate(12)
            ->withQueryString();

        $types     = (clone $listQuery)->distinct()->pluck('type');
        $countries = (clone $listQuery)->whereNotNull('country')->distinct()->pluck('country')->sort()->values();

        return view('formation.index', compact('formations', 'types', 'countries', 'canManage'));
    }

    public function show(string $slug)
    {
        $canManage = PublicContentGate::can(['gerer-formations', 'administrer-utilisateurs']);

        $formation = Formation::query()
            ->when(! $canManage, fn ($q) => $q->validated())
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Formation::query()
            ->when(! $canManage, fn ($q) => $q->validated())
            ->where('id', '!=', $formation->id)
            ->where(fn($q) => $q->where('type', $formation->type)
                                ->orWhere('country', $formation->country))
            ->limit(3)->get();

        return view('formation.show', compact('formation', 'related', 'canManage'));
    }
}

