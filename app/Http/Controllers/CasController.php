<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CasController extends Controller
{
    private const DEFAULT_BASE_URL = 'https://commonchemistry.cas.org/api';

    private const REQUEST_TIMEOUT = 30;

    /**
     * Proxy CAS Common Chemistry API requests to avoid CORS issues
     */
    public function fetchCasData(Request $request): JsonResponse
    {
        $request->validate([
            'cas_rn' => 'required|string|max:20',
        ]);

        $casNumber = $request->input('cas_rn');
        $token = config('services.cas.api_token') ?? env('CAS_API_TOKEN');
        $baseUrl = config('services.cas.base_url') ?? env('COMMON_CHEMISTRY_URL', self::DEFAULT_BASE_URL);

        if (! $token) {
            Log::error('CAS API token not configured');

            return response()->json([
                'error' => 'CAS API token not configured',
            ], 500);
        }

        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders([
                    'X-API-KEY' => $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$baseUrl}/detail", [
                    'cas_rn' => $casNumber,
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            // Handle specific error cases
            if ($response->status() === 404) {
                return response()->json([
                    'error' => "Details not found for CAS number {$casNumber}. Please enter a valid CAS Registry Number.",
                ], 404);
            }

            if ($response->status() === 401) {
                return response()->json([
                    'error' => 'API authentication failed - invalid or missing CAS API token',
                ], 401);
            }

            if ($response->status() === 403) {
                return response()->json([
                    'error' => 'Access forbidden - check CAS API token permissions',
                ], 403);
            }

            if ($response->status() === 429) {
                return response()->json([
                    'error' => 'Rate limit exceeded - please wait before making another request',
                ], 429);
            }

            return response()->json([
                'error' => 'CAS API server error - please try again later',
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('CAS API Exception', [
                'message' => $e->getMessage(),
                'cas_number' => $casNumber,
            ]);

            return response()->json([
                'error' => 'CAS API server error - please try again later',
            ], 500);
        }
    }
}
