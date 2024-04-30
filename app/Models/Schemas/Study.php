<?php

namespace App\Models\Schemas;

use Spatie\SchemaOrg\BaseType;

/**
 * A body of structured information describing some topic(s) of interest.
 *
 * @see https://bioschemas.org/profiles/Study/0.2-DRAFT
 * @link http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources
 */
class Study extends BaseType
{
    /**
     * The author of this content or rating. Please note that author is special
     * in that HTML 5 provides a special mechanism for indicating authorship via
     * the rel tag. That is equivalent to this and may be used interchangeably.
     *
     * @param  \Spatie\SchemaOrg\Contracts\OrganizationContract|\Spatie\SchemaOrg\Contracts\OrganizationContract[]|\Spatie\SchemaOrg\Contracts\PersonContract|\Spatie\SchemaOrg\Contracts\PersonContract[]  $author
     * @return static
     *
     * @see https://schema.org/author
     */
    public function author($author)
    {
        return $this->setProperty('author', $author);
    }

    /**
     * Date of first broadcast/publication.
     *
     * @param  \DateTimeInterface|\DateTimeInterface[]  $datePublished
     * @return static
     *
     * @see https://schema.org/datePublished
     */
    public function datePublished($datePublished)
    {
        return $this->setProperty('datePublished', $datePublished);
    }

    /**
     * A description of the item.
     *
     * @param  string|string[]  $description
     * @return static
     *
     * @see https://schema.org/description
     */
    public function description($description)
    {
        return $this->setProperty('description', $description);
    }

    /**
     * The identifier property represents any kind of identifier for any kind of
     * [[Thing]], such as ISBNs, GTIN codes, UUIDs etc. Schema.org provides
     * dedicated properties for representing many of these, either as textual
     * strings or as URL (URI) links. See [background
     * notes](/docs/datamodel.html#identifierBg) for more details.
     *
     * @param  \Spatie\SchemaOrg\Contracts\PropertyValueContract|\Spatie\SchemaOrg\Contracts\PropertyValueContract[]|string|string[]  $identifier
     * @return static
     *
     * @see https://schema.org/identifier
     */
    public function identifier($identifier)
    {
        return $this->setProperty('identifier', $identifier);
    }

    /**
     * The name of the item.
     *
     * @param  string|string[]  $name
     * @return static
     *
     * @see https://schema.org/name
     */
    public function name($name)
    {
        return $this->setProperty('name', $name);
    }

    /**
     * Define the domain of the study. For example, the domain could be astrophysics,
     * functional genomics or earth science. Those domains can also have an ontology
     * reference.
     *
     * @param  \Spatie\SchemaOrg\Contracts\PropertyValueContract|\Spatie\SchemaOrg\Contracts\PropertyValueContract[]|string|string[]  $studyDomain
     * @return static
     */
    public function studyDomain($studyDomain)
    {
        return $this->setProperty('studyDomain', $studyDomain);
    }

    /**
     * A subject of the study, i.e. one of the medical conditions, therapies, devices,
     * drugs, etc. investigated by the study.
     *
     * @param  \Spatie\SchemaOrg\Contracts\BioChemEntityContract|\Spatie\SchemaOrg\Contracts\MedicalEntityContract[]  $studySubject
     * @return static
     */
    public function studySubject($studySubject)
    {
        return $this->setProperty('studySubject', $studySubject);
    }

    /**
     * The subject matter of the content.
     *
     * @param  \Spatie\SchemaOrg\Contracts\ThingContract|\Spatie\SchemaOrg\Contracts\ThingContract[]  $about
     * @return static
     *
     * @see https://schema.org/about
     * @link https://github.com/schemaorg/schemaorg/issues/1670
     */
    public function about($about)
    {
        return $this->setProperty('about', $about);
    }

    /**
     * A property-value pair representing an additional characteristics of the
     * entitity, e.g. a product feature or another characteristic for which there
     * is no matching property in schema.org. Note: Publishers should be aware that
     * applications designed to use specific schema.org properties (e.g. http://schema.org/width,
     * http://schema.org/color, http://schema.org/gtin13, …) will typically expect
     * such data to be provided using those properties, rather than using the generic
     * property/value mechanism.
     *
     * @param  \Spatie\SchemaOrg\Contracts\PropertyValueContract|\Spatie\SchemaOrg\Contracts\PropertyValueContract[]  $additionalProperty
     * @return static
     *
     * @see https://schema.org/additionalProperty
     */
    public function additionalProperty($additionalProperty)
    {
        return $this->setProperty('additionalProperty', $additionalProperty);
    }

    /**
     * A citation or reference to another creative work, such as another
     * publication, web page, scholarly article, etc.
     *
     * @param  \Spatie\SchemaOrg\Contracts\CreativeWorkContract|\Spatie\SchemaOrg\Contracts\CreativeWorkContract[]|string|string[]  $citation
     * @return static
     *
     * @see https://schema.org/citation
     */
    public function citation($citation)
    {
        return $this->setProperty('citation', $citation);
    }

    /**
     * The creator/author of this CreativeWork. This is the same as the Author
     * property for CreativeWork.
     *
     * @param  \Spatie\SchemaOrg\Contracts\OrganizationContract|\Spatie\SchemaOrg\Contracts\OrganizationContract[]|\Spatie\SchemaOrg\Contracts\PersonContract|\Spatie\SchemaOrg\Contracts\PersonContract[]  $creator
     * @return static
     *
     * @see https://schema.org/creator
     */
    public function creator($creator)
    {
        return $this->setProperty('creator', $creator);
    }

    /**
     * The date on which the CreativeWork was created or the item was added to a
     * DataFeed.
     *
     * @param  \DateTimeInterface|\DateTimeInterface[]  $dateCreated
     * @return static
     *
     * @see https://schema.org/dateCreated
     */
    public function dateCreated($dateCreated)
    {
        return $this->setProperty('dateCreated', $dateCreated);
    }

    /**
     * The end date and time of the item (in ISO 8601 date format).
     *
     * @param  \DateTimeInterface|\DateTimeInterface[]  $endDate
     * @return static
     *
     * @see https://schema.org/endDate
     */
    public function endDate($endDate)
    {
        return $this->setProperty('endDate', $endDate);
    }

    /**
     * The start date and time of the item (in ISO 8601 date format).
     *
     * @param  \DateTimeInterface|\DateTimeInterface[]  $startDate
     * @return static
     *
     * @see https://schema.org/startDate
     */
    public function startDate($startDate)
    {
        return $this->setProperty('startDate', $startDate);
    }

    /**
     * Keywords or tags used to describe some item. Multiple textual entries in
     * a keywords list are typically delimited by commas, or by repeating the
     * property.
     *
     * @param  \Spatie\SchemaOrg\Contracts\DefinedTermContract|\Spatie\SchemaOrg\Contracts\DefinedTermContract[]|string|string[]  $keywords
     * @return static
     *
     * @see https://schema.org/keywords
     */
    public function keywords($keywords)
    {
        return $this->setProperty('keywords', $keywords);
    }

    /**
     * TA process performed as part of an experiment or wider study, i.e. intentionally
     * designed. These processes can have ontology URL attached to.
     *
     * @param  \Spatie\SchemaOrg\Contracts\PropertyValueContract|\Spatie\SchemaOrg\Contracts\PropertyValueContract[]|string|string[]  $studyProcess
     * @return static
     */
    public function studyProcess($studyProcess)
    {
        return $this->setProperty('studyProcess', $studyProcess);
    }

    /**
     * URL of the item.
     *
     * @param  string|string[]  $url
     * @return static
     *
     * @see https://schema.org/url
     */
    public function url($url)
    {
        return $this->setProperty('url', $url);
    }
}
