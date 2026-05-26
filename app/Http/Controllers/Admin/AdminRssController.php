<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actualite;
use App\Models\RssFeedItem;
use App\Models\RssSource;
use App\Services\RssFetcherService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRssController extends Controller
{
    protected function authorizeRss(): void
    {
        abort_unless(auth()->user()?->can('gerer-rss'), 403);
    }

    public function index()
    {
        $this->authorizeRss();
        $sources = RssSource::withCount(['items as pending_count' => fn ($q) => $q->where('status', 'pending')])
            ->latest()
            ->get();

        $pendingItems = RssFeedItem::with('source')
            ->where('status', 'pending')
            ->latest('published_at')
            ->paginate(20);

        return view('admin.rss.index', compact('sources', 'pendingItems'));
    }

    public function storeSource(Request $request)
    {
        $this->authorizeRss();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:500',
        ]);

        RssSource::create($data + ['is_active' => true]);

        return back()->with('success', 'Source RSS ajoutée.');
    }

    public function toggleSource(RssSource $source)
    {
        $this->authorizeRss();
        $source->update(['is_active' => ! $source->is_active]);

        return back()->with('success', 'Source mise à jour.');
    }

    public function destroySource(RssSource $source)
    {
        $this->authorizeRss();
        $source->delete();

        return back()->with('success', 'Source supprimée.');
    }

    public function fetch(RssFetcherService $fetcher)
    {
        $this->authorizeRss();
        $count = $fetcher->fetchAll();

        return back()->with('success', "{$count} nouvel(s) élément(s) importé(s).");
    }

    public function approve(RssFeedItem $item)
    {
        $this->authorizeRss();
        if ($item->status !== 'pending') {
            return back();
        }

        $actualite = Actualite::create([
            'title'        => $item->title,
            'slug'         => Str::slug($item->title) . '-' . Str::random(5),
            'content'      => $item->description ?: '<p>Source : <a href="' . e($item->link) . '">' . e($item->link) . '</a></p>',
            'category'     => 'Partenaire',
            'is_published' => true,
            'published_at' => $item->published_at ?? now(),
            'user_id'      => auth()->id(),
        ]);

        $item->update([
            'status'       => 'approved',
            'actualite_id' => $actualite->id,
        ]);

        return back()->with('success', 'Article publié dans les actualités.');
    }

    public function reject(RssFeedItem $item)
    {
        $this->authorizeRss();
        $item->update(['status' => 'rejected']);

        return back()->with('success', 'Élément rejeté.');
    }
}
