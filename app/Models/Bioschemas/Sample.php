<?php

namespace App\Models\Bioschemas;

use Spatie\SchemaOrg\BaseType;

/**
 * To deliver on the identified use cases for samples, we have identified a minimal set
 *  of properties to encapsulate identification, linking, and metadata descriptions.
 * Some of these properties are existing standard schema.org properties, others require Bioschemas
 * extensions. Table 1 outlines the minimal set of properties for the ‘Sample’ concept and Table 2
 * shows our recommendations for use of the ‘PropertyValue’ concept to markup additional characteristics of
 * a sample described within a sample page. We also propose a new concept, ‘Biomedical Code’, which is a
 * generalisation of the existing ‘Medical Code’ concept defined in the health-lifesci.schema.org extension.
 *
 * @see https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
 * @link http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources
 */
class Sample extends BaseType
{
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
