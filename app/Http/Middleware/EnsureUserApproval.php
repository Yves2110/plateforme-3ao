<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Votre inscription a été refusée. Contactez le secrétariat 3AO pour plus d\'informations.');
        }

        return $next($request);
    }
}
