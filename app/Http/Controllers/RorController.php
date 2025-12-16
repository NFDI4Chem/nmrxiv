<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RorController extends Controller
{
    /**
     * Search for organizations in the Research Organization Registry
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid search query',
                'messages' => $validator->errors(),
            ], 422);
        }

        $query = $request->input('query');

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get(config('ror.api_url', 'https://api.ror.org/organizations'), [
                    'query' => $query,
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::warning('ROR API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'Failed to fetch organizations',
                'items' => [],
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('ROR API exception', [
                'message' => $e->getMessage(),
                'query' => $query,
            ]);

            return response()->json([
                'error' => 'An error occurred while searching for organizations',
                'items' => [],
            ], 500);
        }
    }
}
