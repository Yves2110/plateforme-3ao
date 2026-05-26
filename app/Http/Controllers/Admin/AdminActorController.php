<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use App\Services\CarteCacheService;
use App\Services\UploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminActorController extends Controller
{
    protected function authorizeActorAccess(): void
    {
        $user = auth()->user();
        abort_unless($user?->can('gerer-carte') || $user?->can('soumettre-acteur'), 403);
    }

    public function index(Request $request)
    {
        $this->authorizeActorAccess();
        $acteurs = Actor::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->validated !== null, fn($q) => $q->where('is_validated', (bool)$request->validated))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.acteurs.index', compact('acteurs'));
    }

    public function create()
    {
        $this->authorizeActorAccess();
        return view('admin.acteurs.form', ['acteur' => new Actor(), 'action' => 'create']);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $this->authorizeActorAccess();
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'country'      => 'nullable|string|max:100',
            'region'       => 'nullable|string|max:100',
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'website'      => 'nullable|url',
            'email'        => 'nullable|email',
            'lat'          => 'nullable|numeric|between:-90,90',
            'lng'          => 'nullable|numeric|between:-180,180',
            'logo'         => 'nullable|image|max:2048',
            'is_validated' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        $data['is_validated'] = $this->resolveValidated($request);

        if ($request->hasFile('logo')) {
            $data['logo'] = $uploader->storeImage($request->file('logo'), 'acteurs/logos', 400);
        }

        Actor::create($data);
        CarteCacheService::flush();

        return redirect()->route('admin.acteurs.index')->with('success', 'Acteur créé.');
    }

    public function edit(Actor $acteur)
    {
        $this->authorizeActorAccess();
        return view('admin.acteurs.form', ['acteur' => $acteur, 'action' => 'edit']);
    }

    public function update(Request $request, Actor $acteur, UploadService $uploader)
    {
        $this->authorizeActorAccess();
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'country'      => 'nullable|string|max:100',
            'region'       => 'nullable|string|max:100',
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'website'      => 'nullable|url',
            'email'        => 'nullable|email',
            'lat'          => 'nullable|numeric|between:-90,90',
            'lng'          => 'nullable|numeric|between:-180,180',
            'logo'         => 'nullable|image|max:2048',
            'is_validated' => 'boolean',
        ]);

        if ($request->has('is_validated')) {
            $data['is_validated'] = $this->resolveValidated($request);
        }

        if ($request->hasFile('logo')) {
            $uploader->delete($acteur->logo);
            $data['logo'] = $uploader->storeImage($request->file('logo'), 'acteurs/logos', 400);
        }

        $acteur->update($data);
        CarteCacheService::flush();

        return redirect()->route('admin.acteurs.index')->with('success', 'Acteur mis à jour.');
    }

    public function destroy(Actor $acteur, UploadService $uploader)
    {
        $this->authorizeActorAccess();
        abort_unless(auth()->user()?->can('gerer-carte'), 403);
        $uploader->delete($acteur->logo);
        $acteur->delete();
        CarteCacheService::flush();

        return back()->with('success', 'Acteur supprimé.');
    }

    protected function resolveValidated(Request $request): bool
    {
        if (Auth::user()?->can('gerer-carte')) {
            return $request->boolean('is_validated');
        }

        return false;
    }
}
