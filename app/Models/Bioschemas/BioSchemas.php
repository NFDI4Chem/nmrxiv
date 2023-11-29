<?php

namespace App\Models\Bioschemas;

use Spatie\SchemaOrg\Schema;

/**
 * Factory class for all Schema.org types.
 */
class BioSchemas extends Schema
{
    public static function study(): Study
    {
        return new Study();
    }
}
