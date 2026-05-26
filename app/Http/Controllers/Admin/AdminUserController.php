<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserApprovedMail;
use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.utilisateurs.index', compact('users', 'roles'));
    }

    public function edit(User $user)
    {
        $roles       = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        return view('admin.utilisateurs.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'role'         => 'required|exists:roles,name',
            'organization' => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:100',
        ]);

        $user->update([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'organization' => $data['organization'] ?? null,
            'country'      => $data['country'] ?? null,
        ]);

        $user->syncRoles([$data['role']]);

        $permissionsToSync = $request->input('permissions', []);
        $user->syncPermissions($permissionsToSync);

        SecurityLogger::admin('user.updated', [
            'target_user_id' => $user->id,
            'role'           => $data['role'],
        ]);

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Impossible de supprimer votre propre compte.');
        SecurityLogger::admin('user.deleted', ['target_user_id' => $user->id]);
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

    public function pending()
    {
        $pendingUsers = User::where('approval_status', 'pending')
            ->whereDoesntHave('roles', function($q) {
                $q->where('name', 'super_admin');
            })
            ->latest()
            ->paginate(20);

        return view('admin.utilisateurs.pending', compact('pendingUsers'));
    }

    public function approve(Request $request, User $user)
    {
        abort_if($user->approval_status !== 'pending', 400, 'Cet utilisateur n\'est pas en attente de validation.');

        // Générer un mot de passe aléatoire
        $password = Str::random(12);

        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'password' => Hash::make($password),
        ]);

        // Attribuer le rôle contributeur par défaut
        $user->assignRole('contributeur');

        // Envoyer l'email avec les credentials
        Mail::to($user->email)->send(new UserApprovedMail($user, $password));

        SecurityLogger::admin('user.approved', ['target_user_id' => $user->id]);

        return back()->with('success', "L'utilisateur {$user->name} a été approuvé et un email avec les accès a été envoyé.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        abort_if($user->approval_status !== 'pending', 400, 'Cet utilisateur n\'est pas en attente de validation.');

        $user->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        SecurityLogger::admin('user.rejected', ['target_user_id' => $user->id]);

        return back()->with('info', "L'inscription de {$user->name} a été refusée.");
    }
}
