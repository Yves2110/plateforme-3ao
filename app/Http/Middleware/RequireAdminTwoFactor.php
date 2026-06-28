<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! config('security.require_admin_2fa', false)) {
            return $next($request);
        }

        $rolesRequiring2fa = config('security.require_2fa_roles', []);

        if ($rolesRequiring2fa !== [] && $user->hasRole($rolesRequiring2fa)) {
            if (empty($user->two_factor_secret)) {
                if ($request->expectsJson()) {
                    abort(403, 'L\'authentification à deux facteurs est obligatoire pour ce compte.');
                }

                return redirect()
                    ->to(route('profile.show') . '#two-factor')
                    ->with('error', 'Accès administration sécurisé : l\'authentification à deux facteurs (2FA) est obligatoire. Ouvrez l\'onglet "Authentification à deux facteurs" ci-dessous, scannez le QR code avec une application comme Google Authenticator ou Authy, puis saisissez le code de confirmation pour activer le 2FA.');
            }
        }

        return $next($request);
    }
}
