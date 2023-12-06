<?php

namespace App\Models\Schemas;

use Spatie\SchemaOrg\Schema;

/**
 * Factory class for all Schema.org types.
 */
class Bioschemas extends Schema
{
    public static function study(): Study
    {
        return new Study();
    }
}
