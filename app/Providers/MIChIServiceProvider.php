<?php

namespace App\Providers;

use App\Services\MIChI\MARGARITAS;
use App\Services\MIChI\MIChIService;
use Illuminate\Support\ServiceProvider;

class MIChIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MIChIService::class, MARGARITAS::class);
    }
}
