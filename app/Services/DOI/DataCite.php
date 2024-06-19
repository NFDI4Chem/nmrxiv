<?php

namespace App\Services\DOI;

use Config;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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
     * Update DataCite metadata based on DOI
     *
     * @param  string  $doi
     * @param  array  $metadata
     * @return array $contents
     */
    public function updateDOI($doi, $metadata = [])
    {
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
