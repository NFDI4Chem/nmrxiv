<?php

use App\Providers\AppServiceProvider;
use App\Providers\CASServiceProvider;
use App\Providers\CephStorageServiceProvider;
use App\Providers\DOIServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\JetstreamServiceProvider;
use App\Providers\MinioStorageServiceProvider;
use App\Providers\RouteServiceProvider;

return [
    AppServiceProvider::class,
    CASServiceProvider::class,
    CephStorageServiceProvider::class,
    DOIServiceProvider::class,
    EventServiceProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
    JetstreamServiceProvider::class,
    MinioStorageServiceProvider::class,
    RouteServiceProvider::class,
];
