<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\AssignIdentifier;
use App\Models\Project;
use App\Models\Study;
use App\Models\Ticker;
use App\Services\DOI\DOIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AssignIdentifierTest extends TestCase
{
    use RefreshDatabase;

    private AssignIdentifier $action;

    private DOIService $doiService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock DOI service that returns predictable responses
        $this->doiService = $this->createMock(DOIService::class);
        $this->doiService->method('createDOI')->willReturn([
            'data' => ['id' => '10.1234/test-doi'],
        ]);

        $this->action = new AssignIdentifier($this->doiService);

        // Create necessary tickers using the same approach as the seeder
        $types = ['project', 'study', 'dataset', 'sample', 'molecule'];
        foreach ($types as $type) {
            $ticker = new Ticker;
            $ticker->type = $type;
            $ticker->index = 0;
            $ticker->save();
        }
    }

    public function test_constructor_accepts_doi_service()
    {
        $action = new AssignIdentifier($this->doiService);
        $this->assertInstanceOf(AssignIdentifier::class, $action);
    }

    public function test_assign_creates_project_identifier_when_missing()
    {
        $license = \App\Models\License::factory()->create();
        $project = Project::factory()->create(['identifier' => null, 'license_id' => $license->id]);

        $this->action->assign($project);

        $project->refresh();
        $this->assertNotNull($project->identifier);
        // Identifier is formatted as "NMRXIV:P1", so check for the formatted version or raw value
        $this->assertTrue($project->identifier == 1 || $project->identifier == 'NMRXIV:P1');

        // Check that ticker was updated
        $ticker = Ticker::whereType('project')->first();
        $this->assertEquals(1, $ticker->index);
    }

    public function test_assign_does_not_overwrite_existing_project_identifier()
    {
        $license = \App\Models\License::factory()->create();
        $project = Project::factory()->create(['identifier' => 999, 'license_id' => $license->id]);

        $this->action->assign($project);

        $project->refresh();
        // The identifier is stored as integer in DB but accessed as formatted string
        $this->assertTrue($project->identifier == 999 || $project->identifier == 'NMRXIV:P999');

        // Check that ticker was not updated
        $ticker = Ticker::whereType('project')->first();
        $this->assertEquals(0, $ticker->index);
    }

    public function test_assign_processes_tickers_correctly()
    {
        $license = \App\Models\License::factory()->create();

        // Create first project
        $project1 = Project::factory()->create(['identifier' => null, 'license_id' => $license->id]);
        $this->action->assign($project1);

        // Create second project
        $project2 = Project::factory()->create(['identifier' => null, 'license_id' => $license->id]);
        $this->action->assign($project2);

        $project1->refresh();
        $project2->refresh();

        $this->assertTrue($project1->identifier == 1 || $project1->identifier == 'NMRXIV:P1');
        $this->assertTrue($project2->identifier == 2 || $project2->identifier == 'NMRXIV:P2');

        // Check ticker was incremented correctly
        $ticker = Ticker::whereType('project')->first();
        $this->assertEquals(2, $ticker->index);
    }

    public function test_assign_handles_null_model()
    {
        // Should not throw any errors when null is passed
        $this->action->assign(null);
        $this->assertTrue(true);
    }

    public function test_assign_handles_empty_collection()
    {
        $emptyCollection = collect([]);

        // Should not throw any errors when empty collection is passed
        $this->action->assign($emptyCollection);
        $this->assertTrue(true);
    }

    public function test_assign_handles_unsupported_model_type()
    {
        $unsupportedModel = new \stdClass;

        // Should not throw any errors when unsupported model is passed
        $this->action->assign($unsupportedModel);
        $this->assertTrue(true);
    }

    public function test_assign_skips_non_study_objects_in_collection()
    {
        $nonStudyObject = new \stdClass;
        $studies = collect([$nonStudyObject]);

        // Should not throw errors when processing non-study objects
        $this->action->assign($studies);

        // No studies were processed, ticker should remain at 0
        $ticker = Ticker::whereType('study')->first();
        $this->assertEquals(0, $ticker->index);
    }

    public function test_assign_uses_correct_service_instance()
    {
        // Verify the service is stored correctly
        $reflection = new \ReflectionClass($this->action);
        $property = $reflection->getProperty('doiService');
        $property->setAccessible(true);

        $this->assertSame($this->doiService, $property->getValue($this->action));
    }

    public function test_assign_action_exists_and_is_callable()
    {
        $this->assertTrue(method_exists($this->action, 'assign'));
        $this->assertTrue(is_callable([$this->action, 'assign']));
    }

    public function test_assign_handles_mixed_existing_and_new_identifiers()
    {
        $license = \App\Models\License::factory()->create();

        // Test with existing identifier
        $existingProject = Project::factory()->create(['identifier' => 100, 'license_id' => $license->id]);
        $this->action->assign($existingProject);

        $existingProject->refresh();
        $this->assertTrue($existingProject->identifier == 100 || $existingProject->identifier == 'NMRXIV:P100');

        // Project ticker should not be updated
        $this->assertEquals(0, Ticker::whereType('project')->first()->index);

        // Test with new identifier
        $newProject = Project::factory()->create(['identifier' => null, 'license_id' => $license->id]);
        $this->action->assign($newProject);

        $newProject->refresh();
        $this->assertTrue($newProject->identifier == 1 || $newProject->identifier == 'NMRXIV:P1');

        // Now ticker should be updated
        $this->assertEquals(1, Ticker::whereType('project')->first()->index);
    }
}
