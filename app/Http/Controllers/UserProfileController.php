<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        $threads = $user->threads()
            ->where('is_validated', true)
            ->with('author')
            ->latest()
            ->limit(5)
            ->get();

        $ressources = $user->ressources()
            ->where('is_validated', true)
            ->latest()
            ->limit(5)
            ->get();

        $actualites = $user->actualites()
            ->where('is_published', true)
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('membres.show', compact('user', 'threads', 'ressources', 'actualites'));
    }

    public function dashboard()
    {
        $user = auth()->user();

        $threads   = $user->threads()->latest()->limit(10)->get();
        $ressources = $user->ressources()->latest()->limit(10)->get();
        $actualites = $user->actualites()->latest('published_at')->limit(10)->get();

        return view('membres.dashboard', compact('user', 'threads', 'ressources', 'actualites'));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        auth()->logout();
        $user->delete();

        return redirect('/')->with('success', 'Votre compte a bien été supprimé.');
    }
}
