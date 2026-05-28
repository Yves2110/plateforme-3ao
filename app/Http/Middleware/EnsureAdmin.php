<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403, 'Accès réservé au back-office.');
        }

        $user = auth()->user();

        if ($user->canAccessBackOffice()) {
            return $next($request);
        }

        abort(403, 'Accès réservé au back-office.');
    }
}
