<?php

namespace App\Listeners;

use App\Services\SecurityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogSecurityAuthEvents
{
    public function handleLogin(Login $event): void
    {
        SecurityLogger::auth('login.success', [
            'user_id' => $event->user->getAuthIdentifier(),
            'email'   => $event->user->email ?? null,
            'guard'   => $event->guard,
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        SecurityLogger::auth('logout', [
            'user_id' => $event->user?->getAuthIdentifier(),
            'guard'   => $event->guard,
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        SecurityLogger::auth('login.failed', [
            'email' => $event->credentials['email'] ?? null,
            'guard' => $event->guard,
        ]);
    }
}
