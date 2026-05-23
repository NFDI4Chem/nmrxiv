<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\PublishEmbargoProject;
use App\Exceptions\EmbargoPublicationFailed;
use App\Jobs\ProcessSubmission;
use App\Models\Citation;
use App\Models\Draft;
use App\Models\Project;
use App\Models\Validation;
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
        config(['validations.embargo_action_pass.project' => []]);
        config(['validations.embargo_action_pass.study' => []]);
        config(['validations.embargo_action_pass.dataset' => []]);

        $now = now();
        $draft = Draft::factory()->create();
        $validation = Validation::factory()->passed()->create();
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'embargo',
            'doi' => '10.1234/embargo',
            'release_date' => $now->copy()->subDays(2),
            'draft_id' => $draft->id,
            'validation_id' => $validation->id,
            'schema_version' => 'embargo_action_pass',
        ]);
        $citation = Citation::factory()->create(['doi' => '10.1234/citation']);
        $project->citations()->attach($citation->id);

        $result = $this->action->publish($project);

        $project->refresh();
        $this->assertSame(true, $result['hasDraft']);
        $this->assertSame('async', $result['dispatched']);
        $this->assertSame($validation->id, $result['validation']->id);
        $this->assertSame('queued', $project->status);
        $this->assertSame(now()->startOfDay()->toDateString(), $project->release_date->toDateString());
        Queue::assertPushed(ProcessSubmission::class, fn (ProcessSubmission $job) => $job->project->id === $project->id);
    }

    public function test_publish_rejects_embargo_project_when_validation_fails(): void
    {
        Queue::fake();
        config(['validations.embargo_action_fail.project' => [
            'citations' => 'required',
        ]]);
        config(['validations.embargo_action_fail.study' => []]);
        config(['validations.embargo_action_fail.dataset' => []]);

        $validation = Validation::factory()->passed()->create();
        $originalReleaseDate = now()->subDays(2);
        $project = Project::factory()->create([
            'is_public' => false,
            'status' => 'embargo',
            'doi' => '10.1234/embargo-fail',
            'release_date' => $originalReleaseDate,
            'validation_id' => $validation->id,
            'schema_version' => 'embargo_action_fail',
        ]);

        try {
            $this->action->publish($project, restoreReleaseDateOnValidationFailure: true);
            $this->fail('Expected project publication to fail validation.');
        } catch (EmbargoPublicationFailed $exception) {
            $this->assertSame('false|required', $exception->validation->report['project']['citations']);
        }

        $project->refresh();
        $this->assertSame('embargo', $project->status);
        $this->assertSame($originalReleaseDate->toDateString(), $project->release_date->toDateString());
        Queue::assertNothingPushed();
    }
}
