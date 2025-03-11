<?php

use Illuminate\Support\ServiceProvider;

return [

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Laravel Framework Service Providers...
         */

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\HorizonServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
        App\Providers\JetstreamServiceProvider::class,

        \SocialiteProviders\Manager\ServiceProvider::class,
        OwenIt\Auditing\AuditingServiceProvider::class,
        App\Providers\MinioStorageServiceProvider::class,
        App\Providers\CephStorageServiceProvider::class,
        App\Providers\DOIServiceProvider::class,

        Lab404\Impersonate\ImpersonateServiceProvider::class,
        L5Swagger\L5SwaggerServiceProvider::class,
    ])->toArray(),

];
