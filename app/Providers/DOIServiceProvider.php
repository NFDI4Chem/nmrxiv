<?php

namespace App\Providers;

use App\Services\DOI\DataCite;
use App\Services\DOI\DOIService;
use Illuminate\Support\ServiceProvider;

/**
 * DOI Service Provider
 *
 * This service provider is responsible for registering Digital Object Identifier (DOI)
 * services into the Laravel container. It provides a flexible binding system that
 * allows switching between different DOI providers (e.g., DataCite) based on
 * configuration settings.
 *
 * @author NMRXIV Development Team
 *
 * @since 1.0.0
 */
class DOIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Binds the DOIService interface to concrete implementations based on
     * the configured DOI provider. This allows the application to switch
     * between different DOI services without changing the consuming code.
     */
    public function register(): void
    {
        $this->app->bind(DOIService::class, function ($app) {
            return match (config('doi.default')) {
                'datacite' => new DataCite
            };
        });
    }
}
