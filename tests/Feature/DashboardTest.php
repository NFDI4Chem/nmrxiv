<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_with_personal_team(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_personal_projects_for_personal_team(): void
    {
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
        ]);

        // Create project owned by another user (should not appear)
        $otherUser = User::factory()->withPersonalTeam()->create();
        Project::factory()->create([
            'owner_id' => $otherUser->id,
            'team_id' => $otherUser->currentTeam->id,
            'is_deleted' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_filters_deleted_projects(): void
    {
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_samples_without_project_for_personal_team(): void
    {
        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
        ]);

        Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_compound_library_lists_studies_inside_projects(): void
    {
        $project = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => $project->id,
            'is_deleted' => false,
            'name' => 'CompoundLibStudyInsideProjectAlpha',
        ]);

        Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?tab=samples');

        $response->assertStatus(200);
        $response->assertSee('CompoundLibStudyInsideProjectAlpha', false);
    }

    public function test_dashboard_compound_library_deduplicates_same_primary_molecule(): void
    {
        $dupKey = 'DEDUPEKEYABCDEFGHIJKLMNOPQRSTUVWSYZ012345678900';

        $molecule = Molecule::factory()->create([
            'standard_inchi_key' => $dupKey,
        ]);

        $studyOlder = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
            'name' => 'DedupeCompoundOlderStudyToken',
            'updated_at' => now()->subHours(2),
        ]);

        $studyNewer = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
            'name' => 'DedupeCompoundNewerStudyToken',
            'updated_at' => now(),
        ]);

        $sampleOlder = Sample::factory()->create([
            'study_id' => $studyOlder->id,
        ]);

        $sampleNewer = Sample::factory()->create([
            'study_id' => $studyNewer->id,
        ]);

        $molecule->samples()->attach($sampleOlder->id, ['percentage_composition' => '100']);
        $molecule->samples()->attach($sampleNewer->id, ['percentage_composition' => '100']);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?tab=samples');

        $response->assertStatus(200);
        $body = $response->getContent();
        $this->assertStringContainsString('DedupeCompoundNewerStudyToken', $body);
        $this->assertStringNotContainsString('DedupeCompoundOlderStudyToken', $body);
    }

    public function test_dashboard_compound_card_shows_workspace_sample_count_for_molecule(): void
    {
        $molecule = Molecule::factory()->create();

        $studyOlder = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
            'updated_at' => now()->subHour(),
        ]);

        $studyNewer = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
            'updated_at' => now(),
        ]);

        $sampleA = Sample::factory()->create([
            'study_id' => $studyOlder->id,
        ]);
        $sampleB = Sample::factory()->create([
            'study_id' => $studyNewer->id,
        ]);

        $molecule->samples()->attach($sampleA->id, ['percentage_composition' => '100']);
        $molecule->samples()->attach($sampleB->id, ['percentage_composition' => '100']);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?tab=samples');

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/"workspace_samples_count":\s*2\b/',
            $response->getContent()
        );
    }

    public function test_dashboard_molecule_payload_includes_workspace_experiment_type_counts(): void
    {
        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
        ]);

        $molecule = Molecule::factory()->create();

        $sample = Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => 'WorkspaceExpTypeTokenA',
            'is_deleted' => false,
        ]);

        Dataset::factory()->create([
            'study_id' => $study->id,
            'team_id' => $study->team_id,
            'owner_id' => $study->owner_id,
            'project_id' => $study->project_id,
            'type' => 'WorkspaceExpTypeTokenB',
            'is_deleted' => false,
        ]);

        $molecule->samples()->attach($sample->id, ['percentage_composition' => '100']);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?tab=samples');

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringContainsString('workspace_experiment_type_counts', $body);
        $this->assertStringContainsString('WorkspaceExpTypeTokenA', $body);
        $this->assertStringContainsString('WorkspaceExpTypeTokenB', $body);
    }

    public function test_dashboard_defaults_samples_per_page_to_twelve(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard?tab=samples');

        $response->assertStatus(200);
        $this->assertMatchesRegularExpression(
            '/["\']samples_per_page["\']\s*:\s*12\b/',
            $response->getContent()
        );
    }

    public function test_dashboard_compound_library_filters_by_public_visibility(): void
    {
        Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
            'is_public' => true,
            'name' => 'VisibilityPublicStudyToken',
        ]);

        Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
            'is_public' => false,
            'name' => 'VisibilityPrivateStudyToken',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?tab=samples&samples_status=public');

        $response->assertOk();
        $response->assertSee('VisibilityPublicStudyToken', false);
        $response->assertDontSee('VisibilityPrivateStudyToken', false);
    }

    public function test_dashboard_with_non_personal_team(): void
    {
        $team = Team::factory()->create([
            'user_id' => $this->user->id,
            'personal_team' => false,
        ]);

        $this->user->current_team_id = $team->id;
        $this->user->save();

        Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => false,
        ]);

        Study::factory()->create([
            'team_id' => $team->id,
            'project_id' => null,
            'is_deleted' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_shared_with_me_requires_authentication(): void
    {
        $response = $this->get('/dashboard/shared-with-me');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shared_with_me_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/shared-with-me');

        $response->assertRedirect(route('dashboard', ['workspace' => 'shared']));

        $this->actingAs($this->user)
            ->get(route('dashboard', ['workspace' => 'shared']))
            ->assertStatus(200);
    }

    public function test_trashed_requires_authentication(): void
    {
        $response = $this->get('/dashboard/trashed');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_trashed_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/trashed');

        $response->assertRedirect(route('dashboard', ['workspace' => 'trashed']));

        $this->actingAs($this->user)
            ->get(route('dashboard', ['workspace' => 'trashed']))
            ->assertStatus(200);
    }

    public function test_starred_requires_authentication(): void
    {
        $response = $this->get('/dashboard/starred');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_starred_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/starred');

        $response->assertRedirect(route('dashboard', ['workspace' => 'starred']));

        $this->actingAs($this->user)
            ->get(route('dashboard', ['workspace' => 'starred']))
            ->assertStatus(200);
    }

    public function test_recent_requires_authentication(): void
    {
        $response = $this->get('/dashboard/recent');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_recent_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/recent');

        $response->assertRedirect(route('dashboard', ['workspace' => 'recent']));

        $this->actingAs($this->user)
            ->get(route('dashboard', ['workspace' => 'recent']))
            ->assertStatus(200);
    }

    public function test_onboarding_status_requires_authentication(): void
    {
        $response = $this->post('/onboarding/complete');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_onboarding_status_marks_user_as_onboarded(): void
    {
        $this->user->onboarded = false;
        $this->user->save();

        $this->assertFalse($this->user->onboarded);

        $response = $this->actingAs($this->user)
            ->post('/onboarding/complete');

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertTrue($this->user->onboarded);
    }

    public function test_onboarding_status_returns_user_data(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/onboarding/complete');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->user->id]);
    }

    public function test_onboarding_status_handles_incomplete_status(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/onboarding/incomplete');

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertFalse($this->user->onboarded);
    }

    public function test_skip_primer_requires_authentication(): void
    {
        $response = $this->post('/primer/skip');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_skip_primer_marks_user_as_primed(): void
    {
        $this->user->primed = false;
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->post('/primer/skip');

        $response->assertStatus(200);
        $this->assertEmpty($response->getContent());

        $this->user->refresh();
        $this->assertTrue($this->user->primed);
    }

    public function test_skip_primer_handles_already_primed_user(): void
    {
        $this->user->primed = true;
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->post('/primer/skip');

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertTrue($this->user->primed);
    }

    public function test_dashboard_filters_projects_by_status(): void
    {
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
            'status' => 'published',
            'name' => 'PublishedProjDashXYZ',
        ]);
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
            'status' => 'draft',
            'name' => 'DraftProjDashXYZ',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?projects_status=published');

        $response->assertStatus(200);
        $response->assertSee('PublishedProjDashXYZ', false);
        $response->assertDontSee('DraftProjDashXYZ', false);
    }

    public function test_dashboard_search_projects_by_name(): void
    {
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
            'name' => 'AlphaUniqueSearchToken',
        ]);
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
            'name' => 'BetaOtherThing',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard?projects_q=AlphaUniqueSearchToken');

        $response->assertStatus(200);
        $response->assertSee('AlphaUniqueSearchToken', false);
        $response->assertDontSee('BetaOtherThing', false);
    }

    public function test_dashboard_projects_second_page(): void
    {
        foreach (range(1, 11) as $i) {
            Project::factory()->create([
                'owner_id' => $this->user->id,
                'team_id' => $this->user->currentTeam->id,
                'is_deleted' => false,
                'name' => "PaginatedProj{$i}",
                'updated_at' => now()->subSeconds($i),
            ]);
        }

        $response = $this->actingAs($this->user)
            ->get('/dashboard?projects_page=2');

        $response->assertStatus(200);
        $response->assertSee('Showing 11 to 11', false);
        $response->assertSee('PaginatedProj11', false);
    }

    public function test_dashboard_inertia_payload_includes_embargo_project_fields_for_scheduled_release_ui(): void
    {
        $provisionalDoi = '10.5281/nmrxiv.dashboard-prov-'.Str::lower(Str::random(8));
        $obfuscation = 'obf-dashboard-embargo-'.Str::lower(Str::random(8));

        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
            'is_public' => false,
            'status' => 'embargo',
            'release_date' => now()->addDays(30),
            'obfuscationcode' => $obfuscation,
        ])->forceFill(['provisional_doi' => $provisionalDoi])->save();

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('projects.data', 1, fn (AssertableJson $json) => $json
                ->where('status', 'embargo')
                ->where('provisional_doi', $provisionalDoi)
                ->where('provisional_doi_url', fn (mixed $url): bool => is_string($url) && str_contains((string) $url, $provisionalDoi))
                ->where('obfuscationcode', $obfuscation)
                ->where('is_public', false)
            )
        );
    }
}
