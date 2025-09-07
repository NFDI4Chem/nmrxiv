<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URL'),
    ],

    'orcid' => [
        'client_id' => env('ORCID_CLIENT_ID'),
        'client_secret' => env('ORCID_CLIENT_SECRET'),
        'redirect' => env('ORCID_REDIRECT_URL'),
        'environment' => env('ORCID_ENVIRONMENT', 'test'),
        'uid_fieldname' => env('ORCID_UID_FIELDNAME'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URL'),
    ],

    'chemotion_tracker' => [
        'enabled' => env('CHEMOTION_TRACKER_ENABLED', false),
        'base_url' => env('CHEMOTION_TRACKER_BASE_URL', 'http://193.196.38.117:3000'),
        'client_id' => env('CHEMOTION_TRACKER_CLIENT_ID', 'S8HbJdAEo--y05h4EUYblZBu-bgWkSDJrEb7kKMCpwc'),
        'username' => env('CHEMOTION_TRACKER_USERNAME'),
        'password' => env('CHEMOTION_TRACKER_PASSWORD'),
    ],

];
