<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'admin.2fa' => \App\Http\Middleware\RequireAdminTwoFactor::class,
            'spam.protect' => \App\Http\Middleware\PreventSpamSubmission::class,
            'approved' => \App\Http\Middleware\EnsureUserApproval::class,
            'can.validate.registrations' => \App\Http\Middleware\EnsureCanValidateRegistrations::class,
            'admin.permission' => \App\Http\Middleware\EnsureAdminPermission::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
