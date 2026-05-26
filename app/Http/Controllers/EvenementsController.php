<?php

namespace App\Http\Controllers;

use App\Mail\EventRegistrationMail;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EvenementsController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where('is_validated', true)
            ->where('start_date', '>=', now()->subDay())
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy('start_date')
            ->paginate(12);

        return view('evenements.index', compact('events'));
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('is_validated', true)
            ->firstOrFail();

        return view('evenements.show', compact('event'));
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
