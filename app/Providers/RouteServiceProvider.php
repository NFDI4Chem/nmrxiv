<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * Route Service Provider
 *
 * This service provider is responsible for configuring application routes,
 * rate limiting, and route model bindings. It defines the route structure
 * for both web and API endpoints, and establishes security policies
 * through rate limiting configurations.
 *
 * @package App\Providers
 * @author NMRXIV Development Team
 * @since 1.0.0
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The path to the "landing" route for your application.
     *
     * This is used by Laravel verification to redirect users after successful verification.
     *
     * @var string
     */
    public const LANDING = '/';

    /**
     * Define your route model bindings, pattern filters, and rate limiting.
     *
     * This method is called during the application bootstrap process to
     * configure routing behavior, load route files, and establish
     * security policies through rate limiting.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * Defines rate limiting policies for different types of requests
     * to protect the application from abuse and ensure fair usage.
     * API requests are limited based on user ID or IP address.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
