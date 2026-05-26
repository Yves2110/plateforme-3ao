<?php

namespace App\Providers;

use App\Mail\WelcomeMail;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Listeners\LogSecurityAuthEvents;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Support\PlatformMailAddress;
use Illuminate\Mail\Events\MessageSending;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(fn () => Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols());

        Carbon::setLocale('fr');
        Paginator::useTailwind();

        Event::listen(Login::class, [LogSecurityAuthEvents::class, 'handleLogin']);
        Event::listen(Logout::class, [LogSecurityAuthEvents::class, 'handleLogout']);
        Event::listen(Failed::class, [LogSecurityAuthEvents::class, 'handleFailed']);

        Event::listen(MessageSending::class, function (MessageSending $event) {
            PlatformMailAddress::applyTo($event->message);
        });

        Event::listen(Registered::class, function (Registered $event) {
            Mail::to($event->user)->queue(new WelcomeMail($event->user));
        });

        RateLimiter::for('forum-post', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('event-register', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        View::composer('admin.layouts.admin', \App\View\Composers\AdminSidebarComposer::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
