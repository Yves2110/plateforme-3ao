<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSpamSubmission
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['website', 'url', 'company_url'] as $honeypot) {
            if ($request->filled($honeypot)) {
                abort(422, 'Requête invalide.');
            }
        }

        $started = $request->input('_form_started');

        if ($started !== null && $started !== '') {
            $elapsed = time() - (int) $started;
            $min = config('security.form_min_seconds', 2);

            if ($elapsed < $min) {
                abort(422, 'Veuillez patienter avant de soumettre le formulaire.');
            }
        }

        return $next($request);
    }
}
