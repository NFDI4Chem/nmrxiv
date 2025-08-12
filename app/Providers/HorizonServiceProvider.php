<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * Horizon Service Provider
 *
 * This service provider configures Laravel Horizon for queue management
 * and monitoring. It handles access control, notification routing, and
 * UI customization for the Horizon dashboard used to monitor job queues
 * in the NMRXIV application.
 *
 * @author NMRXIV Development Team
 *
 * @since 1.0.0
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * Initializes the parent Horizon service provider and sets up
     * any additional configuration specific to the NMRXIV application.
     * Commented examples show available notification routing options.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        // Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     * Access is restricted to users with 'super-admin' or 'developer' roles
     * to ensure only authorized personnel can monitor and manage job queues.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {
            return $user->hasAnyRole(['super-admin', 'developer']);
        });
    }
}
