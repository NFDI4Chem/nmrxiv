<?php

namespace App\Services\DOI;

use Config;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataCite implements DOIService
{
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => Config::get('doi.'.Config::get('doi.default').'.endpoint'),
            'auth' => [Config::get('doi.'.Config::get('doi.default').'.username'), Config::get('doi.'.Config::get('doi.default').'.secret')],
            'headers' => [
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->prefix = Config::get('doi.'.Config::get('doi.default').'.prefix');
    }

    /**
     * Allow tests to inject a pre-built Guzzle client (for `MockHandler`)
     * without going through the network.
     */
    public function setHttpClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * Returns a list of DOIs
     */
    public function getDOIs()
    {
        $prefix = Config::get('doi.'.Config::get('doi.default').'.prefix');
        $response = $this->client->get('/dois?prefix='.$prefix);

        return $response->getBody();
    }

    public function getDOI($doi)
    {
        $response = $this->client->get('/dois/'.urlencode($doi));

        return $response->getBody();
    }

    public function createDOI($suffix, $metadata = [])
    {
        $attributes = [
            'doi' => $this->prefix.'/'.Config::get('app.name').'.'.$suffix,
            'prefix' => $this->prefix,
            'suffix' => Config::get('app.name').'.'.$suffix,
            'publisher' => Config::get('app.name'),
            'publicationYear' => now()->format('Y'),
            'language' => 'en',
        ];

        foreach ($metadata as $key => $value) {
            $attributes[$key] = $value;
        }

        $body = [
            'data' => [
                'type' => 'dois',
                'attributes' => $attributes,
            ],
        ];

        $response = $this->client->post('/dois',
            [RequestOptions::JSON => $body]
        );

        $stream = $response->getBody();
        $contents = $stream->getContents();

        return json_decode($contents, true);
    }

    /**
     * Register a DataCite DOI using an exact, pre-built DOI string.
     *
     * Unlike `createDOI`, this does NOT compose the DOI from the configured
     * prefix and `app.name` — it trusts the caller's value verbatim. Used
     * by `HasDOI::linkProvisionalDoi` to register the existing
     * `projects.provisional_doi` placeholder as a real findable DataCite
     * record so the provisional citation keeps resolving.
     *
     * @return array<string, mixed>
     */
    public function createCustomDOI(string $doi, array $metadata = []): array
    {
        [$prefix, $suffix] = array_pad(explode('/', $doi, 2), 2, null);

        $attributes = [
            'doi' => $doi,
            'prefix' => $prefix,
            'suffix' => $suffix,
            'publisher' => Config::get('app.name'),
            'publicationYear' => now()->format('Y'),
            'language' => 'en',
        ];

        foreach ($metadata as $key => $value) {
            $attributes[$key] = $value;
        }

        $body = [
            'data' => [
                'type' => 'dois',
                'attributes' => $attributes,
            ],
        ];

        $response = $this->client->post('/dois',
            [RequestOptions::JSON => $body]
        );

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true) ?? [];
    }

    /**
     * Fetch the `relatedIdentifiers` array currently stored on a DataCite
     * record. Returns an empty array when the record has none or the
     * record cannot be loaded — callers should treat the result as a
     * safe baseline to merge new entries into.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRelatedIdentifiers(string $doi): array
    {
        $response = $this->client->get('/dois/'.urlencode($doi));
        $payload = json_decode($response->getBody()->getContents(), true);

        $relatedIdentifiers = $payload['data']['attributes']['relatedIdentifiers'] ?? [];

        return is_array($relatedIdentifiers) ? $relatedIdentifiers : [];
    }

    /**
     * PUT a fresh `relatedIdentifiers` array (and optionally an updated
     * `url`) onto an existing DataCite record. Snapshots the pre-PUT
     * record to `storage/logs/datacite-audit/{doi}/{timestamp}.json`
     * before sending so any malformed payload can be rolled back.
     *
     * @param  array<int, array<string, mixed>>  $relatedIdentifiers
     * @return array<string, mixed>
     */
    public function putRelatedIdentifiers(string $doi, array $relatedIdentifiers, ?string $url = null): array
    {
        $this->snapshotForAudit($doi);

        $attributes = ['relatedIdentifiers' => $relatedIdentifiers];
        if ($url !== null && $url !== '') {
            $attributes['url'] = $url;
        }

        $body = [
            'data' => [
                'type' => 'dois',
                'attributes' => $attributes,
            ],
        ];

        $response = $this->client->put('/dois/'.urlencode($doi),
            [RequestOptions::JSON => $body]
        );

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true) ?? [];
    }

    /**
     * Best-effort GET-and-stash of a record's current state. Failures are
     * logged and swallowed — losing an audit snapshot must not stop a
     * legitimate metadata PUT from succeeding.
     */
    private function snapshotForAudit(string $doi): void
    {
        try {
            $response = $this->client->get('/dois/'.urlencode($doi));
            $body = $response->getBody()->getContents();

            $disk = Storage::disk('local');
            $path = 'datacite-audit/'.str_replace('/', '_', $doi).'/'.now()->format('Ymd_His_u').'.json';
            $disk->put($path, $body);
        } catch (\Throwable $e) {
            Log::warning('DataCite audit snapshot failed', [
                'doi' => $doi,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update DataCite metadata based on DOI
     *
     * @param  string  $doi
     * @param  array  $metadata
     * @return array $contents
     */
    public function updateDOI($doi, $metadata = [])
    {
        $attributes = [];
        foreach ($metadata as $key => $value) {
            $attributes[$key] = $value;
        }

        $body = [
            'data' => [
                'type' => 'dois',
                'attributes' => $attributes,
            ],
        ];

        $response = $this->client->put('/dois/'.urlencode($doi),
            [RequestOptions::JSON => $body]
        );

        $stream = $response->getBody();
        $contents = $stream->getContents();
        $contents = json_decode($contents, true);

        return $contents;
    }

    public function deleteDOI($doi)
    {
        $response = $this->client->delete('/dois/'.urlencode($doi));

        return $response->getBody();
    }

    public function getDOIActivity($doi)
    {
        $response = $this->client->get('/dois/'.urlencode($doi).'/activities');

        return $response->getBody();
    }
}
