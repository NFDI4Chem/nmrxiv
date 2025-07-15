<?php

namespace App\Actions\License;

use App\Models\License;

class GetLicense
{
    /**
     * Return License object by ID.
     */
    public function getLicenseById(int $id): ?License
    {
        return License::select('id', 'title', 'description')
            ->where('id', $id)
            ->first();
    }
}
