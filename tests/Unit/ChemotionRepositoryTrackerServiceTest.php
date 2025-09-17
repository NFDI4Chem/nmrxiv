<?php

namespace Tests\Unit;

use App\Services\ChemotionRepositoryTrackerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ChemotionRepositoryTrackerServiceTest extends TestCase
{
    private ChemotionRepositoryTrackerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test configuration
        config([
            'services.chemotion_tracker.enabled' => true,
            'services.chemotion_tracker.base_url' => 'http://test-tracker.example.com',
            'services.chemotion_tracker.client_id' => 'test-client-id',
            'services.chemotion_tracker.username' => 'test-username',
            'services.chemotion_tracker.password' => 'test-password',
        ]);

        $this->service = new ChemotionRepositoryTrackerService;
    }

    public function test_create_tracking_success(): void
    {
        // Mock authentication
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::response([
                'id' => 1,
                'status' => 'submitted',
                'tracking_item_name' => 'test-submission-123',
            ], 201),
        ]);

        $trackingData = [
            'status' => 'submitted',
            'metadata' => ['submission_type' => 'eln'],
            'tracking_item_name' => 'test-submission-123',
            'tracking_item_owner_name' => 'John Doe',
            'tracking_item_owner_email' => 'john@example.com',
            'from_trackable_system_name' => 'nmrxiv',
            'to_trackable_system_name' => 'nmrxiv',
        ];

        $result = $this->service->createTracking($trackingData);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('submitted', $result['status']);
        $this->assertEquals('test-submission-123', $result['tracking_item_name']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://test-tracker.example.com/oauth/token' &&
                   $request->method() === 'POST';
        });

        Http::assertSent(function ($request) use ($trackingData) {
            return $request->url() === 'http://test-tracker.example.com/api/v1/trackings' &&
                   $request->method() === 'POST' &&
                   $request->data() === $trackingData;
        });
    }

    public function test_create_tracking_authentication_failure(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'error' => 'invalid_credentials',
            ], 401),
        ]);

        Log::shouldReceive('error')->twice(); // Once for auth failure, once for tracking creation failure

        $trackingData = [
            'status' => 'submitted',
            'metadata' => ['submission_type' => 'eln'],
            'tracking_item_name' => 'test-submission-123',
            'tracking_item_owner_name' => 'John Doe',
            'tracking_item_owner_email' => 'john@example.com',
            'from_trackable_system_name' => 'nmrxiv',
            'to_trackable_system_name' => 'nmrxiv',
        ];

        $result = $this->service->createTracking($trackingData);

        $this->assertNull($result);
    }

    public function test_get_trackings_success(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::response([
                [
                    'id' => 1,
                    'status' => 'submitted',
                    'tracking_item_name' => 'test-submission-123',
                ],
                [
                    'id' => 2,
                    'status' => 'processing',
                    'tracking_item_name' => 'test-submission-456',
                ],
            ], 200),
        ]);

        $result = $this->service->getTrackings();

        $this->assertNotNull($result);
        $this->assertCount(2, $result);
        $this->assertEquals('submitted', $result[0]['status']);
        $this->assertEquals('processing', $result[1]['status']);
    }

    public function test_get_tracking_by_id_success(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings/1' => Http::response([
                'id' => 1,
                'status' => 'submitted',
                'tracking_item_name' => 'test-submission-123',
            ], 200),
        ]);

        $result = $this->service->getTracking(1);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('submitted', $result['status']);
    }

    public function test_get_tracking_items_success(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/tracking_items' => Http::response([
                [
                    'name' => 'test-submission-123',
                    'owner_name' => 'John Doe',
                    'owner_email' => 'john@example.com',
                ],
            ], 200),
        ]);

        $result = $this->service->getTrackingItems();

        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        $this->assertEquals('test-submission-123', $result[0]['name']);
    }

    public function test_get_tracking_item_by_name_success(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/tracking_items/test-submission-123' => Http::response([
                'name' => 'test-submission-123',
                'owner_name' => 'John Doe',
                'owner_email' => 'john@example.com',
            ], 200),
        ]);

        $result = $this->service->getTrackingItem('test-submission-123');

        $this->assertNotNull($result);
        $this->assertEquals('test-submission-123', $result['name']);
        $this->assertEquals('John Doe', $result['owner_name']);
    }

    public function test_create_eln_submission_tracking_success(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::response([
                'id' => 1,
                'status' => 'submitted',
                'tracking_item_name' => 'eln-submission-789',
            ], 201),
        ]);

        $result = $this->service->createElnSubmissionTracking(
            'eln-submission-789',
            ChemotionRepositoryTrackerService::STATUS_SUBMITTED,
            ['submission_type' => 'eln', 'dataset_count' => 5],
            'Jane Smith',
            'jane@example.com'
        );

        $this->assertNotNull($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('submitted', $result['status']);
        $this->assertEquals('eln-submission-789', $result['tracking_item_name']);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'http://test-tracker.example.com/api/v1/trackings' &&
                   $request->method() === 'POST' &&
                   $data['tracking_item_name'] === 'eln-submission-789' &&
                   $data['status'] === ChemotionRepositoryTrackerService::STATUS_SUBMITTED &&
                   $data['tracking_item_owner_name'] === 'Jane Smith' &&
                   $data['tracking_item_owner_email'] === 'jane@example.com' &&
                   $data['from_trackable_system_name'] === 'nmrxiv' &&
                   $data['to_trackable_system_name'] === 'nmrxiv';
        });
    }

    public function test_update_eln_submission_status_success(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::response([
                'id' => 2,
                'status' => 'processed',
                'tracking_item_name' => 'eln-submission-789',
            ], 201),
        ]);

        $result = $this->service->updateElnSubmissionStatus(
            'eln-submission-789',
            ChemotionRepositoryTrackerService::STATUS_PROCESSED,
            ['processing_started_at' => '2024-01-15T10:00:00Z'],
            'Jane Smith',
            'jane@example.com'
        );

        $this->assertNotNull($result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals('processed', $result['status']);
        $this->assertEquals('eln-submission-789', $result['tracking_item_name']);
    }

    public function test_api_error_handling(): void
    {
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::response([
                'error' => 'Bad Request',
            ], 400),
        ]);

        Log::shouldReceive('error')->twice(); // Once for API error, once for exception

        $trackingData = [
            'status' => ChemotionRepositoryTrackerService::STATUS_SUBMITTED,
            'metadata' => ['submission_type' => 'eln'],
            'tracking_item_name' => 'test-submission-123',
            'tracking_item_owner_name' => 'John Doe',
            'tracking_item_owner_email' => 'john@example.com',
            'from_trackable_system_name' => 'nmrxiv',
            'to_trackable_system_name' => 'nmrxiv',
        ];

        $result = $this->service->createTracking($trackingData);

        $this->assertNull($result);
    }

    public function test_is_enabled_returns_correct_value(): void
    {
        // Test when enabled
        config(['services.chemotion_tracker.enabled' => true]);
        $service = new ChemotionRepositoryTrackerService;
        $this->assertTrue($service->isEnabled());

        // Test when disabled
        config(['services.chemotion_tracker.enabled' => false]);
        $service = new ChemotionRepositoryTrackerService;
        $this->assertFalse($service->isEnabled());

        // Test default value (should be false)
        config(['services.chemotion_tracker.enabled' => null]);
        $service = new ChemotionRepositoryTrackerService;
        $this->assertFalse($service->isEnabled());
    }

    public function test_status_validation(): void
    {
        // Test valid statuses
        $this->assertTrue(ChemotionRepositoryTrackerService::isValidStatus(ChemotionRepositoryTrackerService::STATUS_DRAFT));
        $this->assertTrue(ChemotionRepositoryTrackerService::isValidStatus(ChemotionRepositoryTrackerService::STATUS_RECEIVED));
        $this->assertTrue(ChemotionRepositoryTrackerService::isValidStatus(ChemotionRepositoryTrackerService::STATUS_PUBLISHED));

        // Test invalid status
        $this->assertFalse(ChemotionRepositoryTrackerService::isValidStatus('invalid_status'));
        $this->assertFalse(ChemotionRepositoryTrackerService::isValidStatus(''));

        // Test get valid statuses returns array
        $validStatuses = ChemotionRepositoryTrackerService::getValidStatuses();
        $this->assertIsArray($validStatuses);
        $this->assertCount(12, $validStatuses);
        $this->assertContains(ChemotionRepositoryTrackerService::STATUS_RECEIVED, $validStatuses);
        $this->assertContains(ChemotionRepositoryTrackerService::STATUS_PUBLISHED, $validStatuses);
    }
}
