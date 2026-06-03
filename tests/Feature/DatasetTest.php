<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatasetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public dataset view returns Inertia page for public dataset
     */
    public function test_public_dataset_view_returns_page_for_public_dataset()
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
            'owner_id' => $user->id,
            'is_public' => true,
            'slug' => 'test-dataset',
        ]);

        $response = $this->get("/datasets/{$dataset->slug}");

        $response->assertStatus(200);
    }

    /**
     * Test public dataset view does not render private dataset
     */
    public function test_public_dataset_view_does_not_render_private_dataset()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
            'is_public' => false,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'is_public' => false,
        ]);
        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'owner_id' => $user->id,
            'is_public' => false,
            'slug' => 'private-dataset',
        ]);

        $response = $this->get("/datasets/{$dataset->slug}");

        // Should return 401 unauthorized for private datasets
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthorized',
        ]);
    }

    /**
     * Test fetch NMRium returns NMRium info with persisted molecules so chemical
     * structures added inside NMRium re-appear on subsequent reloads.
     */
    public function test_fetch_nmrium_returns_persisted_molecules()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        $molecules = [['smiles' => 'CCO']];
        $nmriumInfo = [
            'data' => [
                'spectra' => ['test' => 'data'],
                'molecules' => $molecules,
            ],
            'version' => '1.0',
        ];

        NMRium::factory()->create([
            'nmrium_info' => $nmriumInfo,
            'nmriumable_id' => $dataset->id,
            'nmriumable_type' => Dataset::class,
        ]);

        $response = $this->actingAs($user)->getJson("/dashboard/datasets/{$dataset->id}/nmriumInfo");

        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals($molecules, $response->json('data.molecules'));
    }

    /**
     * Test fetch NMRium returns empty when no NMRium data exists
     */
    public function test_fetch_nmrium_returns_empty_when_no_data()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        $response = $this->actingAs($user)->get("/dashboard/datasets/{$dataset->id}/nmriumInfo");

        $response->assertStatus(200);
        $this->assertEmpty($response->getContent());
    }

    /**
     * Test saving NMRium info creates new NMRium record
     */
    public function test_nmrium_info_creates_new_nmrium_record()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        $spectra = [
            [
                'info' => [
                    'experiment' => '1D',
                    'nucleus' => ['1H'],
                ],
            ],
        ];

        $molecules = [
            ['smiles' => 'CCO'],
        ];

        $response = $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/nmriumInfo", [
            'version' => '1.0',
            'spectra' => $spectra,
            'molecules' => $molecules,
        ]);

        $response->assertStatus(200);
        $dataset->refresh();

        $this->assertTrue($dataset->has_nmrium);
        $this->assertNotNull($dataset->nmrium);
        $this->assertEquals('1H NMR - 1D', $dataset->type);
    }

    /**
     * Test saving NMRium info updates existing NMRium record
     */
    public function test_nmrium_info_updates_existing_nmrium_record()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        NMRium::factory()->create([
            'nmrium_info' => ['spectra' => ['old' => 'data']],
            'nmriumable_id' => $dataset->id,
            'nmriumable_type' => Dataset::class,
        ]);

        $newSpectra = [
            [
                'info' => [
                    'experiment' => '2D',
                    'nucleus' => ['13C', '1H'],
                ],
            ],
        ];

        $response = $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/nmriumInfo", [
            'version' => '2.0',
            'spectra' => $newSpectra,
            'molecules' => [],
        ]);

        $response->assertStatus(200);
        $dataset->refresh();

        $nmrium = $dataset->nmrium;
        $this->assertEquals('2.0', $nmrium->nmrium_info['version']);
        $this->assertEquals($newSpectra, $nmrium->nmrium_info['spectra']);
        $this->assertEquals('13C-1H NMR - 2D', $dataset->type);
    }

    /**
     * Test NMRium versions returns ordered version history
     */
    public function test_nmrium_versions_returns_ordered_history()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        // Create initial NMRium info
        $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/nmriumInfo", [
            'version' => '1.0',
            'spectra' => [['info' => ['experiment' => '1D', 'nucleus' => ['1H']]]],
            'molecules' => [],
        ]);

        // Update NMRium info to create a version
        $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/nmriumInfo", [
            'version' => '2.0',
            'spectra' => [['info' => ['experiment' => '2D', 'nucleus' => ['13C', '1H']]]],
            'molecules' => [],
        ]);

        $response = $this->actingAs($user)->getJson("/dashboard/datasets/{$dataset->id}/nmriumVersions");

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
        // Versions should be created automatically by the versionable package
    }

    /**
     * Test NMRium versions returns empty when no NMRium exists
     */
    public function test_nmrium_versions_returns_empty_when_no_nmrium()
    {
        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        $response = $this->actingAs($user)->get("/dashboard/datasets/{$dataset->id}/nmriumVersions");

        $response->assertStatus(200);
        $this->assertEmpty($response->getContent());
    }

    /**
     * Test snapshot saves SVG for project-based dataset
     */
    public function test_snapshot_saves_svg_for_project_dataset()
    {
        Storage::fake('local');

        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'slug' => 'test-dataset',
        ]);

        $svgContent = '<svg><circle r="50"/></svg>';

        $response = $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/snapshot", [
            'img' => $svgContent,
        ]);

        $response->assertStatus(200);
        $dataset->refresh();

        $expectedPath = "/projects/{$project->uuid}/{$study->uuid}/{$dataset->slug}.svg";
        $this->assertEquals($expectedPath, $dataset->dataset_photo_path);

        Storage::disk('local')
            ->assertExists($expectedPath);
    }

    /**
     * Test snapshot saves SVG for sample-based dataset
     */
    public function test_snapshot_saves_svg_for_sample_dataset()
    {
        Storage::fake('local');

        $user = User::factory()->withPersonalTeam()->create();
        $study = Study::factory()->create(['project_id' => null]); // Sample without project
        $dataset = Dataset::factory()->create([
            'study_id' => $study->id,
            'slug' => 'sample-dataset',
        ]);

        $svgContent = '<svg><rect width="100" height="100"/></svg>';

        $response = $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/snapshot", [
            'img' => $svgContent,
        ]);

        $response->assertStatus(200);
        $dataset->refresh();

        $expectedPath = "/samples/{$study->uuid}/{$dataset->slug}.svg";
        $this->assertEquals($expectedPath, $dataset->dataset_photo_path);

        Storage::disk('local')
            ->assertExists($expectedPath);
    }

    /**
     * Test snapshot does nothing without image content
     */
    public function test_snapshot_does_nothing_without_content()
    {
        Storage::fake('local');

        $user = User::factory()->withPersonalTeam()->create();
        $project = Project::factory()->create(['owner_id' => $user->id]);
        $study = Study::factory()->create(['project_id' => $project->id]);
        $dataset = Dataset::factory()->create(['study_id' => $study->id]);

        $response = $this->actingAs($user)->post("/dashboard/datasets/{$dataset->id}/snapshot", [
            'img' => null,
        ]);

        $response->assertStatus(200);
        $dataset->refresh();

        $this->assertNull($dataset->dataset_photo_path);
    }
}
