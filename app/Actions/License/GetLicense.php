<?php

namespace App\Actions\License;

use App\Models\License;

class GetLicense
{
    /**
     * Return License object by Id.
     */
    public function getLicensebyId($id)
    {
        $license = License::select('id', 'title', 'description')->where('id', $id)->get();

        return $license;
    }
}
