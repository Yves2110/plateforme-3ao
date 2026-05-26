<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminMediaController extends Controller
{
    public function index(Request $request)
    {
        $medias = Media::when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.medias.index', compact('medias'));
    }

    public function create()
    {
        return view('admin.medias.form', ['media' => new Media(), 'action' => 'create']);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:photo,video,podcast,gallery',
            'description'  => 'nullable|string',
            'url'          => 'nullable|url',
            'duration'     => 'nullable|string|max:20',
            'source'       => 'nullable|string|max:255',
            'file_path'    => 'nullable|file|mimes:jpg,jpeg,png,webp,mp3,m4a,ogg,mp4|max:51200',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'boolean',
        ]);

        $data['slug']         = Str::slug($data['title']) . '-' . Str::random(5);
        $data['user_id']      = auth()->id();
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $mime = $file->getMimeType();
            if (str_starts_with($mime, 'audio')) {
                $data['file_path'] = $uploader->storeAudio($file, 'medias/audio');
            } else {
                $data['file_path'] = $uploader->storeImage($file, 'medias/photos');
            }
        }
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'medias/thumbnails');
        }

        Media::create($data);

        return redirect()->route('admin.medias.index')->with('success', 'Média créé.');
    }

    public function edit(Media $media)
    {
        return view('admin.medias.form', ['media' => $media, 'action' => 'edit']);
    }

    public function update(Request $request, Media $media, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:photo,video,podcast,gallery',
            'description'  => 'nullable|string',
            'url'          => 'nullable|url',
            'duration'     => 'nullable|string|max:20',
            'source'       => 'nullable|string|max:255',
            'file_path'    => 'nullable|file|mimes:jpg,jpeg,png,webp,mp3,m4a,ogg,mp4|max:51200',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'boolean',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && !$media->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('file_path')) {
            $uploader->delete($media->file_path);
            $file = $request->file('file_path');
            $mime = $file->getMimeType();
            if (str_starts_with($mime, 'audio')) {
                $data['file_path'] = $uploader->storeAudio($file, 'medias/audio');
            } else {
                $data['file_path'] = $uploader->storeImage($file, 'medias/photos');
            }
        }
        if ($request->hasFile('thumbnail')) {
            $uploader->delete($media->thumbnail);
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'medias/thumbnails');
        }

        $media->update($data);

        return redirect()->route('admin.medias.index')->with('success', 'Média mis à jour.');
    }

    public function destroy(Media $media, UploadService $uploader)
    {
        $uploader->delete($media->file_path);
        $uploader->delete($media->thumbnail);
        $media->delete();
        return back()->with('success', 'Média supprimé.');
    }
}
