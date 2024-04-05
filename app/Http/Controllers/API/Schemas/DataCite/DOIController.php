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
     * @param  App\Services\DOI\DOIService  $doiService
     * @return void
     */
    public function __construct(DOIService $doiService)
    {
        $this->doiService = $doiService;
    }

    /**
     * Update Model's DataCite metadata
     *
     * @param  Illuminate\Http\Request  $request
     * @param  string  $identifier
     * @return void
     */
    public function update(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $model = $resolvedModel['model'];

        // Call the updateDOI function
        $model->updateDOIMetadata($this->doiService);
    }
}
