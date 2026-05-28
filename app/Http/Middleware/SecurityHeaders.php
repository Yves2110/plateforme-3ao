<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
        $response->headers->remove('X-XSS-Protection');

        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        $response->headers->set(
            'Content-Security-Policy',
            $this->contentSecurityPolicy()
        );

        return $response;
    }

    protected function contentSecurityPolicy(): string
    {
        // unsafe-inline / unsafe-eval requis pour Livewire + Alpine + Leaflet (carte).
        // Les CDN listés sont ceux déjà utilisés par l'app ; préférer des assets locaux à terme.
        return implode(' ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com",
            "style-src 'self' 'unsafe-inline' fonts.googleapis.com unpkg.com",
            "font-src 'self' fonts.gstatic.com",
            "img-src 'self' data: blob: images.unsplash.com *.openstreetmap.org tile.openstreetmap.org *.basemaps.cartocdn.com *.cartocdn.com",
            "frame-src 'self' www.youtube.com player.vimeo.com",
            "connect-src 'self' blob: cdnjs.cloudflare.com nominatim.openstreetmap.org overpass-api.de",
        ]);
    }
}
