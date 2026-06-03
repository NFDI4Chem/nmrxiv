<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Configured via the TRUSTED_PROXIES env var. Defaults to "*" only in
     * production where a real reverse proxy terminates TLS. In local/Docker
     * dev we trust nothing so Laravel\Sentinel does not flag every request
     * coming in via the host interface as a public-proxy access attempt.
     *
     * @var array|string|null
     */
    protected $proxies;

    public function __construct()
    {
        $configured = config('app.trusted_proxies', env('TRUSTED_PROXIES'));

        if ($configured === null) {
            $this->proxies = app()->environment('production') ? '*' : null;

            return;
        }

        $this->proxies = $configured === '*'
            ? '*'
            : array_filter(array_map('trim', explode(',', (string) $configured)));
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB;
}
