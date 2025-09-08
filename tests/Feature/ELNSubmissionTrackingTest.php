<?php

namespace Tests\Feature;

use App\Actions\Study\PublishStudy;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ELNSubmissionTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test configuration for tracker service
        config([
            'services.chemotion_tracker.base_url' => 'http://test-tracker.example.com',
            'services.chemotion_tracker.client_id' => 'test-client-id',
            'services.chemotion_tracker.username' => 'test-username',
            'services.chemotion_tracker.password' => 'test-password',
        ]);
    }

    private function createUserWithTeam(): array
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $team->users()->attach($user, ['role' => 'owner']);
        $user->current_team_id = $team->id;
        $user->save();

        return [$user, $team];
    }

    public function test_eln_submission_creates_tracking_when_received(): void
    {
        // Mock HTTP responses for tracker service
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::response([
                'id' => 1,
                'status' => 'received',
                'tracking_item_name' => 'CHEM-EXP-2024-001',
            ], 201),
        ]);

        // Create test user and team
        [$user, $team] = $this->createUserWithTeam();

        // Make API request to ELN endpoint
        $response = $this->actingAs($user)->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-EXP-2024-001',
            'callback_url' => 'https://chemotion.example.com/api/callback',
            'zip_url' => 'https://chemotion.example.com/exports/experiment-data.zip',
            'release_date' => '2026-12-31',
        ]);

        $response->assertStatus(200);

        // Verify tracking API was called
        Http::assertSent(function ($request) {
            return $request->url() === 'http://test-tracker.example.com/api/v1/trackings' &&
                   $request->method() === 'POST' &&
                   $request->data()['status'] === 'received' &&
                   $request->data()['tracking_item_name'] === 'CHEM-EXP-2024-001' &&
                   $request->data()['tracking_item_owner_name'] === 'John Doe' &&
                   $request->data()['tracking_item_owner_email'] === 'john@example.com' &&
                   $request->data()['from_trackable_system_name'] === 'chemotion_eln' &&
                   $request->data()['to_trackable_system_name'] === 'nmrxiv';
        });
    }

    public function test_study_publication_creates_tracking_when_published(): void
    {
        // Mock HTTP responses for tracker service
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
            ], 200),
            'http://test-tracker.example.com/api/v1/trackings' => Http::sequence()
                ->push([
                    [
                        'id' => 1,
                        'tracking_item_name' => 'CHEM-EXP-2024-002',
                        'tracking_item_owner_name' => 'Jane Smith',
                        'tracking_item_owner_email' => 'jane@example.com',
                        'metadata' => ['submission_type' => 'eln'],
                    ],
                ], 200)
                ->push([
                    'id' => 2,
                    'status' => 'published',
                    'tracking_item_name' => 'CHEM-EXP-2024-002',
                ], 201),
        ]);

        // Create test data
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $study = Study::factory()->create([
            'external_id' => 'CHEM-EXP-2024-002',
            'submitted_through' => 'chemotion',
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'is_public' => false,
        ]);

        // Create some datasets for the study
        Dataset::factory()->count(3)->create([
            'study_id' => $study->id,
            'is_public' => false,
        ]);

        // Publish the study
        $publishStudy = new PublishStudy;
        $publishStudy->publish($study);

        // Verify study and datasets are now public
        $this->assertTrue($study->fresh()->is_public);
        $this->assertTrue($study->datasets->every(fn ($dataset) => $dataset->is_public));

        // Verify tracking API was called for publication
        Http::assertSent(function ($request) {
            $data = $request->data();

            return $request->url() === 'http://test-tracker.example.com/api/v1/trackings' &&
                   $request->method() === 'POST' &&
                   isset($data['metadata']['published_at']) &&
                   isset($data['metadata']['study_id']) &&
                   isset($data['metadata']['datasets_count']);
        });
    }

    public function test_tracking_failure_does_not_break_submission(): void
    {
        // Mock HTTP failure for tracker service
        Http::fake([
            'http://test-tracker.example.com/oauth/token' => Http::response([
                'error' => 'service_unavailable',
            ], 503),
        ]);

        // Create test user and team
        [$user, $team] = $this->createUserWithTeam();

        // Make API request to ELN endpoint - should still succeed even if tracking fails
        $response = $this->actingAs($user)->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-EXP-2024-003',
            'callback_url' => 'https://chemotion.example.com/api/callback',
            'zip_url' => 'https://chemotion.example.com/exports/experiment-data.zip',
        ]);

        $response->assertStatus(200);

        // Verify draft was still created
        $this->assertDatabaseHas('drafts', [
            'external_id' => 'CHEM-EXP-2024-003',
        ]);
    }

    public function test_non_eln_study_publication_does_not_create_tracking(): void
    {
        Http::fake();

        // Create test data for non-ELN study
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $study = Study::factory()->create([
            'external_id' => null, // No external ID
            'submitted_through' => null, // Not from ELN
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'is_public' => false,
        ]);

        // Publish the study
        $publishStudy = new PublishStudy;
        $publishStudy->publish($study);

        // Verify study is now public
        $this->assertTrue($study->fresh()->is_public);

        // Verify no tracking API calls were made
        Http::assertNothingSent();
    }
}
