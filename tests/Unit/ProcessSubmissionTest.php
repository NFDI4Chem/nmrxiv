<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\Draft\DetachStudyFilesystemFromDraft;
use App\Actions\Project\AssignIdentifier;
use App\Actions\Project\ProjectProcessingLogger;
use App\Actions\Project\PublishProject;
use App\Actions\Project\UpdateDOI;
use App\Actions\Study\PublishStudy;
use App\Jobs\ProcessSubmission;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use Tests\TestCase;

class ProcessSubmissionTest extends TestCase
{
    public function test_handle_does_not_throw_when_project_has_no_draft(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->id = 1;
        $project->draft_id = null;
        $project->status = 'queued';
        $project->setRelation('draft', null);

        $project->shouldReceive('fresh')->twice()->andReturn($project);

        $studiesRelation = Mockery::mock(HasMany::class);
        $studiesRelation->shouldReceive('exists')->once()->andReturn(false);
        $project->shouldReceive('studies')->once()->andReturn($studiesRelation);

        $assigner = Mockery::mock(AssignIdentifier::class);
        $assigner->shouldReceive('reserveProjectIdentifier')->once()->with($project)->andReturn($project);
        $assigner->shouldNotReceive('assign');

        $updater = Mockery::mock(UpdateDOI::class);
        $updater->shouldNotReceive('update');

        $projectPublisher = Mockery::mock(PublishProject::class);
        $projectPublisher->shouldNotReceive('publish');

        $studyPublisher = Mockery::mock(PublishStudy::class);
        $studyPublisher->shouldNotReceive('publish');

        $detachStudyFilesystemFromDraft = Mockery::mock(DetachStudyFilesystemFromDraft::class);
        $detachStudyFilesystemFromDraft->shouldNotReceive('detach');

        $logger = Mockery::mock(ProjectProcessingLogger::class);
        $logger->shouldReceive('log')->once();

        $job = new ProcessSubmission($project);
        $job->handle($assigner, $updater, $projectPublisher, $studyPublisher, $detachStudyFilesystemFromDraft, $logger);

        $this->assertSame('queued', $project->status);
    }

    public function test_handle_returns_early_when_project_is_already_complete_without_draft(): void
    {
        $project = Mockery::mock(Project::class)->makePartial();
        $project->id = 2;
        $project->draft_id = null;
        $project->status = 'complete';
        $project->setRelation('draft', null);

        $project->shouldReceive('fresh')->twice()->andReturn($project);
        $project->shouldNotReceive('save');

        $assigner = Mockery::mock(AssignIdentifier::class);
        $assigner->shouldReceive('reserveProjectIdentifier')->once()->with($project)->andReturn($project);
        $updater = Mockery::mock(UpdateDOI::class);
        $projectPublisher = Mockery::mock(PublishProject::class);
        $studyPublisher = Mockery::mock(PublishStudy::class);

        $detachStudyFilesystemFromDraft = Mockery::mock(DetachStudyFilesystemFromDraft::class);
        $detachStudyFilesystemFromDraft->shouldNotReceive('detach');

        $logger = Mockery::mock(ProjectProcessingLogger::class);
        $logger->shouldNotReceive('log');

        $job = new ProcessSubmission($project);
        $job->handle($assigner, $updater, $projectPublisher, $studyPublisher, $detachStudyFilesystemFromDraft, $logger);

        $this->assertSame('complete', $project->status);
    }
}
