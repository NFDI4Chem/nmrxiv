<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | Kept in sync with the latest release by release-please (see the marker
    | comment). Written into generated BagIt archives as Bag-Software-Agent.
    |
    */

    'version' => '2.5.0', // x-release-please-version

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
        // API Endpoints (NMRKit is configured through the single NMRKIT_URL base URL)
        'nmrkit_spectra_parse_url' => rtrim((string) env('NMRKIT_URL', 'https://nmrkit.nmrxiv.org'), '/').'/latest/spectra/parse/url',
        'bioschema_api_url' => env('BIOSCHEMA_API_URL', 'https://nmrxiv.org/api/v1/schemas/bioschemas'),

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

    /*
    |--------------------------------------------------------------------------
    | Zip Extraction
    |--------------------------------------------------------------------------
    |
    | Limits applied to zip archives extracted on the server (submission
    | uploads and ELN imports). Archives exceeding any limit are rejected
    | before anything is written to storage.
    |
    */

    'zip_extraction' => [
        'queue' => env('ZIP_EXTRACTION_QUEUE', 'default'),
        'job_timeout' => (int) env('ZIP_EXTRACTION_JOB_TIMEOUT', 3600),
        'max_entries' => (int) env('ZIP_EXTRACTION_MAX_ENTRIES', 20000),
        'max_uncompressed_bytes' => (int) env('ZIP_EXTRACTION_MAX_UNCOMPRESSED_BYTES', 50 * 1024 * 1024 * 1024),
        'max_compression_ratio' => (int) env('ZIP_EXTRACTION_MAX_COMPRESSION_RATIO', 100),
    ],

];
