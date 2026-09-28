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
    | Spectrum Search Configuration
    |--------------------------------------------------------------------------
    |
    | Chemical-shift search on the public Spectra tab. Tolerances and offsets
    | are in ppm, keyed by nucleus. Uploaded spectra are stored briefly under
    | `temp_prefix` on the default disk so NMRKit can fetch them by URL.
    |
    */

    'spectra_search' => [
        'nuclei' => ['1H', '13C'],

        'closeness' => [
            '1H' => ['strict' => 0.02, 'normal' => 0.05, 'relaxed' => 0.10],
            '13C' => ['strict' => 0.5, 'normal' => 1.0, 'relaxed' => 2.0],
        ],
        'max_tolerance' => ['1H' => 0.5, '13C' => 5.0],
        'max_offset' => ['1H' => 0.1, '13C' => 1.5],
        'min_matches_for_offset' => 3,
        'plausible_range' => ['1H' => [-2, 16], '13C' => [-20, 250]],
        'solvent_window' => ['1H' => 0.05, '13C' => 0.6],
        'max_rows_per_nucleus' => 100,
        'candidate_limit' => (int) env('SPECTRA_SEARCH_CANDIDATE_LIMIT', 500),

        'upload_max_kb' => (int) env('SPECTRA_SEARCH_UPLOAD_MAX_KB', 204800),
        'temp_prefix' => env('SPECTRA_SEARCH_TEMP_PREFIX', 'spectra-search'),
    ],

];
