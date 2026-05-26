<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function index(Request $request)
    {
        $evenements = Event::with('organizer')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest('start_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.evenements.index', compact('evenements'));
    }

    public function create()
    {
        return view('admin.evenements.form', ['evenement' => new Event(), 'action' => 'create']);
    }

    public function store(Request $request, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:100',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_validated' => 'boolean',
            'is_online'    => 'boolean',
            'capacity'     => 'nullable|integer|min:1',
        ]);

        $data['slug']         = Str::slug($data['title']) . '-' . Str::random(5);
        $data['user_id']      = auth()->id();
        $data['is_validated'] = $request->boolean('is_validated');
        $data['is_online']    = $request->boolean('is_online');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'evenements/thumbnails');
        }

        Event::create($data);

        return redirect()->route('admin.evenements.index')->with('success', 'Événement créé.');
    }

    public function edit(Event $evenement)
    {
        return view('admin.evenements.form', ['evenement' => $evenement, 'action' => 'edit']);
    }

    public function update(Request $request, Event $evenement, UploadService $uploader)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'description'  => 'nullable|string',
            'location'     => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:100',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'thumbnail'    => 'nullable|image|max:4096',
            'is_validated' => 'boolean',
            'is_online'    => 'boolean',
            'capacity'     => 'nullable|integer|min:1',
        ]);

        $data['is_validated'] = $request->boolean('is_validated');
        $data['is_online']    = $request->boolean('is_online');

        if ($request->hasFile('thumbnail')) {
            $uploader->delete($evenement->thumbnail);
            $data['thumbnail'] = $uploader->storeImage($request->file('thumbnail'), 'evenements/thumbnails');
        }

        $evenement->update($data);

        return redirect()->route('admin.evenements.index')->with('success', 'Événement mis à jour.');
    }

    public function destroy(Event $evenement, UploadService $uploader)
    {
        $uploader->delete($evenement->thumbnail);
        $evenement->delete();
        return back()->with('success', 'Événement supprimé.');
    }
}
