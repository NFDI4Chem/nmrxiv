<?php

namespace Tests\Feature\Project;

use App\Models\License;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProjectShowRedirectsToPublicUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_project_show_redirects_to_public_url_when_identifier_exists(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 43559,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $response = $this->actingAs($owner)
            ->get('/dashboard/projects/'.$project->id);

        $response->assertRedirect();
        $this->assertStringContainsString(
            '/project/P43559',
            (string) $response->headers->get('Location')
        );
    }

    public function test_dashboard_project_show_preserves_allow_listed_query_string_on_redirect(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 12,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $response = $this->actingAs($owner)
            ->get('/dashboard/projects/'.$project->id.'?edit=license&tab=info&evil=1');

        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertStringContainsString('/project/P12', (string) $location);
        parse_str((string) parse_url((string) $location, PHP_URL_QUERY), $query);
        $this->assertSame('license', $query['edit'] ?? null);
        $this->assertSame('info', $query['tab'] ?? null);
        $this->assertArrayNotHasKey('evil', $query);
    }

    public function test_dashboard_project_show_renders_inertia_when_identifier_missing(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => null,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $response = $this->actingAs($owner)
            ->get('/dashboard/projects/'.$project->id);

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Project/Show')
        );
    }

    public function test_outside_user_cannot_use_dashboard_project_show(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $outsider = User::factory()->create();
        $team = $owner->currentTeam;
        $license = License::factory()->create();

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'identifier' => 77,
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->actingAs($outsider)
            ->get('/dashboard/projects/'.$project->id)
            ->assertForbidden();
    }
}
