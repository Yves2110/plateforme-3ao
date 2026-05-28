<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Support\ActualiteCategories;
use App\Support\PublicContentGate;
use Illuminate\Http\Request;

class ActualitesController extends Controller
{
    public function index(Request $request)
    {
        $canManage = PublicContentGate::can(['publier-actualites', 'administrer-utilisateurs']);
        $categoryFilter = ActualiteCategories::parseFilterInput($request->categories);

        $actualites = Actualite::query()
            ->when(! $canManage, fn ($q) => $q->where('is_published', true))
            ->when($categoryFilter !== [], fn ($q) => $q->whereIn(
                'category',
                ActualiteCategories::storageValuesForFilter($categoryFilter)
            ))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('actualites.index', compact('actualites', 'canManage', 'categoryFilter'));
    }

    public function show(string $slug)
    {
        $canManage = PublicContentGate::can(['publier-actualites', 'administrer-utilisateurs']);

        $actualite = Actualite::where('slug', $slug)
            ->when(! $canManage, fn ($q) => $q->where('is_published', true))
            ->firstOrFail();

        $related = Actualite::where('is_published', true)
            ->where('id', '!=', $actualite->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('actualites.show', compact('actualite', 'related', 'canManage'));
    }
}
