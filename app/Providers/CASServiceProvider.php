<?php

namespace App\Providers;

use App\Services\CAS\CASService;
use App\Services\CAS\CommonChemistry;
use Illuminate\Support\ServiceProvider;

/**
 * CAS Service Provider
 *
 * This service provider is responsible for registering Chemical Abstracts Service (CAS)
 * services into the Laravel container. It provides a flexible binding system that
 * allows switching between different CAS providers (e.g., CommonChemistry) based on
 * configuration settings.
 *
 * @author NMRXIV Development Team
 *
 * @since 1.0.0
 */
class CASServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Binds the CASService interface to concrete implementations based on
     * the configured CAS provider. This allows the application to switch
     * between different CAS services without changing the consuming code.
     */
    public function register(): void
    {
        $this->app->bind(CASService::class, function ($app) {
            return new CommonChemistry;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
