<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproval
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->hasRole(['super_admin', 'moderateur'])) {
            return $next($request);
        }

        if ($user->approval_status === 'pending') {
            return redirect()->route('registration.pending');
        }

        if ($user->approval_status === 'rejected') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Votre inscription a été refusée. Contactez le secrétariat 3AO pour plus d\'informations.');
        }

        if (($user->is_active ?? true) === false && ! $user->hasRole(['super_admin', 'moderateur'])) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Contactez le secrétariat 3AO.');
        }

        return $next($request);
    }
}
