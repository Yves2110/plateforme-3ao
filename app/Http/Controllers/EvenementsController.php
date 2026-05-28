<?php

namespace App\Http\Controllers;

use App\Mail\EventRegistrationMail;
use App\Models\Event;
use App\Support\PublicContentGate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EvenementsController extends Controller
{
    public function index(Request $request)
    {
        $canManage = PublicContentGate::can(['creer-evenements', 'administrer-utilisateurs']);

        $today = now()->startOfDay();

        $events = Event::query()
            ->when(! $canManage, fn ($q) => $q->where('is_validated', true))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->orderByRaw(
                'CASE WHEN COALESCE(end_date, start_date) < ? THEN 1 ELSE 0 END',
                [$today]
            )
            ->orderByRaw(
                'CASE WHEN COALESCE(end_date, start_date) < ? THEN start_date END DESC',
                [$today]
            )
            ->orderByRaw(
                'CASE WHEN COALESCE(end_date, start_date) >= ? THEN start_date END ASC',
                [$today]
            )
            ->paginate(12);

        return view('evenements.index', compact('events', 'canManage'));
    }

    public function show(string $slug)
    {
        $canManage = PublicContentGate::can(['creer-evenements', 'administrer-utilisateurs']);

        $event = Event::where('slug', $slug)
            ->when(! $canManage, fn ($q) => $q->where('is_validated', true))
            ->firstOrFail();

        return view('evenements.show', compact('event', 'canManage'));
    }

    public function inscription(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $event->registrations()->create($validated);

        if (auth()->check()) {
            Mail::to(auth()->user())->queue(new EventRegistrationMail($event, auth()->user()));
        } else {
            Mail::to($validated['email'])->queue(
                (new EventRegistrationMail($event, (object) $validated))
            );
        }

        return back()->with('success', __('evenements.registration_success'));
    }
}
