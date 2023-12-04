<?php

namespace App\Models\Bioschemas;

use Spatie\SchemaOrg\Schema;

/**
 * Factory class for all Schema.org types.
 */
class Bioschema extends Schema
{
    public static function study(): Study
    {
        return new Study();
    }
}
