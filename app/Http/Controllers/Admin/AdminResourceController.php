<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminResourceController extends Controller
{
    public function index(Request $request)
    {
        $ressources = Resource::with('uploader')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.ressources.index', compact('ressources'));
    }

    public function create()
    {
        return view('admin.ressources.form', ['ressource' => new Resource(), 'action' => 'create']);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:pdf,video,article,guide,rapport',
            'language'     => 'required|string|max:10',
            'abstract'     => 'nullable|string',
            'file_path'    => 'nullable|mimes:pdf|max:20480',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_validated' => 'boolean',
        ]);

        $data['slug']         = Str::slug($data['title']) . '-' . Str::random(5);
        $data['user_id']      = auth()->id();
        $data['is_validated'] = $request->boolean('is_validated');

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $uploader->storePdf($request->file('file_path'), 'ressources');
        }
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'ressources/thumbnails');
        }

        Resource::create($data);

        return redirect()->route('admin.ressources.index')->with('success', 'Ressource créée.');
    }

    public function edit(Resource $ressource)
    {
        return view('admin.ressources.form', ['ressource' => $ressource, 'action' => 'edit']);
    }

    public function update(Request $request, Resource $ressource, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:pdf,video,article,guide,rapport',
            'language'     => 'required|string|max:10',
            'abstract'     => 'nullable|string',
            'file_path'    => 'nullable|mimes:pdf|max:20480',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_validated' => 'boolean',
        ]);

        $data['is_validated'] = $request->boolean('is_validated');

        if ($request->hasFile('file_path')) {
            $uploader->delete($ressource->file_path);
            $data['file_path'] = $uploader->storePdf($request->file('file_path'), 'ressources');
        }
        if ($request->hasFile('thumbnail')) {
            $uploader->delete($ressource->thumbnail);
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'ressources/thumbnails');
        }

        $ressource->update($data);

        return redirect()->route('admin.ressources.index')->with('success', 'Ressource mise à jour.');
    }

    public function destroy(Resource $ressource, UploadService $uploader)
    {
        $uploader->delete($ressource->file_path);
        $uploader->delete($ressource->thumbnail);
        $ressource->delete();
        return back()->with('success', 'Ressource supprimée.');
    }
}
