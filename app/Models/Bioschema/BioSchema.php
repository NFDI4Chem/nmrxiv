<?php

namespace App\Models\Bioschema;

use Spatie\SchemaOrg\Schema;

/**
 * Factory class for all Schema.org types.
 */
class BioSchema extends Schema
{
    public static function study(): Study
    {
        return new Study();
    }

    public static function sample(): Sample
    {
        return new Sample();
    }

    public static function bioSample(): BioSample
    {
        return new BioSample();
    }
}
