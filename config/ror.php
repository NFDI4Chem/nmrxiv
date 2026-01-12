<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ROR API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Research Organization Registry (ROR) API.
    | ROR provides persistent identifiers for research organizations.
    |
    */

    'api_url' => env('ROR_API_URL', 'https://api.ror.org/organizations'),

    'client_id' => env('ROR_CLIENT_ID'),

];
