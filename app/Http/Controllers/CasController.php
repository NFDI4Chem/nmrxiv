<?php

namespace App\Http\Controllers;

use App\Services\CAS\CASService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CasController extends Controller
{
    public function __construct(
        private CASService $casService
    ) {}

    /**
     * Proxy CAS Common Chemistry API requests to avoid CORS issues
     */
    public function fetchCasData(Request $request): JsonResponse
    {
        $request->validate([
            'cas_rn' => 'required|string|max:20',
        ]);

        $casNumber = $request->input('cas_rn');

        // Check if API token is configured
        if (! Config::get('services.cas.api_token')) {
            return response()->json([
                'error' => 'CAS API token not configured',
            ], 500);
        }

        try {
            $data = $this->casService->getCASDetails($casNumber);

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], match (true) {
                str_contains($e->getMessage(), 'authentication failed') => 401,
                str_contains($e->getMessage(), 'Details not found') => 404,
                str_contains($e->getMessage(), 'Rate limit') => 429,
                str_contains($e->getMessage(), 'server error') => 502,
                default => 400,
            });
        }
    }
}
