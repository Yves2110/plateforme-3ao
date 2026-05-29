<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserApprovedMail;
use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();

        $users = User::query()
            ->visibleToAdmin($actor)
            ->with('roles')
            ->when($request->search, function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('organization', 'like', "%{$s}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.utilisateurs.index', compact('users', 'roles'));
    }

    public function edit(User $user)
    {
        $this->ensureAdminCanAccessUser($user);

        $roles       = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.utilisateurs.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdminCanAccessUser($user);
        $actor = $request->user();

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,'.$user->id,
            'role'         => 'required|exists:roles,name',
            'organization' => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:100',
        ]);

        abort_if(
            $data['role'] === 'super_admin' && ! $actor->isPlatformOwner(),
            403,
            'Seul le propriétaire de la plateforme peut attribuer le rôle super_admin.'
        );

        if ($user->hasRole('super_admin') && ! $actor->isPlatformOwner()) {
            $data['role'] = 'super_admin';
        }

        if ($user->isPlatformOwner()) {
            $data['role'] = 'super_admin';
        }

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
        $this->ensureAdminCanAccessUser($user);
        abort_unless($user->canAdminDelete(auth()->user()), 403, 'Action non autorisée sur ce compte.');

        SecurityLogger::admin('user.deleted', ['target_user_id' => $user->id]);
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé.');
    }

    public function toggleActive(User $user)
    {
        $this->ensureAdminCanAccessUser($user);
        abort_unless($user->canAdminToggle(auth()->user()), 403, 'Action non autorisée sur ce compte.');

        $user->update(['is_active' => ! $user->is_active]);

        SecurityLogger::admin('user.toggled_active', [
            'target_user_id' => $user->id,
            'is_active' => $user->is_active,
        ]);

        $message = $user->is_active
            ? "Le compte {$user->name} a été réactivé."
            : "Le compte {$user->name} a été désactivé.";

        return back()->with('success', $message);
    }

    public function pending()
    {
        $pendingUsers = User::query()
            ->visibleToAdmin(auth()->user())
            ->where('approval_status', 'pending')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin');
            })
            ->latest()
            ->paginate(10);

        return view('admin.utilisateurs.pending', compact('pendingUsers'));
    }

    public function approve(Request $request, User $user)
    {
        $this->ensureAdminCanAccessUser($user);
        abort_if($user->approval_status !== 'pending', 400, 'Cet utilisateur n\'est pas en attente de validation.');

        $user->forceFill([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'email_verified_at' => now(),
        ])->save();

        $user->assignRole('contributeur');

        $mailSent = true;
        try {
            Mail::to($user->email)->send(new UserApprovedMail($user));
        } catch (\Throwable $e) {
            $mailSent = false;
            SecurityLogger::admin('user.approve_mail_failed', [
                'target_user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        SecurityLogger::admin('user.approved', ['target_user_id' => $user->id]);

        if (! $mailSent) {
            return back()->with('warning', "L'utilisateur {$user->name} a été approuvé, mais l'email d'accès n'a pas pu être envoyé. Vérifiez la configuration mail.");
        }

        return back()->with('success', "L'utilisateur {$user->name} a été approuvé et un email avec les accès a été envoyé.");
    }

    public function reject(Request $request, User $user)
    {
        $this->ensureAdminCanAccessUser($user);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        abort_if($user->approval_status !== 'pending', 400, 'Cet utilisateur n\'est pas en attente de validation.');

        $user->forceFill([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->reason,
        ])->save();

        SecurityLogger::admin('user.rejected', ['target_user_id' => $user->id]);

        return back()->with('info', "L'inscription de {$user->name} a été refusée.");
    }

    private function ensureAdminCanAccessUser(User $user): void
    {
        abort_unless($user->visibleToAdmin(auth()->user()), 404);
    }
}
