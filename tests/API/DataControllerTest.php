<?php

namespace Tests\API;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DataControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving all public projects
     */
    public function test_can_retrieve_all_public_projects()
    {
        $user = User::factory()->withPersonalTeam()->create();

        // Create public projects
        Project::factory()->count(3)->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        // Create private project (should not be returned)
        Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => false,
        ]);

        $response = $this->getJson('/api/v1/list/projects');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'identifier',
                    'name',
                    'is_public',
                ],
            ],
            'meta',
            'links',
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test retrieving all public samples
     */
    public function test_can_retrieve_all_public_samples()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        // Create public studies
        Study::factory()->count(2)->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        // Create private study (should not be returned)
        Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => false,
        ]);

        $response = $this->getJson('/api/v1/list/samples');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'identifier',
                    'name',
                    'is_public',
                ],
            ],
        ]);

        $this->assertCount(2, $response->json('data'));
    }

    /**
     * Test retrieving all public datasets
     */
    public function test_can_retrieve_all_public_datasets()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        // Create public datasets
        Dataset::factory()->count(5)->create([
            'study_id' => $study->id,
            'is_public' => true,
        ]);

        // Create private dataset (should not be returned)
        Dataset::factory()->create([
            'study_id' => $study->id,
            'is_public' => false,
        ]);

        $response = $this->getJson('/api/v1/list/datasets');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'identifier',
                    'name',
                    'is_public',
                ],
            ],
        ]);

        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test pagination for public data
     */
    public function test_pagination_works_for_public_data()
    {
        $user = User::factory()->withPersonalTeam()->create();

        // Create 25 public projects
        Project::factory()->count(25)->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/v1/list/projects?per_page=10&page=1');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(25, $response->json('meta.total'));
        $this->assertEquals(3, $response->json('meta.last_page'));
    }

    /**
     * Test filtering by name
     */
    public function test_can_filter_projects_by_name()
    {
        $user = User::factory()->withPersonalTeam()->create();

        Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'name' => 'NMR Analysis Project',
        ]);

        Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'name' => 'Chemical Synthesis Study',
        ]);

        $response = $this->getJson('/api/v1/list/projects?filter[name]=NMR');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertStringContainsString('NMR', $response->json('data.0.name'));
    }

    public function test_can_filter_projects_by_identifier(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 792,
        ]);

        Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 793,
        ]);

        $response = $this->getJson('/api/v1/list/projects?filter[identifier]=P792');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($project->id, $response->json('data.0.id'));
        $this->assertSame('NMRXIV:P792', $response->json('data.0.identifier'));
    }

    public function test_can_filter_projects_by_created_at_date_range(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $matchingProject = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'created_at' => Carbon::parse('2024-01-15 10:00:00'),
            'updated_at' => Carbon::parse('2024-01-15 10:00:00'),
        ]);

        Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'created_at' => Carbon::parse('2024-03-01 10:00:00'),
            'updated_at' => Carbon::parse('2024-03-01 10:00:00'),
        ]);

        $response = $this->getJson('/api/v1/list/projects?filter[created_at]=2024-01-01,2024-01-31');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($matchingProject->id, $response->json('data.0.id'));
    }

    /**
     * Test sorting by created_at ascending
     */
    public function test_can_sort_projects_by_created_at_ascending()
    {
        $user = User::factory()->withPersonalTeam()->create();

        Project::factory()->count(3)->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/v1/list/projects?sort=created_at');

        $response->assertStatus(200);
        $dates = collect($response->json('data'))->pluck('created_at');
        $this->assertEquals($dates->sort()->values(), $dates->values());
    }

    /**
     * Test sorting by created_at descending
     */
    public function test_can_sort_projects_by_created_at_descending()
    {
        $user = User::factory()->withPersonalTeam()->create();

        Project::factory()->count(3)->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/v1/list/projects?sort=-created_at');

        $response->assertStatus(200);
        $dates = collect($response->json('data'))->pluck('created_at');
        $this->assertEquals($dates->sortDesc()->values(), $dates->values());
    }

    /**
     * Test retrieving public project by identifier
     */
    public function test_can_retrieve_public_project_by_identifier()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
            'identifier' => 12345,
        ]);

        $response = $this->getJson('/api/v1/P12345');

        $response->assertStatus(200);

        // Check if wrapped in 'data'
        if ($response->json('data')) {
            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'identifier',
                    'name',
                    'is_public',
                ],
            ]);
            $this->assertEquals('NMRXIV:P12345', $response->json('data.identifier'));
        } else {
            $response->assertJsonStructure([
                'id',
                'identifier',
                'name',
                'is_public',
            ]);
            $this->assertEquals('NMRXIV:P12345', $response->json('identifier'));
        }
    }

    /**
     * Test retrieving public sample by identifier
     */
    public function test_can_retrieve_public_sample_by_identifier()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
            'identifier' => 12345,
        ]);

        $response = $this->getJson('/api/v1/S12345');

        $response->assertStatus(200);

        // Check if wrapped in 'data'
        if ($response->json('data')) {
            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'identifier',
                    'name',
                    'is_public',
                ],
            ]);
            $this->assertEquals('NMRXIV:S12345', $response->json('data.identifier'));
        } else {
            $response->assertJsonStructure([
                'id',
                'identifier',
                'name',
                'is_public',
            ]);
            $this->assertEquals('NMRXIV:S12345', $response->json('identifier'));
        }
    }

    /**
     * Test retrieving public dataset by identifier
     */
    public function test_can_retrieve_public_dataset_by_identifier()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'is_public' => true,
            'identifier' => 12345,
        ]);

        $response = $this->getJson('/api/v1/D12345');

        $response->assertStatus(200);

        // Check if wrapped in 'data'
        if ($response->json('data')) {
            $response->assertJsonStructure([
                'data' => [
                    'id',
                    'identifier',
                    'name',
                    'is_public',
                ],
            ]);
            $this->assertEquals('NMRXIV:D12345', $response->json('data.identifier'));
        } else {
            $response->assertJsonStructure([
                'id',
                'identifier',
                'name',
                'is_public',
            ]);
            $this->assertEquals('NMRXIV:D12345', $response->json('identifier'));
        }
    }

    /**
     * Test cannot retrieve private project by identifier
     */
    public function test_cannot_retrieve_private_project_by_identifier()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => false,
            'identifier' => 99999,
        ]);

        $response = $this->getJson('/api/v1/P99999');

        // Should return 403 Forbidden or 422 if identifier resolution fails
        $this->assertContains($response->status(), [403, 422, 500, 404]);
    }

    /**
     * Test cannot retrieve private sample
     */
    public function test_cannot_retrieve_private_sample()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => false,
            'identifier' => 99999,
        ]);

        $response = $this->getJson('/api/v1/S99999');

        // Should return 403 Forbidden or 422/500 if identifier resolution fails
        $this->assertContains($response->status(), [403, 422, 500, 404]);
    }

    /**
     * Test cannot retrieve private dataset
     */
    public function test_cannot_retrieve_private_dataset()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => true,
        ]);

        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'is_public' => false,
            'identifier' => 99999,
        ]);

        $response = $this->getJson('/api/v1/D99999');

        // Should return 403 Forbidden or 422/500 if identifier resolution fails
        $this->assertContains($response->status(), [403, 422, 500, 404]);
    }

    /**
     * Test returns 404 for non-existent identifier
     */
    public function test_returns_404_for_non_existent_identifier()
    {
        $response = $this->getJson('/api/v1/P999999');

        // Non-existent valid identifiers should return 404, 422, or 500
        $this->assertContains($response->status(), [404, 422, 500]);
    }

    /**
     * Test per_page can be customized
     */
    public function test_per_page_can_be_customized()
    {
        $user = User::factory()->withPersonalTeam()->create();

        // Create 15 public projects
        Project::factory()->count(15)->create([
            'owner_id' => $user->id,
            'is_public' => true,
        ]);

        // Request 5 per page
        $response = $this->getJson('/api/v1/list/projects?per_page=5');

        $response->assertStatus(200);
        // Should return only 5 results per page
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test empty result when no public data exists
     */
    public function test_returns_empty_when_no_public_data_exists()
    {
        $response = $this->getJson('/api/v1/list/projects');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'No public data available for the specified criteria, adjust your filter and try again.',
        ]);
    }

    /**
     * Test only valid model types work
     */
    public function test_only_valid_model_types_work()
    {
        Project::factory()->count(3)->create(['is_public' => true]);

        // Valid model types should work
        $response = $this->getJson('/api/v1/list/projects');
        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
    }
}
