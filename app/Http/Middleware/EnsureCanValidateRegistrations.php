<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanValidateRegistrations
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->canValidateRegistrations()) {
            return $next($request);
        }

        abort(403, 'Vous n\'êtes pas autorisé à valider les inscriptions.');
    }
}
