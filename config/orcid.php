<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ORCID API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for ORCID public API integration.
    |
    */

    'base_url' => env('ORCID_BASE_URL', 'https://pub.orcid.org/v3.0'),

    'search_api' => env('ORCID_SEARCH_API', 'https://pub.orcid.org/v2.1/search'),

];
