<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Support\PublicContentGate;
use Illuminate\Http\Request;

class BibliothequeController extends Controller
{
    public function index(Request $request)
    {
        $canManage = PublicContentGate::can(['publier-bibliotheque', 'administrer-utilisateurs']);

        $query = Resource::query()->when(! $canManage, fn ($q) => $q->where('is_validated', true));

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%{$q}%")
                   ->orWhere('abstract', 'like', "%{$q}%")
                   ->orWhere('author', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('theme')) {
            $query->whereHas('tags', fn($t) => $t->where('name', $request->theme));
        }

        if ($request->filled('langue')) {
            $query->where('language', $request->langue);
        }

        $ressources = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('bibliotheque.index', compact('ressources', 'canManage'));
    }

    public function show(string $slug)
    {
        $canManage = PublicContentGate::can(['publier-bibliotheque', 'administrer-utilisateurs']);

        $ressource = Resource::with('tags')
            ->where('slug', $slug)
            ->when(! $canManage, fn ($q) => $q->where('is_validated', true))
            ->firstOrFail();

        $related = Resource::where('is_validated', true)
            ->where('id', '!=', $ressource->id)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('bibliotheque.show', compact('ressource', 'related', 'canManage'));
    }
}
