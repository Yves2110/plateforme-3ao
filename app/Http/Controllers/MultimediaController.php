<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Support\PublicContentGate;
use Illuminate\Http\Request;

class MultimediaController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        $canManage = PublicContentGate::can(['contribuer-multimedia', 'administrer-utilisateurs']);

        $baseQuery = fn () => Media::query()->when(! $canManage, fn ($q) => $q->published());

        $media = $baseQuery()
            ->when($type, fn ($q) => $q->byType($type))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all'     => $baseQuery()->count(),
            'photo'   => $baseQuery()->byType('photo')->count(),
            'video'   => $baseQuery()->byType('video')->count(),
            'podcast' => $baseQuery()->byType('podcast')->count(),
            'gallery' => $baseQuery()->byType('gallery')->count(),
        ];

        return view('multimedia.index', compact('media', 'type', 'counts', 'canManage'));
    }

    public function show(string $slug)
    {
        $canManage = PublicContentGate::can(['contribuer-multimedia', 'administrer-utilisateurs']);

        $item = Media::query()
            ->with('photos')
            ->when(! $canManage, fn ($q) => $q->published())
            ->where('slug', $slug)
            ->firstOrFail();
        $item->increment('views');

        $related = Media::published()
            ->where('type', $item->type)
            ->where('id', '!=', $item->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('multimedia.show', compact('item', 'related', 'canManage'));
    }
}
