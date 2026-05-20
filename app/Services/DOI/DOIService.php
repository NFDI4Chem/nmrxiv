<?php

namespace App\Services\DOI;

interface DOIService
{
    public function getDOIs();

    public function createDOI($suffix, $attributes = []);

    public function getDOI($doi);

    public function updateDOI($doi, $attributes = []);

    public function deleteDOI($doi);

    public function getDOIActivity($doi);

    /**
     * Register a DataCite DOI using an exact, pre-built DOI string.
     *
     * @return array<string, mixed>
     */
    public function createCustomDOI(string $doi, array $metadata = []): array;

    /**
     * Fetch the `relatedIdentifiers` array currently stored on a DataCite record.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRelatedIdentifiers(string $doi): array;

    /**
     * Replace the `relatedIdentifiers` array (and optionally the `url`)
     * on an existing DataCite record.
     *
     * @param  array<int, array<string, mixed>>  $relatedIdentifiers
     * @return array<string, mixed>
     */
    public function putRelatedIdentifiers(string $doi, array $relatedIdentifiers, ?string $url = null): array;
}
