<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \SocialiteProviders\Manager\ServiceProvider::class,
        \OwenIt\Auditing\AuditingServiceProvider::class,
        \Lab404\Impersonate\ImpersonateServiceProvider::class,
        \L5Swagger\L5SwaggerServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->validateCsrfTokens(except: [
            //
            'support-bubble',
        ]);

        $middleware->append(\Spatie\CookieConsent\CookieConsentMiddleware::class);

        $middleware->web([
            \Laravel\Jetstream\Http\Middleware\AuthenticateSession::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\XFrameOptions::class,
        ]);

        $middleware->throttleApi();

        $middleware->replace(\Illuminate\Http\Middleware\TrustProxies::class, \App\Http\Middleware\TrustProxies::class);

        $middleware->alias([
            'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
