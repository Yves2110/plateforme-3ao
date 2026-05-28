<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Tag;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminResourceController extends Controller
{
    public function index(Request $request)
    {
        $ressources = Resource::with(['uploader', 'tags'])
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ressources.index', compact('ressources'));
    }

    public function create()
    {
        return view('admin.ressources.form', [
            'ressource' => new Resource(),
            'action'    => 'create',
        ]);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $this->validated($request);

        $data['slug']         = Str::slug($data['title']).'-'.Str::random(5);
        $data['user_id']      = auth()->id();
        $data['is_validated'] = $request->boolean('is_validated');
        $data['published_at'] = now();

        $theme = $data['theme'] ?? null;
        unset($data['theme']);

        $data = $this->handleUploads($request, $data, null, $uploader);

        $ressource = Resource::create($data);
        $this->syncTheme($ressource, $theme);

        return redirect()->route('admin.ressources.index')->with('success', 'Ressource créée.');
    }

    public function edit(Resource $ressource)
    {
        $ressource->load('tags');

        return view('admin.ressources.form', [
            'ressource' => $ressource,
            'action'    => 'edit',
        ]);
    }

    public function update(Request $request, Resource $ressource, UploadService $uploader)
    {
        $data = $this->validated($request);

        $data['is_validated'] = $request->boolean('is_validated');

        $theme = $data['theme'] ?? null;
        unset($data['theme']);

        $data = $this->handleUploads($request, $data, $ressource, $uploader);

        $ressource->update($data);
        $this->syncTheme($ressource, $theme);

        return redirect()->route('admin.ressources.index')->with('success', 'Ressource mise à jour.');
    }

    public function toggleValidation(Resource $ressource)
    {
        $ressource->update(['is_validated' => ! $ressource->is_validated]);

        $message = $ressource->is_validated
            ? 'Ressource publiée sur la bibliothèque publique.'
            : 'Ressource retirée de la bibliothèque publique.';

        return back()->with('success', $message);
    }

    public function destroy(Resource $ressource, UploadService $uploader)
    {
        $uploader->delete($ressource->file_path);
        $uploader->delete($ressource->thumbnail);
        $ressource->delete();

        return back()->with('success', 'Ressource supprimée.');
    }

    protected function validated(Request $request): array
    {
        $types    = config('bibliotheque.types');
        $langs    = array_keys(config('bibliotheque.languages'));
        $themes   = config('bibliotheque.themes');
        $isVideo  = $request->input('type') === 'Vidéo';

        return $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => ['required', Rule::in($types)],
            'language'     => ['required', Rule::in($langs)],
            'theme'        => ['nullable', Rule::in($themes)],
            'author'       => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:255',
            'abstract'     => 'nullable|string',
            'video_url'    => $isVideo
                ? 'required|url|max:500'
                : 'nullable|url|max:500',
            'file_path'    => 'nullable|mimes:pdf|max:20480',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_validated' => 'boolean',
        ], [
            'video_url.required' => 'Indiquez l’URL YouTube ou Vimeo pour une ressource de type Vidéo.',
        ]);
    }

    protected function handleUploads(Request $request, array $data, ?Resource $ressource, UploadService $uploader): array
    {
        if ($data['type'] === 'Vidéo') {
            $data['file_path'] = null;
            if ($ressource?->file_path) {
                $uploader->delete($ressource->file_path);
            }
        } else {
            $data['video_url'] = null;
            if ($ressource?->video_url) {
                $data['video_url'] = null;
            }
        }

        if ($request->hasFile('file_path')) {
            if ($ressource?->file_path) {
                $uploader->delete($ressource->file_path);
            }
            $data['file_path'] = $uploader->storePdf($request->file('file_path'), 'ressources');
        }

        if ($request->hasFile('thumbnail')) {
            if ($ressource?->thumbnail) {
                $uploader->delete($ressource->thumbnail);
            }
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'ressources/thumbnails');
        }

        return $data;
    }

    protected function syncTheme(Resource $ressource, ?string $themeName): void
    {
        if (blank($themeName)) {
            $ressource->tags()->detach();

            return;
        }

        $tag = Tag::firstOrCreate(
            ['name' => $themeName],
            ['slug' => Str::slug($themeName), 'color' => '#52B788']
        );

        $ressource->tags()->sync([$tag->id]);
    }
}
