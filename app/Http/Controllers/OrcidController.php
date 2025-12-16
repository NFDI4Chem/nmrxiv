<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrcidController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (! $query) {
            return response()->json(['error' => 'Query parameter is required'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('orcid.base_url').'/search', [
                'q' => $query,
            ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch ORCID search results'], 500);
        }
    }

    public function person(string $orcidId): JsonResponse
    {
        if (! $orcidId) {
            return response()->json(['error' => 'ORCID ID is required'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('orcid.base_url').'/'.$orcidId.'/person');

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch person data'], 500);
        }
    }

    public function employment(string $orcidId): JsonResponse
    {
        if (! $orcidId) {
            return response()->json(['error' => 'ORCID ID is required'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('orcid.base_url').'/'.$orcidId.'/employments');

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch employment data'], 500);
        }
    }
}
