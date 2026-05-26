<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CarteCacheService
{
    public static function flush(): void
    {
        Cache::forget('carte.actors_count');
        Cache::forget('carte.types');
        Cache::forget('carte.countries');

        // Les clés acteurs filtrées utilisent un hash — on flush le store tag si disponible,
        // sinon on laisse expirer (TTL 5 min). Pour SQLite/file, pattern flush n'est pas natif.
        if (method_exists(Cache::getStore(), 'tags')) {
            try {
                Cache::tags(['carte'])->flush();
            } catch (\BadMethodCallException) {
                // driver sans tags
            }
        }
    }
}
