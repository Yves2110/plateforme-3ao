<?php

namespace App\Support;

use App\Models\User;

class PublicContentGate
{
    public static function can(array $permissions): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->can('administrer-utilisateurs')) {
            return true;
        }

        return $user->hasAnyPermission($permissions);
    }

    public static function authorize(array $permissions): void
    {
        abort_unless(static::can($permissions), 403);
    }
}
