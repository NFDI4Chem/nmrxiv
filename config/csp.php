<?php

use App\Support\Csp\Policies\NmrxivPolicy;
use Spatie\Csp\Directive;
use Spatie\Csp\Nonce\RandomString;

// use Spatie\Csp\Directive;
// use Spatie\Csp\Keyword;

return [

    /*
     * Presets will determine which CSP headers will be set. A valid CSP preset is
     * any class that implements `Spatie\Csp\Preset`
     */
    'presets' => [
        // Enforcement mode - now using secure CSP policy
        NmrxivPolicy::class,
    ],

    /**
     * Register additional global CSP directives here.
     * These can be configured via environment variables for runtime flexibility.
     */
    'directives' => [
        // Additional connect-src domains (configurable via env)
        ...(env('CSP_ADDITIONAL_CONNECT_SRC') ? [
            [Directive::CONNECT, array_filter(explode(',', env('CSP_ADDITIONAL_CONNECT_SRC')))],
        ] : []),

        // Additional img-src domains (configurable via env)
        ...(env('CSP_ADDITIONAL_IMG_SRC') ? [
            [Directive::IMG, array_filter(explode(',', env('CSP_ADDITIONAL_IMG_SRC')))],
        ] : []),

        // Additional script-src domains (configurable via env)
        ...(env('CSP_ADDITIONAL_SCRIPT_SRC') ? [
            [Directive::SCRIPT, array_filter(explode(',', env('CSP_ADDITIONAL_SCRIPT_SRC')))],
        ] : []),

        // Additional style-src domains (configurable via env)
        ...(env('CSP_ADDITIONAL_STYLE_SRC') ? [
            [Directive::STYLE, array_filter(explode(',', env('CSP_ADDITIONAL_STYLE_SRC')))],
        ] : []),
    ],

    /*
     * These presets which will be put in a report-only policy. This is great for testing out
     * a new policy or changes to existing CSP policy without breaking anything.
     */
    'report_only_presets' => [
        // Moved to enforcement mode above
    ],

    /**
     * Register additional global report-only CSP directives here.
     */
    'report_only_directives' => [
        // [Directive::SCRIPT, [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE]],
    ],

    /*
     * All violations against a policy will be reported to this url.
     * Set to null to disable violation reporting.
     */
    'report_uri' => null,

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => env('CSP_ENABLED', true),

    /**
     * Headers will be added when Vite is hot reloading.
     */
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', true),

    /*
     * The class responsible for generating the nonces used in inline tags and headers.
     */
    'nonce_generator' => RandomString::class,

    /*
     * Set false to disable automatic nonce generation and handling.
     * This is useful when you want to use 'unsafe-inline' for scripts/styles
     * and cannot add inline nonces.
     * Note that this will make your CSP policy less secure.
     */
    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
];
