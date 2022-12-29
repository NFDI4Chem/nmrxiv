<?php

namespace App\Models\Bioschema;

use Spatie\SchemaOrg\BaseType;

/**
 * A biological material entity that is representative of a whole.
 * Comments: Typically samples are intended to be representative of the
 * whole or aspects thereof. Examples of samples include biomedical
 * samples (blood, urine) and plant specimens held at herbaria.
 *
 * @see https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19
 * @link http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources
 */
class BioSample extends BaseType
{
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
     * Indicates a BioChemEntity that (in some sense) has this BioChemEntity as a part.
     * Inverse property: isPartOfBioChemEntity
     *
     * @param  \Spatie\SchemaOrg\Contracts\CreativeWorkContract|\Spatie\SchemaOrg\Contracts\CreativeWorkContract[]  $hasPart
     * @return static
     *
     * @link http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_bibex
     */
    public function hasBioChemEntityPart($hasBioChemEntityPart)
    {
        return $this->setProperty('hasBioChemEntityPart', $hasBioChemEntityPart);
    }

    /**
     *  An alias for the item.
     *
     * @param  string|string[]  $alternateName
     * @return static
     *
     * @see https://schema.org/name
     */
    public function alternateName($alternateName)
    {
        return $this->setProperty('alternateName', $alternateName);
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
}
