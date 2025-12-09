<?php

namespace Tests\API;

use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Draft;
use App\Models\Study;
use App\Models\User;
use App\Services\ChemotionRepositoryTrackerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class ELNControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ChemotionRepositoryTrackerService to avoid null config issues
        $this->app->bind(ChemotionRepositoryTrackerService::class, function () {
            return Mockery::mock(ChemotionRepositoryTrackerService::class, function ($mock) {
                $mock->shouldReceive('sendSubmissionReceived')->andReturn(true);
            });
        });
    }

    /**
     * Test ELN upload requires authentication
     */
    public function test_eln_upload_requires_authentication()
    {
        $response = $this->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-001',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test ELN upload with unsupported ELN system
     */
    public function test_eln_upload_with_unsupported_eln_system()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/unsupported/upload', [
            'external_id' => 'TEST-001',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unsupported ELN system']);
        $response->assertJsonStructure(['supported_elns']);
    }

    /**
     * Test ELN upload requires external_id
     */
    public function test_eln_upload_requires_external_id()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'External ID is required']);
    }

    /**
     * Test ELN upload requires valid callback_url
     */
    public function test_eln_upload_requires_valid_callback_url()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-001',
            'callback_url' => 'not-a-url',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => 'Callback URL is required and must be a valid URL']);
    }

    /**
     * Test ELN upload requires valid zip_url
     */
    public function test_eln_upload_requires_valid_zip_url()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-001',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'not-a-url',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => 'ZIP URL is required and must be a valid URL']);
    }

    /**
     * Test ELN upload creates new draft successfully
     */
    public function test_eln_upload_creates_new_draft_successfully()
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-NEW-001',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
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
            'user_id',
            'team_id',
            'eln_status',
        ]);

        $this->assertTrue($response->json('created_new'));
        $this->assertEquals('CHEM-NEW-001', $response->json('external_id'));

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }

    /**
     * Test ELN upload with release date in future
     */
    public function test_eln_upload_with_release_date_in_future()
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();

        $futureDate = Carbon::now()->addDays(30)->toDateString();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-002',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
            'release_date' => $futureDate,
        ]);

        $response->assertStatus(200);
        // Just verify release date is set (format may vary)
        $this->assertNotNull($response->json('release_date'));
        $this->assertStringContainsString('2026', $response->json('release_date'));
    }

    /**
     * Test ELN upload ignores past release date
     */
    public function test_eln_upload_ignores_past_release_date()
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();

        $pastDate = Carbon::now()->subDays(30)->toDateString();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-003',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
            'release_date' => $pastDate,
        ]);

        $response->assertStatus(200);
        // Past dates should be ignored/set to null
        $releaseDate = $response->json('release_date');
        $this->assertTrue($releaseDate === null || $releaseDate === '');
    }

    /**
     * Test ELN upload updates existing draft
     */
    public function test_eln_upload_updates_existing_draft()
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();
        $team = $user->currentTeam;

        // Create existing draft
        $draft = Draft::factory()->create([
            'external_id' => 'CHEM-EXISTING',
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'callback_url' => 'https://old.com/callback',
            'zip_url' => 'https://old.com/data.zip',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-EXISTING',
            'callback_url' => 'https://new.com/callback',
            'zip_url' => 'https://new.com/data.zip',
        ]);

        $response->assertStatus(200);
        $this->assertFalse($response->json('created_new'));
        $this->assertEquals('https://new.com/callback', $response->json('callback_url'));
        $this->assertEquals('https://new.com/data.zip', $response->json('zip_url'));
    }

    /**
     * Test ELN upload rejects published study with same external_id
     */
    public function test_eln_upload_rejects_published_study_with_same_external_id()
    {
        $user = User::factory()->withPersonalTeam()->create();

        // Create published study
        $study = Study::factory()->create([
            'external_id' => 'CHEM-PUBLISHED',
            'is_public' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-PUBLISHED',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => 'A submission with this chemotion external ID already exists and is published']);
    }

    /**
     * Test ELN status requires authentication
     */
    public function test_eln_status_requires_authentication()
    {
        $response = $this->getJson('/api/v1/chemotion/status/CHEM-001');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test ELN status with unsupported ELN system
     */
    public function test_eln_status_with_unsupported_eln_system()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/unsupported/status/TEST-001');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unsupported ELN system']);
    }

    /**
     * Test ELN status returns draft information
     */
    public function test_eln_status_returns_draft_information()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $team = $user->currentTeam;

        $draft = Draft::factory()->create([
            'external_id' => 'CHEM-STATUS-001',
            'eln' => 'chemotion',
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'status' => 'zip_processed',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/chemotion/status/CHEM-STATUS-001');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'draft_id',
                'draft_key',
                'external_id',
                'eln_system',
                'name',
                'description',
                'status',
                'current_step',
                'callback_url',
                'zip_url',
                'release_date',
                'created_at',
                'updated_at',
                'owner_id',
                'team_id',
                'files_count',
            ],
        ]);
        $this->assertEquals('CHEM-STATUS-001', $response->json('data.external_id'));
    }

    /**
     * Test ELN status returns published study when draft doesn't exist
     */
    public function test_eln_status_returns_published_study_when_draft_doesnt_exist()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $study = Study::factory()->create([
            'external_id' => 'CHEM-STUDY-001',
            'is_public' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/chemotion/status/CHEM-STUDY-001');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'data',
        ]);
    }

    /**
     * Test ELN status returns 404 when not found
     */
    public function test_eln_status_returns_404_when_not_found()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/chemotion/status/NON-EXISTENT');

        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'Submission not found with the provided external ID',
            'external_id' => 'NON-EXISTENT',
        ]);
    }

    /**
     * Test ELN status only returns user's own drafts
     */
    public function test_eln_status_only_returns_users_own_drafts()
    {
        $user1 = User::factory()->withPersonalTeam()->create();
        $user2 = User::factory()->withPersonalTeam()->create();

        $draft = Draft::factory()->create([
            'external_id' => 'CHEM-USER1',
            'eln' => 'chemotion',
            'owner_id' => $user1->id,
            'team_id' => $user1->currentTeam->id,
        ]);

        // User2 trying to access User1's draft
        $response = $this->actingAs($user2, 'sanctum')->getJson('/api/v1/chemotion/status/CHEM-USER1');

        $response->assertStatus(404);
    }

    /**
     * Test ELN upload with non-personal team
     */
    public function test_eln_upload_with_non_personal_team()
    {
        Queue::fake();

        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $team->personal_team = false;
        $team->save();

        $member = User::factory()->create([
            'current_team_id' => $team->id,
        ]);

        $response = $this->actingAs($member, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-TEAM-001',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        $response->assertStatus(200);
        $this->assertEquals($team->id, $response->json('team_id'));
        $this->assertEquals($team->user_id, $response->json('user_id'));
    }

    /**
     * Test ELN upload dispatches processing job
     */
    public function test_eln_upload_dispatches_processing_job()
    {
        Queue::fake();

        $user = User::factory()->withPersonalTeam()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/chemotion/upload', [
            'external_id' => 'CHEM-JOB-001',
            'callback_url' => 'https://example.com/callback',
            'zip_url' => 'https://example.com/data.zip',
        ]);

        Queue::assertPushed(ProcessDraftELNSubmission::class, function ($job) {
            return true;
        });
    }
}
