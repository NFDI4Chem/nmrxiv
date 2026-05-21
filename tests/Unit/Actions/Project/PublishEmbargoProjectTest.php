<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\PublishEmbargoProject;
use App\Jobs\ProcessSubmission;
use App\Models\Draft;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PublishEmbargoProjectTest extends TestCase
{
    use RefreshDatabase;

    private PublishEmbargoProject $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new PublishEmbargoProject;
    }

    public function test_publish_rejects_project_that_is_already_public(): void
    {
        $project = Project::factory()->create([
            'is_public' => true,
            'status' => 'embargo',
            'doi' => '10.1234/public',
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['Project is already public.'], $exception->errors()['publish']);
        }
    }

    public function test_publish_rejects_non_embargo_project(): void
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'draft',
            'doi' => '10.1234/draft',
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['Project is not in embargo status.'], $exception->errors()['publish']);
        }
    }

    public function test_publish_rejects_archived_project(): void
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'is_archived' => true,
            'status' => 'embargo',
            'doi' => '10.1234/archived',
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['Archived projects cannot be published.'], $exception->errors()['publish']);
        }
    }

    public function test_publish_requires_doi(): void
    {
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'embargo',
            'doi' => null,
        ]);

        try {
            $this->action->publish($project);
            $this->fail('Expected project publication to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(['A DOI is required before publishing this project.'], $exception->errors()['publish']);
        }
    }

    public function test_publish_updates_embargo_project_and_queues_processing_when_draft_exists(): void
    {
        Queue::fake();
        $now = now();
        $draft = Draft::factory()->create();
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'embargo',
            'doi' => '10.1234/embargo',
            'release_date' => $now->copy()->subDays(2),
            'draft_id' => $draft->id,
        ]);

        $result = $this->action->publish($project);

        $project->refresh();
        $this->assertSame(['hasDraft' => true, 'dispatched' => 'async'], $result);
        $this->assertSame('queued', $project->status);
        $this->assertSame(now()->startOfDay()->toDateString(), $project->release_date->toDateString());
        Queue::assertPushed(ProcessSubmission::class, fn (ProcessSubmission $job) => $job->project->id === $project->id);
    }
}
