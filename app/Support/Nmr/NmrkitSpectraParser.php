<?php

namespace App\Support\Nmr;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * HTTP client for NMRKit's spectra parser.
 *
 * Uploads go through `/parse/url` because the production `/parse/file`
 * endpoint currently answers with an empty body.
 */
class NmrkitSpectraParser
{
    /**
     * Parse the spectra behind a URL, auto-processing FIDs and detecting peaks and ranges.
     *
     * @return list<array<string, mixed>> NMRium spectrum objects.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function parseUrl(string $url, bool $autoDetect = true): array
    {
        $response = Http::timeout(config('nmrxiv.spectra_parsing.api_timeout', 300))
            ->post(config('nmrxiv.spectra_parsing.nmrkit_api_url'), [
                'url' => $url,
                'capture_snapshot' => false,
                'auto_processing' => $autoDetect,
                'auto_detection' => $autoDetect,
                'raw_data' => false,
            ])
            ->throw();

        $spectra = $response->json('nmriumState.data.spectra') ?? $response->json('data.spectra');

        return is_array($spectra) ? array_values($spectra) : [];
    }
}
