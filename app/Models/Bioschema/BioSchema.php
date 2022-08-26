<?php

namespace App\Models\Bioschema;

use App\Models\Bioschema\Study;
use \Spatie\SchemaOrg\Schema;

/**
 * Factory class for all Schema.org types.
 */
class BioSchema extends Schema
{
    public static function study(): Study
    {
        return new Study();
    }
}