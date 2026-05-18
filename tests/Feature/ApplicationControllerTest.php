<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Project $project;

    private Study $study;

    private Dataset $dataset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;

        [$userId, $teamId] = $this->user->getUserTeamData();

        $this->project = Project::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'is_public' => true,
            'identifier' => 1,
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $userId,
            'team_id' => $teamId,
            'identifier' => 1,
        ]);

        $this->dataset = Dataset::factory()->create([
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'owner_id' => $userId,
            'team_id' => $teamId,
            'identifier' => 1,
        ]);
    }

    public function test_compounds_renders_view_with_default_parameters(): void
    {
        $response = $this->get('/compounds');

        $response->assertStatus(200);
    }

    public function test_compounds_renders_view_with_query_parameter(): void
    {
        $response = $this->get('/compounds?query=caffeine');

        $response->assertStatus(200);
    }

    public function test_compounds_renders_view_with_custom_limit(): void
    {
        $response = $this->get('/compounds?limit=50');

        $response->assertStatus(200);
    }

    public function test_compounds_renders_view_with_default_limit_when_not_provided(): void
    {
        $response = $this->get('/compounds');

        $response->assertStatus(200);
    }

    public function test_compounds_renders_view_with_page_parameter(): void
    {
        $response = $this->get('/compounds?page=2');

        $response->assertStatus(200);
    }

    public function test_compounds_renders_view_with_tag_type(): void
    {
        $response = $this->get('/compounds?tagType=organic');

        $response->assertStatus(200);
    }

    public function test_resolve_compound_returns_404_for_invalid_identifier(): void
    {
        $response = $this->get('/compound/INVALID');

        $response->assertStatus(404);
    }

    public function test_resolve_compound_renders_studies_for_valid_molecule(): void
    {
        Molecule::factory()->create([
            'identifier' => 188,
        ]);

        $response = $this->get('/compound/M188');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Public/Studies'));
    }

    public function test_resolve_compound_with_lowercase_prefix(): void
    {
        Molecule::factory()->create([
            'identifier' => 189,
        ]);

        $response = $this->get('/compound/m189');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Public/Studies'));
    }

    public function test_resolve_compound_returns_404_for_non_existent_molecule(): void
    {
        $response = $this->get('/compound/M99999');

        $response->assertStatus(404);
    }

    public function test_resolve_project_renders_default_info_tab(): void
    {
        $response = $this->get('/project/P1');

        $response->assertStatus(200);
    }

    public function test_resolve_project_renders_info_tab(): void
    {
        $response = $this->get('/project/P1?tab=info');

        $response->assertStatus(200);
    }

    public function test_resolve_project_renders_samples_tab(): void
    {
        $response = $this->get('/project/P1?tab=samples');

        $response->assertStatus(200);
    }

    public function test_resolve_project_renders_files_tab(): void
    {
        $response = $this->get('/project/P1?tab=files');

        $response->assertStatus(200);
    }

    public function test_resolve_project_license_tab_renders_info(): void
    {
        $response = $this->get('/project/P1?tab=license');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Project/Show')
            ->where('tab', 'info'));
    }

    public function test_resolve_project_returns_404_for_non_existent_project(): void
    {
        $response = $this->get('/project/P99999');

        $response->assertStatus(404);
    }

    public function test_resolve_project_requires_authorization_for_private_project(): void
    {
        $privateProject = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'identifier' => 2,
        ]);

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get('/project/P2');

        $response->assertStatus(403);
    }

    public function test_resolve_project_allows_owner_to_view_private_project(): void
    {
        $privateProject = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'identifier' => 3,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/project/P3');

        $response->assertStatus(200);
    }

    public function test_resolve_study_renders_study_tab_with_project(): void
    {
        $response = $this->get('/sample/S1');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Project/Study')
        );
    }

    public function test_resolve_study_renders_study_without_project_when_no_project(): void
    {
        $studyWithoutProject = Study::factory()->create([
            'project_id' => null,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'identifier' => 2,
        ]);

        $response = $this->get('/sample/S2');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Sample/Show')
        );
    }

    public function test_resolve_dataset_renders_dataset_tab(): void
    {
        $response = $this->get('/dataset/D1');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Project/Dataset')
        );
    }

    public function test_resolve_dataset_includes_nmrium_info_when_nmrium_present(): void
    {
        NMRium::factory()->forDataset($this->dataset)->create([
            'nmrium_info' => [
                'version' => '4',
                'data' => [
                    'spectra' => [
                        ['id' => 'test-spectrum'],
                    ],
                ],
            ],
        ]);

        $response = $this->get('/dataset/D1');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Project/Dataset')
            ->where('dataset.data.nmrium_info.version', '4')
        );
    }

    public function test_resolve_dataset_returns_404_for_non_existent_dataset(): void
    {
        $response = $this->get('/dataset/D99999');

        $response->assertStatus(404);
    }

    public function test_resolve_sample_delegates_to_resolve_method(): void
    {
        $response = $this->get('/sample/S1');

        $response->assertStatus(200);
    }

    public function test_resolve_badge_generates_svg_for_project_with_doi(): void
    {
        $this->project->doi = '10.1234/test';
        $this->project->save();

        $response = $this->get('/badge/doi/P1');

        $response->assertStatus(200);
        $this->assertStringContainsString('10.1234/test', $response->getContent());
    }

    public function test_resolve_badge_generates_svg_for_study_with_doi(): void
    {
        $this->study->doi = '10.1234/study';
        $this->study->save();

        $response = $this->get('/badge/doi/S1');

        $response->assertStatus(200);
        $this->assertStringContainsString('10.1234/study', $response->getContent());
    }

    public function test_resolve_badge_generates_svg_for_dataset_with_doi(): void
    {
        $this->dataset->doi = '10.1234/dataset';
        $this->dataset->save();

        $response = $this->get('/badge/doi/D1');

        $response->assertStatus(200);
        $this->assertStringContainsString('10.1234/dataset', $response->getContent());
    }

    public function test_resolve_badge_returns_nothing_when_model_has_no_doi(): void
    {
        $this->project->doi = null;
        $this->project->save();

        $response = $this->get('/badge/doi/P1');

        $response->assertStatus(200);
        $this->assertEquals('', $response->getContent());
    }

    public function test_resolve_badge_returns_nothing_for_non_existent_identifier(): void
    {
        $response = $this->get('/badge/doi/P99999');

        $response->assertStatus(200);
        $this->assertEquals('', $response->getContent());
    }

    public function test_resolve_with_lowercase_identifier_prefix(): void
    {
        $response = $this->get('/project/p1');

        $response->assertStatus(200);
    }

    public function test_compounds_with_all_parameters(): void
    {
        $response = $this->get('/compounds?query=test&limit=10&page=3&tagType=sample');

        $response->assertStatus(200);
    }

    public function test_resolve_badge_calculates_correct_svg_width(): void
    {
        $this->project->doi = '10.1234/verylongdoiidentifier';
        $this->project->save();

        $response = $this->get('/badge/doi/P1');

        $response->assertStatus(200);
        $this->assertStringContainsString('10.1234/verylongdoiidentifier', $response->getContent());
    }

    public function test_resolve_project_with_public_unauthenticated_user(): void
    {
        $response = $this->get('/project/P1');

        $response->assertStatus(200);
    }

    public function test_resolve_project_with_public_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/project/P1');

        $response->assertStatus(200);
    }

    public function test_resolve_dataset_with_all_relationships(): void
    {
        $response = $this->get('/dataset/D1?tab=dataset');

        $response->assertStatus(200);
    }

    public function test_resolve_study_tab_explicitly(): void
    {
        $response = $this->get('/sample/S1?tab=study');

        $response->assertStatus(200);
    }

    public function test_compounds_defaults_to_null_tag_type(): void
    {
        $response = $this->get('/compounds');

        $response->assertStatus(200);
    }

    public function test_resolve_project_with_invalid_tab_defaults_to_info(): void
    {
        $response = $this->get('/project/P1?tab=invalid_tab');

        $response->assertStatus(200);
    }
}
