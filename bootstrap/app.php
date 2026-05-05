<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\XFrameOptions;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use L5Swagger\L5SwaggerServiceProvider;
use Lab404\Impersonate\ImpersonateServiceProvider;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;
use OwenIt\Auditing\AuditingServiceProvider;
use SocialiteProviders\Manager\ServiceProvider;
use Spatie\CookieConsent\CookieConsentMiddleware;
use Spatie\Csp\AddCspHeaders;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        ServiceProvider::class,
        AuditingServiceProvider::class,
        ImpersonateServiceProvider::class,
        L5SwaggerServiceProvider::class,
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

        $middleware->append(CookieConsentMiddleware::class);

        $middleware->web([
            AuthenticateSession::class,
            HandleInertiaRequests::class,
            XFrameOptions::class,
            AddCspHeaders::class,
        ]);

        // Disable API throttling in testing environment to allow test suite to run
        if (env('APP_ENV') !== 'testing') {
            $middleware->throttleApi();
        }

        $middleware->replace(TrustProxies::class, App\Http\Middleware\TrustProxies::class);

        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if (! $request->routeIs('login') || ! $request->isMethod('POST')) {
                return null;
            }

            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->route('login')
                ->withInput(Arr::except($request->input(), [
                    'current_password',
                    'password',
                    'password_confirmation',
                ]))
                ->withErrors($e->errors(), $request->input('_error_bag', $e->errorBag));
        });
    })->create();
