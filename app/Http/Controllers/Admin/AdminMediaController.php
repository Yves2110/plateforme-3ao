<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\Media;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminMediaController extends Controller
{
    public function index(Request $request)
    {
        $medias = Media::withCount('photos')
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
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
        $data = $this->validatedMedia($request);

        $data['slug']         = Str::slug($data['title']).'-'.Str::random(5);
        $data['user_id']      = auth()->id();
        $data['is_published'] = $request->boolean('is_published');
        $data['featured_in_gallery'] = $request->boolean('featured_in_gallery');
        $data['gallery_sort_order'] = (int) $request->input('gallery_sort_order', 0);
        $data['published_at'] = $data['is_published'] ? now() : null;

        $data = $this->handleMainFile($request, $data, null, $uploader);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'medias/thumbnails');
        }

        $media = Media::create($data);

        if ($media->type === 'gallery') {
            $this->storeGalleryImages($media, $request, $uploader);
            $this->ensureGalleryThumbnail($media);
        }

        return redirect()->route('admin.medias.index')->with('success', 'Média créé.');
    }

    public function edit(Media $media)
    {
        $media->load('photos');

        return view('admin.medias.form', ['media' => $media, 'action' => 'edit']);
    }

    public function update(Request $request, Media $media, UploadService $uploader)
    {
        $data = $this->validatedMedia($request, $media);

        $data['is_published'] = $request->boolean('is_published');
        $data['featured_in_gallery'] = $request->boolean('featured_in_gallery');
        $data['gallery_sort_order'] = (int) $request->input('gallery_sort_order', 0);
        if ($data['is_published'] && ! $media->published_at) {
            $data['published_at'] = now();
        }

        $previousType = $media->type;
        $data = $this->handleMainFile($request, $data, $media, $uploader);

        if ($request->hasFile('thumbnail')) {
            $uploader->delete($media->thumbnail);
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'medias/thumbnails');
        }

        if ($previousType === 'gallery' && $data['type'] !== 'gallery') {
            $this->deleteAllGalleryPhotos($media, $uploader);
        }

        if ($data['type'] === 'gallery') {
            $data['file_path'] = null;
            if ($media->file_path) {
                $uploader->delete($media->file_path);
            }
        }

        $media->update($data);

        if ($media->type === 'gallery') {
            $this->removeGalleryPhotos($media, $request, $uploader);
            $this->storeGalleryImages($media, $request, $uploader);
            $this->ensureGalleryThumbnail($media->fresh());
            $this->assertGalleryHasPhotos($media->fresh());
        }

        return redirect()->route('admin.medias.index')->with('success', 'Média mis à jour.');
    }

    public function destroy(Media $media, UploadService $uploader)
    {
        $this->deleteAllGalleryPhotos($media, $uploader);
        $uploader->delete($media->file_path);
        $uploader->delete($media->thumbnail);
        $media->delete();

        return back()->with('success', 'Média supprimé.');
    }

    protected function validatedMedia(Request $request, ?Media $media = null): array
    {
        $type = $request->input('type');
        $isGallery = $type === 'gallery';
        $isNew     = $media === null;

        $rules = [
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:photo,video,podcast,gallery',
            'description'  => 'nullable|string',
            'url'          => 'nullable|url',
            'duration'     => 'nullable|string|max:20',
            'source'       => 'nullable|string|max:255',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_published' => 'boolean',
            'featured_in_gallery' => 'boolean',
            'gallery_sort_order' => 'nullable|integer|min:0|max:999',
            'remove_gallery_photos'   => 'nullable|array',
            'remove_gallery_photos.*' => [
                'integer',
                Rule::exists('gallery_photos', 'id')->where('media_id', $media?->id ?? 0),
            ],
            'gallery_images'   => ($isGallery && $isNew) ? 'required|array|min:1|max:30' : 'nullable|array|max:30',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:51200',
        ];

        if ($type === 'photo') {
            $rules['file_path'] = ($isNew && ! $request->hasFile('gallery_images'))
                ? 'required|image|max:51200'
                : 'nullable|image|max:51200';
        } elseif ($type === 'video') {
            $rules['file_path'] = 'nullable|file|mimes:mp4,webm|max:51200';
        } elseif ($type === 'podcast') {
            $rules['file_path'] = 'nullable|file|mimes:mp3,m4a,ogg|max:51200';
        }

        if ($isGallery && ! $isNew) {
            $remaining = $media->photos()->count()
                - count($request->input('remove_gallery_photos', []))
                + count($request->file('gallery_images', []));
            if ($remaining < 1) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'gallery_images' => 'Une galerie doit contenir au moins une image.',
                ]);
            }
        }

        return $request->validate($rules, [
            'gallery_images.required' => 'Ajoutez au moins une image pour créer une galerie.',
            'gallery_images.min'      => 'Ajoutez au moins une image pour créer une galerie.',
            'file_path.required'      => 'Téléversez une image pour une photo.',
        ]);
    }

    protected function handleMainFile(Request $request, array $data, ?Media $media, UploadService $uploader): array
    {
        if ($data['type'] === 'gallery') {
            $data['file_path'] = null;

            return $data;
        }

        if (! $request->hasFile('file_path')) {
            return $data;
        }

        if ($media?->file_path) {
            $uploader->delete($media->file_path);
        }

        $file = $request->file('file_path');
        $mime = $file->getMimeType();

        $data['file_path'] = match ($data['type']) {
            'podcast' => $uploader->storeAudio($file, 'medias/audio'),
            'video'   => $uploader->storeFile($file, 'medias/videos', 'video'),
            default   => $uploader->storeImage($file, 'medias/photos'),
        };

        return $data;
    }

    protected function storeGalleryImages(Media $media, Request $request, UploadService $uploader): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $order = (int) $media->photos()->max('order');

        foreach ($request->file('gallery_images') as $file) {
            $order++;
            $media->photos()->create([
                'file_path' => $uploader->storeImage($file, 'medias/galleries'),
                'order'     => $order,
            ]);
        }
    }

    protected function removeGalleryPhotos(Media $media, Request $request, UploadService $uploader): void
    {
        $ids = $request->input('remove_gallery_photos', []);
        if ($ids === []) {
            return;
        }

        GalleryPhoto::where('media_id', $media->id)
            ->whereIn('id', $ids)
            ->get()
            ->each(function (GalleryPhoto $photo) use ($uploader) {
                $uploader->delete($photo->file_path);
                $photo->delete();
            });
    }

    protected function deleteAllGalleryPhotos(Media $media, UploadService $uploader): void
    {
        $media->photos()->get()->each(function (GalleryPhoto $photo) use ($uploader) {
            $uploader->delete($photo->file_path);
            $photo->delete();
        });
    }

    protected function ensureGalleryThumbnail(Media $media): void
    {
        if ($media->thumbnail) {
            return;
        }

        $first = $media->photos()->orderBy('order')->first();
        if ($first) {
            $media->update(['thumbnail' => $first->file_path]);
        }
    }

    protected function assertGalleryHasPhotos(Media $media): void
    {
        if ($media->photos()->count() === 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'gallery_images' => 'Une galerie doit contenir au moins une image.',
            ]);
        }
    }
}
