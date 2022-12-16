<?php

namespace App\Models\Bioschema;

use Spatie\SchemaOrg\BaseType;

/**
 * Any biological, chemical, or biochemical thing. For example: a protein;
 * a gene; a chemical; a synthetic chemical.
 *
 * @see https://bioschemas.org/types/BioChemEntity/0.7-RELEASE-2019_06_19
 * @link http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources
 */
class BioChemEntity extends BaseType
{
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
