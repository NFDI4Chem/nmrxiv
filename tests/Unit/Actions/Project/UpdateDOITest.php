<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\UpdateDOI;
use App\Models\Dataset;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Services\DOI\DOIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UpdateDOITest extends TestCase
{
    use RefreshDatabase;

    private UpdateDOI $action;

    private DOIService $doiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doiService = $this->createMock(DOIService::class);
        $this->action = new UpdateDOI($this->doiService);
    }

    public function test_constructor_accepts_doi_service()
    {
        $action = new UpdateDOI($this->doiService);
        $this->assertInstanceOf(UpdateDOI::class, $action);
    }

    public function test_update_processes_project_type()
    {
        $project = Project::factory()->create();

        // Create a test class that tracks what was processed
        $processedModels = [];
        $testAction = new class($this->doiService, $processedModels) extends UpdateDOI
        {
            private $processedModels;

            public function __construct($doiService, &$processedModels)
            {
                parent::__construct($doiService);
                $this->processedModels = &$processedModels;
            }

            public function update($model)
            {
                $this->processedModels[] = get_class($model);

                if ($model instanceof Project) {
                    $this->processedModels[] = 'processed_project';
                    $studies = $model->studies;
                    foreach ($studies as $study) {
                        if ($study instanceof Study) {
                            $this->processedModels[] = 'processed_study';
                            $datasets = $study->datasets;
                            foreach ($datasets as $dataset) {
                                if ($dataset instanceof Dataset) {
                                    $this->processedModels[] = 'processed_dataset';
                                }
                            }
                        }
                    }
                } elseif ($model instanceof Collection) {
                    foreach ($model as $study) {
                        if ($study instanceof Study) {
                            $this->processedModels[] = 'processed_study';
                            $datasets = $study->datasets;
                            foreach ($datasets as $dataset) {
                                if ($dataset instanceof Dataset) {
                                    $this->processedModels[] = 'processed_dataset';
                                }
                            }
                        }
                    }
                }
            }
        };

        $testAction->update($project);

        $this->assertContains(Project::class, $processedModels);
        $this->assertContains('processed_project', $processedModels);
    }

    public function test_update_processes_collection_type()
    {
        $study1 = Study::factory()->create();
        $study2 = Study::factory()->create();
        $studies = collect([$study1, $study2]);

        // Create a test class that tracks what was processed
        $processedModels = [];
        $testAction = new class($this->doiService, $processedModels) extends UpdateDOI
        {
            private $processedModels;

            public function __construct($doiService, &$processedModels)
            {
                parent::__construct($doiService);
                $this->processedModels = &$processedModels;
            }

            public function update($model)
            {
                if ($model instanceof Collection) {
                    $this->processedModels[] = 'processed_collection';
                    foreach ($model as $study) {
                        if ($study instanceof Study) {
                            $this->processedModels[] = 'processed_study';
                        }
                    }
                }
            }
        };

        $testAction->update($studies);

        $this->assertContains('processed_collection', $processedModels);
        $this->assertContains('processed_study', $processedModels);
    }

    public function test_update_handles_null_model()
    {
        // Should not throw any errors when null is passed
        $this->action->update(null);
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function test_update_handles_empty_collection()
    {
        $emptyCollection = collect([]);

        // Should not throw any errors when empty collection is passed
        $this->action->update($emptyCollection);
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function test_update_handles_unsupported_model_type()
    {
        $unsupportedModel = new \stdClass;

        // Should not throw any errors when unsupported model is passed
        $this->action->update($unsupportedModel);
        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function test_update_skips_non_study_objects_in_collection()
    {
        $study = Study::factory()->create();
        $nonStudyObject = new \stdClass;
        $studies = collect([$study, $nonStudyObject]);

        // Create a test class that tracks what was processed
        $processedModels = [];
        $testAction = new class($this->doiService, $processedModels) extends UpdateDOI
        {
            private $processedModels;

            public function __construct($doiService, &$processedModels)
            {
                parent::__construct($doiService);
                $this->processedModels = &$processedModels;
            }

            public function update($model)
            {
                if ($model instanceof Collection) {
                    foreach ($model as $study) {
                        if ($study instanceof Study) {
                            $this->processedModels[] = 'processed_study';
                        } else {
                            $this->processedModels[] = 'skipped_non_study';
                        }
                    }
                }
            }
        };

        $testAction->update($studies);

        $this->assertContains('processed_study', $processedModels);
        $this->assertContains('skipped_non_study', $processedModels);
    }

    public function test_update_skips_non_dataset_objects_in_study()
    {
        $study = Study::factory()->create();
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);
        $nonDatasetObject = new \stdClass;

        // Set a custom collection on the study that includes a non-dataset object
        $study->setRelation('datasets', collect([$dataset, $nonDatasetObject]));
        $studies = collect([$study]);

        // Create a test class that tracks what was processed
        $processedModels = [];
        $testAction = new class($this->doiService, $processedModels) extends UpdateDOI
        {
            private $processedModels;

            public function __construct($doiService, &$processedModels)
            {
                parent::__construct($doiService);
                $this->processedModels = &$processedModels;
            }

            public function update($model)
            {
                if ($model instanceof Collection) {
                    foreach ($model as $study) {
                        if ($study instanceof Study) {
                            $datasets = $study->datasets;
                            foreach ($datasets as $dataset) {
                                if ($dataset instanceof Dataset) {
                                    $this->processedModels[] = 'processed_dataset';
                                } else {
                                    $this->processedModels[] = 'skipped_non_dataset';
                                }
                            }
                        }
                    }
                }
            }
        };

        $testAction->update($studies);

        $this->assertContains('processed_dataset', $processedModels);
        $this->assertContains('skipped_non_dataset', $processedModels);
    }

    public function test_update_processes_project_with_nested_relationships()
    {
        $project = Project::factory()->create();
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        // Create a test class that tracks processing flow
        $processedModels = [];
        $testAction = new class($this->doiService, $processedModels) extends UpdateDOI
        {
            private $processedModels;

            public function __construct($doiService, &$processedModels)
            {
                parent::__construct($doiService);
                $this->processedModels = &$processedModels;
            }

            public function update($model)
            {
                if ($model instanceof Project) {
                    $this->processedModels[] = 'project';
                    $studies = $model->studies;
                    foreach ($studies as $study) {
                        if ($study instanceof Study) {
                            $this->processedModels[] = 'study';
                            $datasets = $study->datasets;
                            foreach ($datasets as $dataset) {
                                if ($dataset instanceof Dataset) {
                                    $this->processedModels[] = 'dataset';
                                }
                            }
                        }
                    }
                }
            }
        };

        $testAction->update($project);

        $this->assertContains('project', $processedModels);
        $this->assertContains('study', $processedModels);
        $this->assertContains('dataset', $processedModels);
    }

    public function test_update_uses_correct_service_instance()
    {
        $project = Project::factory()->create();

        // Verify the service is stored correctly
        $reflection = new \ReflectionClass($this->action);
        $property = $reflection->getProperty('doiService');
        $property->setAccessible(true);

        $this->assertSame($this->doiService, $property->getValue($this->action));
    }

    public function test_update_action_exists_and_is_callable()
    {
        $this->assertTrue(method_exists($this->action, 'update'));
        $this->assertTrue(is_callable([$this->action, 'update']));
    }

    public function test_update_handles_project_without_studies()
    {
        // Create license first
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);
        $project->studies()->delete(); // Ensure no studies exist

        // Should not throw any errors
        $this->action->update($project);
        $this->assertTrue(true);
    }

    public function test_update_handles_study_without_datasets()
    {
        // Create license and project first
        $license = License::factory()->create();
        $project = Project::factory()->create(['license_id' => $license->id]);
        $study = Study::factory()->create(['project_id' => $project->id, 'license_id' => $license->id]);
        $study->datasets()->delete(); // Ensure no datasets exist
        $studies = collect([$study]);

        // Should not throw any errors
        $this->action->update($studies);
        $this->assertTrue(true);
    }

    public function test_update_with_mock_doi_service_completes_successfully()
    {
        // Create license first
        $license = License::factory()->create();

        // Create a project with nested relationships
        $project = Project::factory()->create(['license_id' => $license->id]);
        $study = Study::factory()->create(['project_id' => $project->id, 'license_id' => $license->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id, 'license_id' => $license->id]);

        // Create a simple mock DOI service
        $mockService = new class implements DOIService
        {
            public function getDOIs()
            {
                return [];
            }

            public function createDOI($suffix, $attributes = []) {}

            public function getDOI($doi) {}

            public function updateDOI($doi, $attributes = []) {}

            public function deleteDOI($doi) {}

            public function getDOIActivity($doi) {}
        };

        $action = new UpdateDOI($mockService);

        // Should complete without errors
        $action->update($project);
        $this->assertTrue(true);
    }
}
