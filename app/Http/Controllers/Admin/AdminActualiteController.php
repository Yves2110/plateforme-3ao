<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actualite;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminActualiteController extends Controller
{
    public function index(Request $request)
    {
        $actualites = Actualite::with('author')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.actualites.index', compact('actualites'));
    }

    public function create()
    {
        return view('admin.actualites.form', ['actualite' => new Actualite(), 'action' => 'create']);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'content'      => 'required|string',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'boolean',
        ]);

        $data['slug']         = Str::slug($data['title']) . '-' . Str::random(5);
        $data['user_id']      = auth()->id();
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'actualites/thumbnails');
        }

        Actualite::create($data);

        return redirect()->route('admin.actualites.index')->with('success', 'Actualité créée.');
    }

    public function edit(Actualite $actualite)
    {
        return view('admin.actualites.form', ['actualite' => $actualite, 'action' => 'edit']);
    }

    public function update(Request $request, Actualite $actualite, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'content'      => 'required|string',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'boolean',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && !$actualite->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            $uploader->delete($actualite->thumbnail);
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'actualites/thumbnails');
        }

        $actualite->update($data);

        return redirect()->route('admin.actualites.index')->with('success', 'Actualité mise à jour.');
    }

    public function destroy(Actualite $actualite, UploadService $uploader)
    {
        $uploader->delete($actualite->thumbnail);
        $actualite->delete();
        return back()->with('success', 'Actualité supprimée.');
    }
}
