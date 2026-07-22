<?php

return [

    /*
    |--------------------------------------------------------------------------
    | External URLs and API Endpoints
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for external URLs, API endpoints,
    | and third-party service URLs used throughout the application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Michi Standards
    |--------------------------------------------------------------------------
    */

    'michi_standards_url' => env('MICHI_STANDARDS_URL'),

    /*
    |--------------------------------------------------------------------------
    | External API Endpoints
    |--------------------------------------------------------------------------
    |
    | API endpoints for external services used by the application.
    |
    */

    'nmrium_url' => env('NMRIUM_URL', 'https://nmrium.nmrxiv.org'),
    'nmrkit_url' => env('NMRKIT_URL', 'https://nmrkit.nmrxiv.org'),
    'europemc_ws_api' => env('EUROPEMC_WS_API', 'https://www.ebi.ac.uk/europepmc/webservices/rest/search'),
    'cm_api' => env('CM_API', 'https://api2.naturalproducts.net/latest/'),
    'crossref_api' => env('CROSSREF_API', 'https://api.crossref.org/works/'),
    'datacite_api' => env('DATACITE_API', 'https://api.datacite.org'),

];
