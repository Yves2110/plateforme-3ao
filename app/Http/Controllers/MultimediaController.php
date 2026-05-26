<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;

class MultimediaController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');

        $media = Media::published()
            ->when($type, fn($q) => $q->byType($type))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all'     => Media::published()->count(),
            'photo'   => Media::published()->byType('photo')->count(),
            'video'   => Media::published()->byType('video')->count(),
            'podcast' => Media::published()->byType('podcast')->count(),
            'gallery' => Media::published()->byType('gallery')->count(),
        ];

        return view('multimedia.index', compact('media', 'type', 'counts'));
    }

    public function show(string $slug)
    {
        $item = Media::published()->where('slug', $slug)->firstOrFail();
        $item->increment('views');

        $related = Media::published()
            ->where('type', $item->type)
            ->where('id', '!=', $item->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('multimedia.show', compact('item', 'related'));
    }
}
