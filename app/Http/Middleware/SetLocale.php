<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['fr', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale');

        if ($locale === null) {
            $locale = $this->resolveInitialLocale($request);
            Session::put('locale', $locale);
        }

        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, self::SUPPORTED, true)) {
                $locale = $lang;
                Session::put('locale', $locale);
            }
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    private function resolveInitialLocale(Request $request): string
    {
        return config('app.locale', 'fr');
    }
}
