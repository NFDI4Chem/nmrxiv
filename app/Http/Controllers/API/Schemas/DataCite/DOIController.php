<?php

namespace App\Http\Controllers\API\Schemas\DataCite;

use App\Http\Controllers\Controller;
use App\Services\DOI\DOIService;
use Illuminate\Http\Request;

class DOIController extends Controller
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(DOIService $doiService)
    {
        $this->doiService = $doiService;
    }

    public function update(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $model = $resolvedModel['model'];

        // Call the updateDOI function
        $model->updateDOIMetadata($this->doiService);
    }
}
