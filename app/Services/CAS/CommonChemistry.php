<?php

namespace App\Services\CAS;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CommonChemistry implements CASService
{
    private const REQUEST_TIMEOUT = 30;

    /**
     * Get the API configuration for CAS Common Chemistry service
     */
    private function getApiConfig(): array
    {
        return [
            'base_url' => Config::get('services.cas.base_url'),
            'api_token' => Config::get('services.cas.api_token'),
        ];
    }

    /**
     * Fetch detailed information for a given CAS registry number
     */
    public function getCASDetails(string $casNumber): array
    {
        try {
            $config = $this->getApiConfig();

            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders([
                    'X-API-KEY' => $config['api_token'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$config['base_url']}/detail", [
                    'cas_rn' => $casNumber,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Unable to fetch CAS details');
        } catch (\Exception $e) {
            throw new \Exception('Unable to fetch CAS details');
        }
    }

    /**
     * Search for CAS registry number using SMILES molecular structure notation
     */
    public function searchCasBySmiles(string $smiles): ?string
    {
        try {
            $config = $this->getApiConfig();

            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withHeaders([
                    'X-API-KEY' => $config['api_token'],
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->get("{$config['base_url']}/search", [
                    'q' => $smiles,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['count']) && $data['count'] > 0 && isset($data['results'][0]['rn'])) {
                    return $data['results'][0]['rn'];
                }
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
