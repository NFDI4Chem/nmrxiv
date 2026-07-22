<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectsPageTest extends TestCase
{
    use RefreshDatabase;

    private function attachPublicStudy(Project $project, User $owner): Study
    {
        return Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);
    }

    public function test_public_projects_page_can_be_rendered(): void
    {
        $page = $this->assertInertiaPageComponent($this->get('/projects'), 'Public/Projects');

        $this->assertArrayHasKey('projects', $page['props']);
        $this->assertArrayHasKey('filters', $page['props']);
    }

    public function test_public_projects_page_excludes_deleted_projects(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();

        $visibleProject = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);
        $this->attachPublicStudy($visibleProject, $owner);

        $deletedProject = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => true,
        ]);

        $page = $this->assertInertiaPageComponent($this->get('/projects'), 'Public/Projects');

        $projectIds = collect($page['props']['projects']['data'])->pluck('id');

        $this->assertTrue($projectIds->contains($visibleProject->id));
        $this->assertFalse($projectIds->contains($deletedProject->id));
    }

    public function test_public_projects_page_excludes_projects_without_public_studies(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();

        $projectWithStudies = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);
        $this->attachPublicStudy($projectWithStudies, $owner);

        $projectWithoutStudies = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);

        $projectWithDeletedStudies = Project::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => false,
        ]);
        Study::factory()->create([
            'project_id' => $projectWithDeletedStudies->id,
            'owner_id' => $owner->id,
            'team_id' => $owner->currentTeam->id,
            'is_public' => true,
            'is_archived' => false,
            'is_deleted' => true,
        ]);

        $page = $this->assertInertiaPageComponent($this->get('/projects'), 'Public/Projects');

        $projectIds = collect($page['props']['projects']['data'])->pluck('id');

        $this->assertTrue($projectIds->contains($projectWithStudies->id));
        $this->assertFalse($projectIds->contains($projectWithoutStudies->id));
        $this->assertFalse($projectIds->contains($projectWithDeletedStudies->id));
    }
}
