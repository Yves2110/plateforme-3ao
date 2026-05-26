<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actualite;

class ActualitesController extends Controller
{
    public function index()
    {
        $actualites = Actualite::where('is_published', true)
            ->latest('published_at')
            ->paginate(12);

        return view('actualites.index', compact('actualites'));
    }

    public function show(string $slug)
    {
        $actualite = Actualite::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $related = Actualite::where('is_published', true)
            ->where('id', '!=', $actualite->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('actualites.show', compact('actualite', 'related'));
    }
}
