<?php

namespace Tests\Feature\Project;

use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PublicProjectWorkspacePropsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_public_project_page_does_not_include_workspace_props(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 501,
            'is_public' => true,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->get('/project/P501')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Public/Project/Show')
                ->missing('workspace')
            );
    }

    public function test_project_member_sees_workspace_on_public_project_page(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 502,
            'is_public' => true,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->actingAs($owner)
            ->get('/project/P502')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Public/Project/Show')
                ->has('workspace', fn (AssertableInertia $w) => $w
                    ->has('dashboardProject')
                    ->has('projectPermissions')
                    ->where('role', 'creator')
                )
            );
    }

    public function test_authenticated_non_member_does_not_receive_workspace_on_public_project(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $outsider = User::factory()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 503,
            'is_public' => true,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->actingAs($outsider)
            ->get('/project/P503')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Public/Project/Show')
                ->missing('workspace')
            );
    }

    public function test_dashboard_studies_endpoint_forbids_outsider_on_public_project(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $outsider = User::factory()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 504,
            'is_public' => true,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->actingAs($outsider)
            ->get(route('dashboard.project.studies', $project->id))
            ->assertForbidden();
    }

    public function test_public_project_includes_samples_count_for_public_studies(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 506,
            'is_public' => true,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        Study::factory()->count(2)->for($project)->create([
            'is_public' => true,
            'owner_id' => $owner->id,
            'team_id' => $team->id,
        ]);

        $this->get('/project/P506')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Public/Project/Show')
                ->where('project.data.samples_count', 2)
            );
    }
}
