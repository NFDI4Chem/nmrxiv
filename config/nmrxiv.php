<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cool Off Period
    |--------------------------------------------------------------------------
    |
    | The number of days a project remains in draft status before being
    | automatically deleted. This applies to projects marked for deletion.
    |
    */

    'cool_off_period' => (int) env('COOL_OFF_PERIOD', 30),

    /*
    |--------------------------------------------------------------------------
    | Spectra Parsing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the spectra parsing queue system including API endpoints,
    | storage locations, retry logic, and timeout values.
    |
    */

    'spectra_parsing' => [
        // API Endpoints
        'nmrkit_api_url' => env('NMRKIT_API_URL', 'https://nmrkit.nmrxiv.org/latest/spectra/parse/url'),
        'bioschema_api_url' => env('BIOSCHEMA_API_URL', 'https://nmrxiv.org/api/v1/schemas/bioschemas'),

        // Storage Configuration
        'storage_disk' => env('SPECTRA_STORAGE_DISK', 'local'),
        'storage_path' => env('SPECTRA_STORAGE_PATH', 'spectra_parse'),

        // Job Configuration
        'job_tries' => (int) env('SPECTRA_JOB_TRIES', 3),
        'job_timeout' => (int) env('SPECTRA_JOB_TIMEOUT', 600),

        // Network Configuration
        'retry_count' => (int) env('SPECTRA_RETRY_COUNT', 3),
        'download_timeout' => (int) env('SPECTRA_DOWNLOAD_TIMEOUT', 300),
        'api_timeout' => (int) env('SPECTRA_API_TIMEOUT', 300),
    ],

];
