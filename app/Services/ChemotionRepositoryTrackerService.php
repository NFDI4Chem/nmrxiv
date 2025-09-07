<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChemotionRepositoryTrackerService
{
    private string $baseUrl;

    private string $clientId;

    private string $username;

    private string $password;

    private ?string $accessToken = null;

    public function __construct()
    {
        $this->baseUrl = config('services.chemotion_tracker.base_url');
        $this->clientId = config('services.chemotion_tracker.client_id');
        $this->username = config('services.chemotion_tracker.username');
        $this->password = config('services.chemotion_tracker.password');
    }

    /**
     * Check if Chemotion tracking is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) config('services.chemotion_tracker.enabled', false);
    }

    /**
     * Authenticate with the Repository-Tracker API using OAuth2 password flow
     */
    private function authenticate(): bool
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => '', // Must be empty according to API docs
                'username' => $this->username,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                Log::info('Chemotion Repository-Tracker authentication successful', [
                    'access_token' => $this->accessToken,
                ]);
                return true;
            }

            Log::error('Chemotion Repository-Tracker authentication failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Chemotion Repository-Tracker authentication error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get authenticated HTTP client
     */
    private function getAuthenticatedClient()
    {
        if (! $this->accessToken && ! $this->authenticate()) {
            throw new Exception('Failed to authenticate with Chemotion Repository-Tracker');
        }

        return Http::withToken($this->accessToken)
            ->acceptJson()
            ->contentType('application/json');
    }

    /**
     * Create a new tracking record
     */
    public function createTracking(array $trackingData): ?array
    {
        try {
            $client = $this->getAuthenticatedClient();

            $response = $client->post("{$this->baseUrl}/api/v1/trackings", $trackingData);

            if ($response->successful()) {
                Log::info('Chemotion tracking created successfully', [
                    'tracking_item_name' => $trackingData['tracking_item_name'],
                    'status' => $trackingData['status'],
                ]);

                return $response->json();
            }

            // Handle specific error cases
            $errorMessage = 'Failed to create Chemotion tracking';
            if ($response->status() === 401) {
                $responseBody = $response->json();
                if (isset($responseBody['error']) && str_contains($responseBody['error'], 'Trackable System Admin')) {
                    $errorMessage = 'Authorization failed: User does not have admin permissions for the trackable system';
                }
            }

            Log::error($errorMessage, [
                'status' => $response->status(),
                'body' => $response->body(),
                'tracking_data' => $trackingData,
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Chemotion tracking creation error', [
                'error' => $e->getMessage(),
                'tracking_data' => $trackingData,
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Get all trackings
     */
    public function getTrackings(): ?array
    {
        try {
            $client = $this->getAuthenticatedClient();

            $response = $client->get("{$this->baseUrl}/api/v1/trackings");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get Chemotion trackings', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Chemotion trackings retrieval error', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get a specific tracking by ID
     */
    public function getTracking(int $id): ?array
    {
        try {
            $client = $this->getAuthenticatedClient();

            $response = $client->get("{$this->baseUrl}/api/v1/trackings/{$id}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get Chemotion tracking', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Chemotion tracking retrieval error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get all tracking items
     */
    public function getTrackingItems(): ?array
    {
        try {
            $client = $this->getAuthenticatedClient();

            $response = $client->get("{$this->baseUrl}/api/v1/tracking_items");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get Chemotion tracking items', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Chemotion tracking items retrieval error', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get a specific tracking item by name
     */
    public function getTrackingItem(string $name): ?array
    {
        try {
            $client = $this->getAuthenticatedClient();

            $response = $client->get("{$this->baseUrl}/api/v1/tracking_items/{$name}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get Chemotion tracking item', [
                'name' => $name,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Chemotion tracking item retrieval error', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a tracking for ELN submission
     */
    public function createElnSubmissionTracking(
        string $submissionId,
        string $status,
        array $metadata,
        string $ownerName,
        string $ownerEmail,
        string $fromSystem = 'nmrxiv',
        string $toSystem = 'nmrxiv'
    ): ?array {
        $trackingData = [
            'status' => $status,
            'metadata' => $metadata,
            'tracking_item_name' => $submissionId,
            'tracking_item_owner_name' => $ownerName,
            'tracking_item_owner_email' => $ownerEmail,
            'from_trackable_system_name' => $fromSystem,
            'to_trackable_system_name' => $toSystem,
        ];

        return $this->createTracking($trackingData);
    }

    /**
     * Update ELN submission status
     */
    public function updateElnSubmissionStatus(
        string $submissionId,
        string $newStatus,
        array $additionalMetadata = [],
        string $ownerName = '',
        string $ownerEmail = ''
    ): ?array {
        // Get existing tracking to preserve owner information if not provided
        $existingTracking = null;
        if (empty($ownerName) || empty($ownerEmail)) {
            $trackings = $this->getTrackings();
            if ($trackings) {
                foreach ($trackings as $tracking) {
                    if ($tracking['tracking_item_name'] === $submissionId) {
                        $existingTracking = $tracking;
                        break;
                    }
                }
            }
        }

        $ownerName = $ownerName ?: ($existingTracking['tracking_item_owner_name'] ?? '');
        $ownerEmail = $ownerEmail ?: ($existingTracking['tracking_item_owner_email'] ?? '');

        $metadata = array_merge(
            $existingTracking['metadata'] ?? [],
            $additionalMetadata,
            ['status_updated_at' => now()->toISOString()]
        );

        return $this->createElnSubmissionTracking(
            $submissionId,
            $newStatus,
            $metadata,
            $ownerName,
            $ownerEmail
        );
    }
}
