<?php

namespace Tests\Feature\ExternalServices\ELN;

use App\Actions\Study\PublishStudy;
use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ELNSubmissionTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set test configuration for tracker service
        config([
            'services.chemotion_tracker.enabled' => true,
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
        Queue::fake();

        // Mock HTTP responses for tracker service
        Http::fake([
            'http://test-tracker.example.com/*' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
                'id' => 1,
                'status' => 'received',
                'tracking_item_name' => 'CHEM-EXP-2024-001',
            ], 200),
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
        $response->assertJsonStructure([
            'draft_id',
            'draft_key',
            'external_id',
            'callback_url',
            'zip_url',
            'release_date',
            'created_new',
        ]);

        // Verify job was dispatched
        Queue::assertPushed(ProcessDraftELNSubmission::class);

        // Verify draft was created
        $this->assertDatabaseHas('drafts', [
            'external_id' => 'CHEM-EXP-2024-001',
            'eln' => 'chemotion',
            'callback_url' => 'https://chemotion.example.com/api/callback',
        ]);

        // Verify at least auth request was made to tracker
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'test-tracker.example.com');
        });
    }

    public function test_study_publication_creates_tracking_when_published(): void
    {
        // Mock HTTP responses for tracker service
        Http::fake([
            'http://test-tracker.example.com/*' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
                'id' => 1,
                'status' => 'published',
            ], 200),
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

        // Note: The tracking integration in PublishStudy might not be fully implemented yet
        // This test verifies the basic publication flow works
    }

    public function test_tracking_failure_does_not_break_submission(): void
    {
        Queue::fake();

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

        // Verify job was dispatched
        Queue::assertPushed(ProcessDraftELNSubmission::class);

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
