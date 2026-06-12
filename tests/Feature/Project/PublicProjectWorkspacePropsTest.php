<?php

namespace Tests\Feature\Project;

use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectWorkspacePropsTest extends TestCase
{
    use RefreshDatabase;

    private function createPublicProject(User $owner, int $identifier): Project
    {
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'license_id' => $license->id,
            'identifier' => $identifier,
            'is_public' => true,
        ]);

        $project->users()->attach($owner, ['role' => 'creator']);

        return $project;
    }

    public function test_guest_public_project_page_does_not_include_workspace_props(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $this->createPublicProject($owner, 501);

        $page = $this->assertInertiaPageComponent($this->get('/project/P501'), 'Public/Project/Show');

        $this->assertArrayNotHasKey('workspace', $page['props']);
    }

    public function test_project_member_sees_workspace_on_public_project_page(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $this->createPublicProject($owner, 502);

        $page = $this->assertInertiaPageComponent(
            $this->actingAs($owner)->get('/project/P502'),
            'Public/Project/Show'
        );

        $this->assertArrayHasKey('workspace', $page['props']);
        $this->assertArrayHasKey('dashboardProject', $page['props']['workspace']);
        $this->assertArrayHasKey('projectPermissions', $page['props']['workspace']);
        // The project owner always resolves to 'owner', even when also attached
        // with a 'creator' membership role.
        $this->assertSame('owner', $page['props']['workspace']['role']);
    }

    public function test_authenticated_non_member_receives_read_only_workspace_on_public_project(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $outsider = User::factory()->create();
        $this->createPublicProject($owner, 503);

        $page = $this->assertInertiaPageComponent(
            $this->actingAs($outsider)->get('/project/P503'),
            'Public/Project/Show'
        );

        $this->assertArrayHasKey('workspace', $page['props']);
        $this->assertNull($page['props']['workspace']['role']);
        $this->assertFalse($page['props']['workspace']['projectPermissions']['canUpdateProject']);
        $this->assertFalse($page['props']['workspace']['projectPermissions']['canDeleteProject']);
        $this->assertFalse($page['props']['workspace']['projectPermissions']['canManageSettings']);
    }

    public function test_dashboard_studies_endpoint_allows_outsider_on_public_project(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $outsider = User::factory()->create();
        $project = $this->createPublicProject($owner, 504);

        $this->actingAs($outsider)
            ->get(route('dashboard.project.studies', $project->id))
            ->assertOk();
    }

    public function test_public_project_includes_samples_count_for_public_studies(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $project = $this->createPublicProject($owner, 506);

        Study::factory()->count(2)->for($project)->create([
            'is_public' => true,
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
        ]);

        $page = $this->assertInertiaPageComponent($this->get('/project/P506'), 'Public/Project/Show');

        $this->assertSame(2, $page['props']['project']['data']['samples_count']);
    }
}
