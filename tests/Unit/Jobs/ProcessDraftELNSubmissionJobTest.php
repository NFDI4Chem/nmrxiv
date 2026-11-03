<?php

namespace Tests\Unit\Jobs;

use App\Actions\Draft\DraftProcessingLogger;
use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
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

    public function test_attach_molecules_stores_the_draft_eln_as_sample_submitted_through(): void
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

        // CreateNewStudy / CreateDraftStudy create the sample before the ELN job runs.
        $sample = Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
            'submitted_through' => null,
        ]);

        $job = new ProcessDraftELNSubmission($draft->id);
        $method = new ReflectionMethod($job, 'attachMoleculesToStudy');

        $molecules = [['standard_inchi' => 'InChI=1S/SUBMITTED_THROUGH_TEST/c1/h1H2', 'molecular_formula' => 'H2']];

        $method->invoke($job, $study, $molecules, new DraftProcessingLogger);

        $this->assertSame('chemotion', $sample->fresh()->submitted_through);

        // A sample that still carries the legacy value is brought in line with the draft's ELN.
        $sample->update(['submitted_through' => 'ELN']);

        $method->invoke($job, $study->fresh(), $molecules, new DraftProcessingLogger);

        $this->assertSame('chemotion', $sample->fresh()->submitted_through);
    }

    public function test_attach_keywords_creates_typed_tags_that_publish_does_not_duplicate(): void
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

        $job = new ProcessDraftELNSubmission($draft->id);
        $method = new ReflectionMethod($job, 'attachKeywordsToStudy');

        $method->invoke($job, $study, ['keywords' => ['sample', 'chemical reaction']], new DraftProcessingLogger);

        $this->assertEqualsCanonicalizing(['sample', 'chemical reaction'], $study->fresh()->tags->pluck('name')->all());
        $this->assertSame(['Study'], $study->fresh()->tags->pluck('type')->unique()->values()->all());
        $this->assertSame(['Project'], $project->fresh()->tags->pluck('type')->unique()->values()->all());

        // What ProcessSubmission does when copying the project's tags onto the study.
        $study->syncTagsWithType($project->fresh()->tags->pluck('name')->toArray(), 'Study');

        $this->assertCount(2, $study->fresh()->tags);
    }

    private function makeStudyForLicense(): Study
    {
        $draft = Draft::factory()->create(['eln' => 'chemotion', 'processing_logs' => []]);
        $validation = Validation::factory()->create();

        $project = Project::factory()->create([
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => License::factory()->create()->id,
            'validation_id' => $validation->id,
        ]);

        return Study::factory()->create([
            'project_id' => $project->id,
            'draft_id' => $draft->id,
            'owner_id' => $draft->owner_id,
            'team_id' => $draft->team_id,
            'license_id' => $project->license_id,
            'validation_id' => $validation->id,
        ]);
    }

    private function attachLicense(Study $study, array $metadata): void
    {
        $job = new ProcessDraftELNSubmission($study->project->draft->id);
        $method = new ReflectionMethod($job, 'attachLicenseToStudy');
        $method->invoke($job, $study, $metadata, new DraftProcessingLogger);
    }

    public function test_attach_license_reads_the_chemotion_license_keys(): void
    {
        License::factory()->create(['spdx_id' => 'CC-BY-4.0', 'url' => 'https://creativecommons.org/licenses/by/4.0/legalcode']);
        $cc0 = License::factory()->create(['spdx_id' => 'CC0-1.0', 'url' => 'https://creativecommons.org/publicdomain/zero/1.0/legalcode']);

        $study = $this->makeStudyForLicense();
        $dataset = Dataset::factory()->create(['study_id' => $study->id, 'project_id' => $study->project_id]);

        $this->attachLicense($study, ['license' => [
            'schemeURI' => 'https://spdx.org/licenses/',
            'rightsIdentifierScheme' => 'SPDX',
            'rightsIdentifier' => 'CC0-1.0',
            'rightsURI' => 'http://creativecommons.org/publicdomain/zero/1.0/',
            'rightsName' => 'CC0 1.0 Universal',
        ]]);

        $this->assertSame($cc0->id, $study->fresh()->license_id);
        $this->assertSame($cc0->id, $study->project->fresh()->license_id);
        $this->assertSame($cc0->id, $dataset->fresh()->license_id);
    }

    public function test_attach_license_matches_chemotion_rights_uri_with_the_stored_legalcode_url(): void
    {
        License::factory()->create(['spdx_id' => 'CC-BY-4.0', 'url' => 'https://creativecommons.org/licenses/by/4.0/legalcode']);
        $bySa = License::factory()->create(['spdx_id' => 'CC-BY-SA-4.0', 'url' => 'https://creativecommons.org/licenses/by-sa/4.0/legalcode']);

        $study = $this->makeStudyForLicense();

        $this->attachLicense($study, ['license' => [
            'rightsIdentifier' => 'not-in-our-table',
            'rightsURI' => 'http://creativecommons.org/licenses/by-sa/4.0/',
        ]]);

        $this->assertSame($bySa->id, $study->fresh()->license_id);
    }

    public function test_attach_license_ignores_the_old_license_keys_and_uses_the_default(): void
    {
        $default = License::factory()->create(['spdx_id' => 'CC-BY-4.0']);
        License::factory()->create(['spdx_id' => 'CC-BY-NC-4.0']);

        $study = $this->makeStudyForLicense();

        $this->attachLicense($study, ['license' => ['spdx_id' => 'CC-BY-NC-4.0']]);

        $this->assertSame($default->id, $study->fresh()->license_id);
    }

    public function test_attach_license_uses_the_default_when_the_license_is_not_an_object(): void
    {
        $default = License::factory()->create(['spdx_id' => 'CC-BY-4.0']);

        $study = $this->makeStudyForLicense();

        $this->attachLicense($study, ['license' => 'https://creativecommons.org/publicdomain/zero/1.0/']);

        $this->assertSame($default->id, $study->fresh()->license_id);
    }

    public function test_attach_license_uses_the_default_when_the_request_has_no_license(): void
    {
        $default = License::factory()->create(['spdx_id' => 'CC-BY-4.0']);

        $study = $this->makeStudyForLicense();
        $dataset = Dataset::factory()->create(['study_id' => $study->id, 'owner_id' => $study->owner_id]);

        $this->attachLicense($study, []);

        $this->assertSame($default->id, $study->fresh()->license_id);
        $this->assertSame($default->id, $study->project->fresh()->license_id);
        $this->assertSame($default->id, $dataset->fresh()->license_id);
    }

    public function test_attach_license_uses_the_default_when_nothing_matches(): void
    {
        $default = License::factory()->create(['spdx_id' => 'CC-BY-4.0']);

        $study = $this->makeStudyForLicense();
        $study->update(['license_id' => null]);
        $study->project->update(['license_id' => null]);

        $this->attachLicense($study, ['license' => ['rightsIdentifier' => 'Unknown-1.0']]);

        $this->assertSame($default->id, $study->fresh()->license_id);
        $this->assertSame($default->id, $study->project->fresh()->license_id);
    }

    public function test_attach_license_default_can_be_configured(): void
    {
        License::factory()->create(['spdx_id' => 'CC-BY-4.0']);
        $configured = License::factory()->create(['spdx_id' => 'CC0-1.0']);
        config(['services.eln.default_license' => 'CC0-1.0']);

        $study = $this->makeStudyForLicense();

        $this->attachLicense($study, []);

        $this->assertSame($configured->id, $study->fresh()->license_id);
    }
}
