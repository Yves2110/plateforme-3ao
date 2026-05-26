<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SecurityLogger
{
    public static function auth(string $event, array $context = []): void
    {
        Log::channel('security')->info($event, self::sanitize($context));
    }

    public static function admin(string $action, array $context = []): void
    {
        Log::channel('security')->info("admin.{$action}", self::sanitize(array_merge([
            'user_id'    => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip'         => request()->ip(),
        ], $context)));
    }

    protected static function sanitize(array $context): array
    {
        $hidden = ['password', 'password_confirmation', 'current_password', 'token', 'two_factor_secret', 'api_key'];

        foreach ($hidden as $key) {
            if (array_key_exists($key, $context)) {
                $context[$key] = '[redacted]';
            }
        }

        return $context;
    }
}
