<?php

namespace Tests\Unit\Jobs;

use App\Actions\Draft\DraftProcessingLogger;
use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Draft;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Study;
use App\Models\Validation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use ReflectionMethod;
use Tests\TestCase;

class ProcessDraftELNSubmissionJobTest extends TestCase
{
    use RefreshDatabase;

    private Draft $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'zip_url' => 'https://example.com/test.zip',
            'status' => 'PENDING',
        ]);
    }

    public function test_it_can_be_dispatched(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id);

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }

    public function test_it_implements_should_queue_interface(): void
    {
        $job = new ProcessDraftELNSubmission($this->draft->id);

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_job_has_correct_queue_traits(): void
    {
        $job = new ProcessDraftELNSubmission($this->draft->id);

        $traits = class_uses($job);

        $this->assertContains('Illuminate\Foundation\Queue\Queueable', $traits);
    }

    public function test_it_can_be_pushed_to_different_queues(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id)->onQueue('eln-submissions');

        Queue::assertPushedOn('eln-submissions', ProcessDraftELNSubmission::class);
    }

    public function test_it_can_be_delayed(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id)->delay(now()->addMinutes(5));

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }

    public function test_multiple_eln_jobs_for_different_drafts(): void
    {
        Queue::fake();

        $draft1 = Draft::factory()->create(['eln' => 'chemotion']);
        $draft2 = Draft::factory()->create(['eln' => 'chemotion']);

        ProcessDraftELNSubmission::dispatch($draft1->id);
        ProcessDraftELNSubmission::dispatch($draft2->id);

        Queue::assertPushed(ProcessDraftELNSubmission::class, 2);
    }

    public function test_job_can_be_dispatched_with_specific_connection(): void
    {
        Queue::fake();

        ProcessDraftELNSubmission::dispatch($this->draft->id)->onConnection('redis');

        Queue::assertPushed(ProcessDraftELNSubmission::class);
    }

    public function test_attach_citations_to_study_with_valid_citations(): void
    {
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'processing_logs' => [],
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $citations = [
            ['doi' => '10.1234/test', 'title' => 'Test Citation', 'authors' => 'A Author'],
            ['doi' => '10.5678/test2', 'title' => 'Another Citation', 'authors' => 'B Author'],
        ];

        $logger = new DraftProcessingLogger;
        $job = new ProcessDraftELNSubmission($draft->id);

        $method = new ReflectionMethod($job, 'attachCitationsToStudy');
        $method->invoke($job, $study, $citations, $logger);

        $study->refresh();
        $this->assertEquals($citations, $study->citations);
        $study->load('linkedCitations');
        $this->assertCount(2, $study->linkedCitations);
        $this->assertDatabaseCount('citation_study', 2);
    }

    public function test_attach_citations_to_study_with_empty_citations(): void
    {
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'processing_logs' => [],
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'citations' => null,
        ]);

        $logger = new DraftProcessingLogger;
        $job = new ProcessDraftELNSubmission($draft->id);

        $method = new ReflectionMethod($job, 'attachCitationsToStudy');
        $method->invoke($job, $study, [], $logger);

        $study->refresh();
        $this->assertNull($study->citations);

        $draft->refresh();
        $logs = $draft->processing_logs;
        $lastLog = end($logs);
        $this->assertStringContainsString('No citations found for study', $lastLog['message']);
    }

    public function test_attach_citations_to_study_logs_error_on_exception(): void
    {
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $draft = Draft::factory()->create([
            'eln' => 'chemotion',
            'processing_logs' => [],
        ]);

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $logger = $this->createMock(DraftProcessingLogger::class);
        $logger->expects($this->once())
            ->method('log')
            ->with(
                $this->anything(),
                'error',
                $this->stringContains('Failed to attach citations to study')
            );

        $studyMock = $this->createPartialMock(Study::class, ['update']);
        $studyMock->setRawAttributes($study->getAttributes());
        $studyMock->exists = true;
        $studyMock->setRelation('project', $project);
        $studyMock->method('update')->willThrowException(new \Exception('DB error'));

        $job = new ProcessDraftELNSubmission($draft->id);

        $method = new ReflectionMethod($job, 'attachCitationsToStudy');
        $method->invoke($job, $studyMock, [['doi' => '10.1234/test']], $logger);
    }

    public function test_create_or_find_molecule_does_not_abort_outer_transaction_on_duplicate_standard_inchi(): void
    {
        $uniqueStandardInchi = 'InChI=1S/ROLLBACK_TEST/c1/h1H2';

        $existing = Molecule::factory()->create([
            'standard_inchi' => $uniqueStandardInchi,
            'inchi' => null,
            'smiles' => null,
        ]);

        $job = new ProcessDraftELNSubmission($this->draft->id);
        $logger = app(DraftProcessingLogger::class);

        $method = new ReflectionMethod(ProcessDraftELNSubmission::class, 'createOrFindMolecule');
        $method->setAccessible(true);

        DB::transaction(function () use ($method, $job, $logger, $existing, $uniqueStandardInchi) {
            $result = $method->invoke($job, [
                'standard_inchi' => $uniqueStandardInchi,
                'molecular_formula' => 'H2',
                'molecular_weight' => 2.016,
            ], $logger);

            $this->assertNotNull($result);
            $this->assertSame($existing->id, $result->id);

            $this->assertGreaterThan(0, Molecule::query()->count());
        });
    }
}
