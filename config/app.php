<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | BrowserSync client (local only, optional)
    |--------------------------------------------------------------------------
    |
    | When set (e.g. http://localhost:3000), app.blade.php loads the BrowserSync
    | client for live reload. Leave unset if BrowserSync is not running on that
    | port (avoids net::ERR_CONNECTION_REFUSED in the console).
    |
    */

    'browser_sync_client_url' => env('BROWSER_SYNC_CLIENT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Description
    |--------------------------------------------------------------------------
    |
    | A brief description of your application.
    |
    */

    'description' => env('APP_DESCRIPTION', 'NMRXIV is an open-access preprint repository for sharing and discovering nuclear magnetic resonance (NMR) spectroscopy data.'),

    /*
    |--------------------------------------------------------------------------
    | Schema Version
    |--------------------------------------------------------------------------
    |
    | The schema version used by the application for data structures.
    |
    */

    'schema_version' => env('SCHEMA_VERSION', 'beta'),

];
