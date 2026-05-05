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

    /*
    |--------------------------------------------------------------------------
    | ORCID API Endpoints
    |--------------------------------------------------------------------------
    |
    | Various ORCID API endpoints for searching and retrieving person data.
    |
    */

    'search_api' => env('ORCID_ID_SEARCH_API', 'https://pub.orcid.org/v3.0/search'),
    'person_api' => env('ORCID_ID_PERSON_API', 'https://pub.orcid.org/v3.0'),
    'employment_api' => env('ORCID_ID_EMPLOYMENT_API', 'https://pub.orcid.org/v3.0'),

];
