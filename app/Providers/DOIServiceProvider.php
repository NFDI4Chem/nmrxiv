<?php

namespace App\Providers;

use App\Services\DOI\DataCite;
use App\Services\DOI\DOIService;
use Illuminate\Support\ServiceProvider;

class DOIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
