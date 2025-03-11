<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageLicenseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if licenses can be fetched
     *
     * @return void
     */
    public function test_get_licenses()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('licenses');
        $response->assertStatus(200);
    }

    /**
     * Test if license in a project can be updated
     *
     * @return void
     */
    public function test_add_license_to_project()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());
        $license = License::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $body = $this->prepareBody($project, $license);

        $response = $this->updateProject($body, $project->id);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'license_id' => $license->id,
        ]);
    }

    /**
     * Test if license in a project cannot be updated by reviewer
     *
     * @return void
     */
    public function test_license_cannot_be_added_by_reviewer()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create();

        $reviewer = User::find($user->id);
        if (! is_null($reviewer)) {
            $project->users()->attach(
                $reviewer, ['role' => 'reviewer']
            );
        }

        $license = License::factory()->create();

        $body = $this->prepareBody($project, $license);

        // Update Project
        $response = $this->updateProject($body, $project->id);

        $response->assertStatus(403);
    }

    /**
     * Prepare request body for project
     *
     * @param  \App\Models\Project  $project
     * @return array $body
     */
    public function prepareBody($project, $license)
    {
        $body = [];
        if ($project) {
            $body = [
                'name' => $project->name,
                'description' => $project->description,
                'team_id' => $project->team_id,
                'owner_id' => $project->owner_id,
                'license' => $license,
                'license_id' => $license->id,
            ];
        }

        return $body;
    }

    /**
     * Make Request to update project
     *
     * @param  \App\Models\Project  $body
     * @param  int  $projectId
     * @return \Illuminate\Http\Response
     */
    public function updateProject($body, $projectId)
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
        ])->put('dashboard/projects/'.$projectId.'/update', $body);
    }
}
