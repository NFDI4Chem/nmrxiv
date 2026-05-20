<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

use App\Models\Dataset;
use App\Models\NMRium;
use Illuminate\Support\Facades\DB;
use Spatie\SchemaOrg\Schema;

class BioschemasHelper
{
    /**
     * Use Schema.org PropertyValue type to represent terms from controlled vocabularies.
     *
     * @link https://schema.org/PropertyValue
     *
     * @param  string  $name
     * @param  string  $id
     * @param  string  $value
     * @param  string  $unitUrl
     * @return object $propertyValueSchema
     */
    public static function preparePropertyValue($name, $id, $value, $unitUrl)
    {
        $propertyValueSchema = Schema::PropertyValue();
        $propertyValueSchema->name($name);
        $propertyValueSchema->propertyID($id);
        $propertyValueSchema->value($value);
        $propertyValueSchema->unitCode($unitUrl);

        return $propertyValueSchema;
    }

    /**
     * Use Schema.org DefinedTerm type to represent terms from controlled vocabularies.
     *
     * @link https://schema.org/DefinedTerm
     *
     * @param  string  $name
     * @param  array  $alternameName
     * @param  string  $identifier
     * @param  string  $url
     * @param  object  $inDefinedTermSet
     * @return object $definedTermSchema
     */
    public static function prepareDefinedTerm($name, $alternateName, $identifier, $url, $inDefinedTermSet)
    {
        $definedTermSchema = Schema::DefinedTerm();
        $definedTermSchema->name($name);
        $definedTermSchema->alternateName($alternateName);
        $definedTermSchema->identifier($identifier);
        $definedTermSchema->url($url);
        $definedTermSchema->inDefinedTermSet($inDefinedTermSet);

        return $definedTermSchema;
    }

    /**
     * Use Schema.org DefinedTermSet type to represent controlled vocabularies.
     *
     * @link https://schema.org/DefinedTermSet
     *
     * @param  string  $name
     * @param  string  $url
     * @return object $definedTermSetSchema
     */
    public static function prepareDefinedTermSet($name, $url)
    {
        $definedTermSetSchema = Schema::DefinedTermSet();
        $definedTermSetSchema->name($name);
        $definedTermSetSchema->url($url);

        return $definedTermSetSchema;
    }

    /**
     * Use Schema.org Person type to represent a person.
     *
     * @link https://schema.org/Person
     *
     * @param  string  $id
     * @param  string  $givenName
     * @param  string  $familyName
     * @param  string  $email
     * @param  string  $affiliation
     * @return object $personSchema
     */
    public static function preparePerson($id, $givenName, $familyName, $email, $affiliation)
    {
        $personSchema = Schema::Person();
        $personSchema->identifier($id);
        $personSchema->givenName($givenName);
        $personSchema->familyName($familyName);
        $personSchema->email($email);
        $personSchema->affiliation($affiliation);

        return $personSchema;
    }

    /**
     * Use Schema.org Person type to represent the authors of a model.
     *
     * @link https://schema.org/Person.
     *
     * @param  object  $model
     * @return array $authorsSchemas
     */
    public static function prepareAuthors($model)
    {
        $helper = new self;
        $authorsSchemas = [];
        foreach ($model->authors as &$author) {
            $authorSchema = $helper->preparePerson($author->orcid_id, $author->given_name, $author->family_name, $author->email_id, $author->affiliation);
            array_push($authorsSchemas, $authorSchema);
        }

        return $authorsSchemas;
    }

    /**
     * Use Schema.org CreativeWork type to represent a model's citations.
     *
     * @link https://schema.org/CreativeWork
     *
     * @param  object  $model
     * @return array $citationsSchemas
     */
    public static function prepareCitations($model)
    {
        $citationsSchemas = [];
        foreach ($model->citations as &$citation) {
            $citationSchema = Schema::CreativeWork();
            $citationSchema->author($citation->authors);
            $citationSchema->headline($citation->title);
            $citationSchema->identifier($citation->doi);
            array_push($citationsSchemas, $citationSchema);
        }

        return $citationsSchemas;
    }

    /**
     * Use Schema.org DataDownload type to represent Dataset download details.
     *
     * @link https://schema.org/DataDownload
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $DataDownloadSchema
     */
    public static function prepareDataDownload($dataset)
    {
        $url = config('app.url');
        $user = $dataset->owner->username;
        if (property_exists($dataset, 'project')) {
            $slug = $dataset->project->slug;
            $name = $dataset->project->name;
        } else {
            $slug = $dataset->study->slug;
            $name = $dataset->study->name;
        }
        $contentURL = $url.'/'.$user.'/datasets/'.$slug;

        $DataDownloadSchema = Schema::DataDownload();
        $DataDownloadSchema->name($name);
        $DataDownloadSchema->encodingFormat('zip');
        $DataDownloadSchema->contentURL($contentURL);

        return $DataDownloadSchema;
    }

    /**
     * Use Schema.org Organization type to represent nmrXiv as a publisher.
     *
     * @link https://schema.org/Organization
     *
     * @return object $publisherSchema
     */
    public static function preparePublisher()
    {
        $publisherSchema = Schema::Organization();
        $publisherSchema->name(config('app.name'));
        $publisherSchema->url(config('app.url'));

        return $publisherSchema;
    }

    /**
     * Use Schema.org DataCatalog type with only few properties to represent nmrXiv as the repository to be
     * included in the dataset schema.
     *
     * @link https://schema.org/DataCatalog
     *
     * @return object $dataCatalogSchema
     */
    public static function prepareDataCatalogLite()
    {
        $dataCatalogSchema = Schema::DataCatalog();
        $dataCatalogSchema->name(config('app.name'));
        $dataCatalogSchema->url(config('app.url'));

        return $dataCatalogSchema;
    }

    /**
     * Get NMRium spectrum `info` for a dataset (read-only).
     *
     * Uses the dataset's own `nmrium` row when present. Otherwise returns the
     * first matching spectrum's `info` from the parent study's stored NMRium
     * JSON without persisting — persistence happens only when the study
     * payload is saved via {@see self::syncDatasetNmriumFromStudyPayload()}.
     */
    public static function getNMRiumInfo(Dataset $dataset): ?object
    {
        $dataset->loadMissing([
            'nmrium',
            'study.nmrium',
            'study.sample',
            'study.draft',
            'fsObject',
            'study.fsObject',
        ]);

        if ($dataset->nmrium) {
            $rawInfo = self::extractPrimaryInfoFromNmriumInfo($dataset->nmrium->nmrium_info);
            $normalized = self::normalizeSpectrumInfo($rawInfo);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        $matched = self::collectStudySpectraMatchingDataset($dataset);
        if ($matched === []) {
            return null;
        }

        $first = $matched[0];
        $info = is_array($first) ? ($first['info'] ?? null) : null;

        return self::normalizeSpectrumInfo($info);
    }

    /**
     * Collect spectra from a study-level NMRium JSON payload that belong to
     * this dataset (robust path match on `FileSystemObject::relative_url`).
     *
     * @param  array<string, mixed>  $nmriumInfo
     * @return list<array<string, mixed>>
     */
    public static function collectStudySpectraMatchingDatasetFromPayload(Dataset $dataset, array $nmriumInfo): array
    {
        $study = $dataset->study;
        if (! $study) {
            return [];
        }

        if (! isset($nmriumInfo['data']['spectra']) || ! is_array($nmriumInfo['data']['spectra'])) {
            return [];
        }

        $studyFSObject = $study->fsObject;
        $datasetFSObject = $dataset->fsObject;
        if (! $studyFSObject || ! $datasetFSObject) {
            return [];
        }

        $draft = $study->relationLoaded('draft') ? $study->draft : $study->draft()->first();
        $isChemotion = $draft && ($draft->eln === 'chemotion');
        $parentName = $isChemotion ? optional($datasetFSObject->parent)->name : null;
        if ($isChemotion && $parentName === null) {
            return [];
        }

        $datasetRelativeUrl = $datasetFSObject->relative_url;
        if (! is_string($datasetRelativeUrl) || $datasetRelativeUrl === '') {
            $datasetRelativeUrl = $isChemotion
                ? '/'.$studyFSObject->name.'/'.$parentName.'/'.$datasetFSObject->name
                : '/'.$studyFSObject->name.'/'.$datasetFSObject->name;
        }
        $path = rtrim($datasetRelativeUrl, '/');
        $isDatasetFile = $datasetFSObject->type === 'file';
        $needle = $isDatasetFile ? $path : $path.'/';

        $matchedSpectra = [];
        foreach ($nmriumInfo['data']['spectra'] as $spectra) {
            if (! is_array($spectra)) {
                continue;
            }
            $selector = $spectra['sourceSelector'] ?? $spectra['selector'] ?? [];
            $files = $selector['files'] ?? [];
            if (! is_array($files)) {
                continue;
            }
            $hit = false;
            foreach ($files as $file) {
                if (! is_string($file)) {
                    continue;
                }
                $pathsMatch = $isDatasetFile
                    ? str_ends_with($file, $needle)
                    : str_contains($file, $needle);
                if ($pathsMatch) {
                    $hit = true;
                    break;
                }
            }
            if ($hit) {
                $matchedSpectra[] = $spectra;
            }
        }

        return $matchedSpectra;
    }

    /**
     * Collect spectra entries from the parent study's stored NMRium JSON.
     *
     * @return list<array<string, mixed>>
     */
    public static function collectStudySpectraMatchingDataset(Dataset $dataset): array
    {
        $study = $dataset->study;
        if (! $study || ! $study->nmrium) {
            return [];
        }

        $nmriumInfo = $study->nmrium->nmrium_info;
        if (! is_array($nmriumInfo)) {
            return [];
        }

        return self::collectStudySpectraMatchingDatasetFromPayload($dataset, $nmriumInfo);
    }

    /**
     * Persist matched study spectra onto the dataset. Intended for the study
     * NMRium save path only — pass the merged study JSON (e.g. request payload
     * after sample/molecule merge) so matching uses the same data being stored.
     *
     * @param  array<string, mixed>  $mergedStudyNmriumInfo
     * @return list<array<string, mixed>> Matched spectrum entries (empty if none)
     */
    public static function syncDatasetNmriumFromStudyPayload(Dataset $dataset, array $mergedStudyNmriumInfo): array
    {
        $dataset->loadMissing([
            'nmrium',
            'study',
            'fsObject',
            'study.fsObject',
            'study.draft',
        ]);

        $matched = self::collectStudySpectraMatchingDatasetFromPayload($dataset, $mergedStudyNmriumInfo);
        if ($matched === []) {
            return [];
        }

        $base = json_decode(json_encode($mergedStudyNmriumInfo), true);
        if (! is_array($base)) {
            return [];
        }

        $base['data']['spectra'] = $matched;

        DB::transaction(function () use ($dataset, $base) {
            $dataset->refresh();
            $dataset->load('nmrium');

            if ($dataset->nmrium) {
                $dataset->nmrium->nmrium_info = $base;
                $dataset->nmrium->save();
            } else {
                $nmrium = NMRium::create([
                    'nmrium_info' => $base,
                ]);
                $dataset->nmrium()->save($nmrium);
            }

            $dataset->forceFill(['has_nmrium' => true])->save();
        });

        return $matched;
    }

    /**
     * @param  mixed  $nmriumInfo
     */
    private static function extractPrimaryInfoFromNmriumInfo($nmriumInfo): mixed
    {
        if ($nmriumInfo === null) {
            return null;
        }
        $decoded = is_array($nmriumInfo)
            ? $nmriumInfo
            : json_decode(json_encode($nmriumInfo), true);
        if (! is_array($decoded)) {
            return null;
        }
        if (isset($decoded['data']['spectra'][0]['info'])) {
            return $decoded['data']['spectra'][0]['info'];
        }

        return null;
    }

    private static function normalizeSpectrumInfo(mixed $info): ?object
    {
        if ($info === null) {
            return null;
        }
        if (is_object($info)) {
            return $info;
        }
        if (is_array($info)) {
            $obj = json_decode(json_encode($info), false);

            return is_object($obj) ? $obj : null;
        }

        return null;
    }

    /**
     * Use Schema.org CreativeWork type to represent Schema.org and ISA types that an object conforms to.
     *
     * @link https://schema.org/CreativeWork
     *
     * @param  array  $urls
     * @return array $confromsToList
     */
    public static function conformsTo($urls)
    {
        $confromsToList = [];
        foreach ($urls as &$url) {
            $creativeWorkSchema = Schema::CreativeWork();
            $creativeWorkSchema['@id'] = $url;
            array_push($confromsToList, $creativeWorkSchema);
        }

        return $confromsToList;
    }

    /**
     * Get the tags names (keywords) of a model.
     *
     * @param  object  $model
     * @return object $tags
     */
    public static function getTags($model)
    {
        $tags = [];
        foreach ($model->tags as &$tag) {
            $tag = $tag->name;
            array_push($tags, $tag);
        }

        return $tags;
    }
}
