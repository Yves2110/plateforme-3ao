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

        if ($user->hasRole(['super_admin', 'moderateur'])) {
            return $next($request);
        }

        $backOfficePermissions = [
            'publier-bibliotheque',
            'moderer-forum',
            'gerer-carte',
            'soumettre-acteur',
            'creer-evenements',
            'gerer-rss',
            'publier-actualites',
            'administrer-utilisateurs',
            'acceder-statistiques',
            'contribuer-multimedia',
            'gerer-newsletter',
        ];

        if ($user->hasAnyPermission($backOfficePermissions)) {
            return $next($request);
        }

        abort(403, 'Accès réservé au back-office.');
    }
}
