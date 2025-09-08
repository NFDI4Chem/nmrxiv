<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTTP Proxy Configuration
    |--------------------------------------------------------------------------
    |
    | Configure HTTP and HTTPS proxy settings for outbound HTTP requests.
    | These settings are used when making external HTTP requests through
    | Laravel's HTTP client.
    |
    */

    'http_proxy' => env('HTTP_PROXY'),
    'https_proxy' => env('HTTPS_PROXY'),

    /*
    |--------------------------------------------------------------------------
    | No Proxy Hosts
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of hosts that should bypass the proxy.
    |
    */

    'no_proxy' => env('NO_PROXY'),
];
