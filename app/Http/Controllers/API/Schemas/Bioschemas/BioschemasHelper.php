<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

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
        $helper = new self();
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
            $citationSchema->abstract($citation->abstract);
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
        $url = env('APP_URL');
        $user = $dataset->owner;
        $slug = $dataset->project->slug;
        $contentURL = $url.'/'.$user.'/datasets/'.$slug;

        $DataDownloadSchema = Schema::DataDownload();
        $DataDownloadSchema->name($dataset->project->name);
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
        $publisherSchema->name(env('APP_NAME'));
        $publisherSchema->url(env('APP_URL'));

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
        $dataCatalogSchema->name(env('APP_NAME'));
        $dataCatalogSchema->url(env('APP_URL'));

        return $dataCatalogSchema;
    }

    /**
     * Get NMRium info from a dataset.
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $info
     */
    public static function getNMRiumInfo($dataset)
    {
        $info = null;
        $nmrium = $dataset->nmrium;
        if (! $nmrium) {
            $study = $dataset->study;
            if ($study->nmrium) {
                $NMRiumInfo = json_decode($study->nmrium->nmrium_info);
                foreach ($NMRiumInfo->data->spectra as $spectra) {
                    $fileSource = $spectra->sourceSelector->files[0];
                    $fileName = pathinfo($fileSource);
                    if ($fileName['basename'] == $dataset->fsObject->name) {
                        $info = $spectra->info;
                    }
                }
            }
        } else {
            $NMRiumInfo = json_decode($nmrium->nmrium_info);
            $spectra = $NMRiumInfo->data->spectra[0];
            $info = $spectra->info;
        }

        return $info;
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
