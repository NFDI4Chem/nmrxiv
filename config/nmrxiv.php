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
    | Publish Processing
    |--------------------------------------------------------------------------
    |
    | When a project stays in queued/processing without new log activity for
    | longer than this many minutes, the status endpoint marks it as stale.
    |
    */

    'publish' => [
        'stale_after_minutes' => (int) env('PUBLISH_STALE_AFTER_MINUTES', 30),
    ],

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

        // Queue Configuration
        'queue' => env('SPECTRA_QUEUE', 'metadata-extraction'),
        'backoff' => array_map('intval', explode(',', env('SPECTRA_BACKOFF', '60,300,900'))),

        // Job Configuration
        'job_tries' => (int) env('SPECTRA_JOB_TRIES', 3),
        'job_timeout' => (int) env('SPECTRA_JOB_TIMEOUT', 600),

        // Network Configuration
        'retry_count' => (int) env('SPECTRA_RETRY_COUNT', 3),
        'download_timeout' => (int) env('SPECTRA_DOWNLOAD_TIMEOUT', 300),
        'api_timeout' => (int) env('SPECTRA_API_TIMEOUT', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Assignment Quickcheck
    |--------------------------------------------------------------------------
    |
    | 1H/13C assignments are checked by NMRKit against predicted shifts. The
    | prediction is a shared service, so failed calls back off and the public
    | endpoint is rate limited per IP.
    |
    */

    'assignment_validation' => [
        'url' => env(
            'ASSIGNMENT_VALIDATION_URL',
            rtrim((string) env('NMRKIT_URL', 'https://nmrkit.nmrxiv.org'), '/').'/latest/validate/assignments'
        ),
        'timeout' => (int) env('ASSIGNMENT_VALIDATION_TIMEOUT', 150),
        'queue' => env('ASSIGNMENT_VALIDATION_QUEUE', 'default'),
        'job_tries' => (int) env('ASSIGNMENT_VALIDATION_JOB_TRIES', 4),
        'backoff' => array_map('intval', explode(',', env('ASSIGNMENT_VALIDATION_BACKOFF', '60,300,900'))),
        'public_per_minute' => (int) env('ASSIGNMENT_QUICKCHECK_PER_MINUTE', 6),
        'max_file_kb' => (int) env('ASSIGNMENT_VALIDATION_MAX_FILE_KB', 2048),
    ],

];
