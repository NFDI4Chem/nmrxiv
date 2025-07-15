<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;

/**
 * Application Service Provider
 *
 * This service provider is responsible for registering application-wide services,
 * configuring core functionality, and bootstrapping essential application features
 * including filesystem services, URL configuration, and user impersonation events.
 *
 * @author NMRXIV Development Team
 *
 * @since 1.0.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Register any application services.
     *
     * This method is called by Laravel during the application bootstrap process
     * to register services into the container. All custom services and bindings
     * should be registered here.
     */
    public function register(): void
    {
        // Register filesystem services
        $this->app->singleton(\App\Services\PathGeneratorService::class);
        $this->app->singleton(\App\Services\StorageSignedUrlService::class);
        $this->app->singleton(\App\Services\FileIntegrityService::class);
        $this->app->bind(\App\Services\FileSystemObjectService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all services have been registered and
     * the application is ready to handle requests. Environment-specific
     * configurations and event listeners are set up here.
     */
    public function boot(): void
    {
        if (App::environment('production') || App::environment('development')) {
            URL::forceScheme('https');
        }

        $this->bootEvent();
    }

    /**
     * Bootstrap event listeners for the application.
     *
     * Registers event listeners for user impersonation functionality,
     * managing session state during impersonation events to maintain
     * proper authentication context.
     */
    public function bootEvent(): void
    {
        Event::listen(function (TakeImpersonation $event) {
            session()->put([
                'password_hash_sanctum' => $event->impersonated->getAuthPassword(),
            ]);
        });

        Event::listen(function (LeaveImpersonation $event) {
            session()->remove('password_hash_web');
            session()->put([
                'password_hash_sanctum' => $event->impersonator->getAuthPassword(),
            ]);
            Auth::setUser($event->impersonator);
        });
    }
}
