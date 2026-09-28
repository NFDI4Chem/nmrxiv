<?php

namespace Tests\Feature\Project;

use App\Actions\Project\ProjectProcessingLogger;
use App\Jobs\ProcessSubmission;
use App\Models\Draft;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProjectProcessingStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $stranger;

    private Project $project;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->stranger = User::factory()->withPersonalTeam()->create();

        $this->draft = Draft::factory()->create([
            'owner_id' => $this->owner->id,
            'project_enabled' => true,
        ]);

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->owner->personalTeam()->id,
            'is_public' => false,
            'status' => 'processing',
            'draft_id' => $this->draft->id,
            'process_logs' => [],
        ]);

        $this->project->users()->attach($this->owner, ['role' => 'creator']);
    }

    public function test_status_endpoint_returns_logs_and_stale_flag_for_owner(): void
    {
        $staleTimestamp = now()->subHours(2)->toIso8601String();

        $this->project->forceFill([
            'process_logs' => [
                [
                    'timestamp' => $staleTimestamp,
                    'level' => 'INFO',
                    'stage' => 'started',
                    'message' => 'Publish processing started.',
                    'context' => [],
                ],
            ],
            'updated_at' => now()->subHours(2),
        ])->save();

        config(['nmrxiv.publish.stale_after_minutes' => 30]);

        $response = $this->actingAs($this->owner)
            ->getJson(route('project.status', $this->project));

        $response->assertOk()
            ->assertJsonPath('status', 'processing')
            ->assertJsonPath('has_draft', true)
            ->assertJsonStructure([
                'status',
                'logs',
                'last_activity_at',
                'is_stale',
                'has_draft',
            ]);

        $this->assertNotEmpty($response->json('logs'));
        $this->assertSame('started', $response->json('logs.0.stage'));
        $this->assertTrue($response->json('is_stale'), 'Expected stale when last log is older than threshold. Payload: '.json_encode($response->json()));
    }

    public function test_status_endpoint_forbids_unrelated_users(): void
    {
        $response = $this->actingAs($this->stranger)
            ->getJson(route('project.status', $this->project));

        $response->assertForbidden();
    }

    public function test_retry_publish_returns_to_draft_when_draft_exists(): void
    {
        $this->project->update(['status' => 'failed']);

        $response = $this->actingAs($this->owner)
            ->postJson(route('project.publish.retry', $this->project));

        $response->assertOk()
            ->assertJsonPath('project.status', 'draft')
            ->assertJsonPath('redirect', route('publish', $this->draft));

        $this->assertSame('draft', $this->project->fresh()->status);

        $logs = app(ProjectProcessingLogger::class)->getLogs($this->project->fresh());
        $this->assertSame('retry_draft', $logs[array_key_last($logs)]['stage']);
    }

    public function test_retry_publish_requeues_when_draft_is_gone(): void
    {
        Queue::fake();

        $this->project->update([
            'status' => 'failed',
            'draft_id' => null,
        ]);
        $this->draft->delete();

        $response = $this->actingAs($this->owner)
            ->postJson(route('project.publish.retry', $this->project));

        $response->assertOk()
            ->assertJsonPath('project.status', 'queued');

        $this->assertSame('queued', $this->project->fresh()->status);
        Queue::assertPushed(ProcessSubmission::class, function (ProcessSubmission $job) {
            return $job->project->id === $this->project->id;
        });
    }

    public function test_retry_publish_rejects_non_failed_projects(): void
    {
        $this->project->update(['status' => 'processing']);

        $response = $this->actingAs($this->owner)
            ->from(route('dashboard'))
            ->post(route('project.publish.retry', $this->project));

        $response->assertSessionHasErrors('publish');
        $this->assertSame('processing', $this->project->fresh()->status);
    }

    public function test_retry_publish_forbids_unrelated_users(): void
    {
        $this->project->update(['status' => 'failed']);

        $response = $this->actingAs($this->stranger)
            ->post(route('project.publish.retry', $this->project));

        $response->assertForbidden();
    }
}
