<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminFormationController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Request $request)
    {
        $query = Formation::with('author')
            ->when($request->validated === '1', fn($q) => $q->where('is_validated', true))
            ->when($request->validated === '0', fn($q) => $q->where('is_validated', false))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->q, fn($q) => $q->where('title', 'like', "%{$request->q}%"));

        $formations = $query->latest()->paginate(20)->withQueryString();

        $counts = [
            'all' => Formation::count(),
            'validated' => Formation::where('is_validated', true)->count(),
            'pending' => Formation::where('is_validated', false)->count(),
        ];

        $types = Formation::distinct()->pluck('type')->filter();

        return view('admin.formations.index', compact('formations', 'counts', 'types'));
    }

    public function create()
    {
        return view('admin.formations.form', ['formation' => new Formation()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateFormation($request);
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->uploadService->storeImage(
                $request->file('thumbnail'),
                'formations'
            );
        }

        Formation::create($validated);

        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    public function edit(Formation $formation)
    {
        return view('admin.formations.form', compact('formation'));
    }

    public function update(Request $request, Formation $formation)
    {
        $validated = $this->validateFormation($request);

        if ($request->hasFile('thumbnail')) {
            if ($formation->thumbnail) {
                $this->uploadService->delete($formation->thumbnail);
            }
            $validated['thumbnail'] = $this->uploadService->storeImage(
                $request->file('thumbnail'),
                'formations'
            );
        }

        $formation->update($validated);

        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy(Formation $formation)
    {
        if ($formation->thumbnail) {
            $this->uploadService->delete($formation->thumbnail);
        }

        $formation->delete();

        return redirect()->route('admin.formations.index')
            ->with('success', 'Formation supprimée avec succès.');
    }

    public function toggleValidation(Formation $formation)
    {
        $formation->update(['is_validated' => !$formation->is_validated]);

        $status = $formation->is_validated ? 'validée' : 'dévalidée';
        return redirect()->back()
            ->with('success', "Formation {$status} avec succès.");
    }

    private function validateFormation(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:atelier,cours,webinaire,certification',
            'organizer' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'is_online' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'audience' => 'nullable|string|max:500',
            'language' => 'required|in:fr,en,pt',
            'price' => 'nullable|numeric|min:0',
            'registration_url' => 'nullable|url|max:500',
            'thumbnail' => 'nullable|image|max:2048',
            'is_validated' => 'boolean',
        ]);
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Formation::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
