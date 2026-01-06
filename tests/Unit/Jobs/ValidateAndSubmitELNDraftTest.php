<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessSubmission;
use App\Jobs\ValidateAndSubmitELNDraft;
use App\Models\Draft;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use App\Services\ChemotionRepositoryTrackerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class ValidateAndSubmitELNDraftTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    private Draft $draft;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'status' => 'pending',
            'release_date' => now()->addDays(7),
        ]);

        $this->draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'external_id' => 'ext-123',
            'processing_logs' => '{"log": "test"}',
            'project_enabled' => false,
            'status' => 'PROCESSING',
            'current_step' => '2',
        ]);

        $this->project->draft_id = $this->draft->id;
        $this->project->save();
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ValidateAndSubmitELNDraft(1);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        ValidateAndSubmitELNDraft::dispatch(1);

        Queue::assertPushed(ValidateAndSubmitELNDraft::class);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ValidateAndSubmitELNDraft(1);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Foundation\Queue\Queueable', $traits);
    }

    public function test_it_stores_draft_id_in_constructor(): void
    {
        $job = new ValidateAndSubmitELNDraft(123);

        $reflection = new \ReflectionClass($job);
        $property = $reflection->getProperty('draftId');
        $property->setAccessible(true);

        $this->assertEquals(123, $property->getValue($job));
    }

    public function test_handle_returns_early_when_draft_not_found(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Draft not found');
            });

        $job = new ValidateAndSubmitELNDraft(999);
        $job->handle();

        $this->assertTrue(true);
    }

    public function test_handle_returns_early_when_project_not_found(): void
    {
        $draftWithoutProject = Draft::factory()->create();

        Log::shouldReceive('info')->once()->withAnyArgs();
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) use ($draftWithoutProject) {
                return str_contains($message, 'No project found') && 
                       isset($context['draft_id']) && 
                       $context['draft_id'] === $draftWithoutProject->id;
            });

        $job = new ValidateAndSubmitELNDraft($draftWithoutProject->id);
        $job->handle();

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
