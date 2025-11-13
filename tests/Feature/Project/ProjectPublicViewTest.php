<?php

namespace Tests\Feature\Project;

use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPublicViewTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Project $publicProject;

    private Project $privateProject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->withPersonalTeam()->create(['username' => 'testowner']);

        $this->publicProject = Project::factory()
            ->for($this->owner, 'owner')
            ->create([
                'slug' => 'test-project',
                'is_public' => true,
                'name' => 'Test Public Project',
                'team_id' => $this->owner->personalTeam()->id,
            ]);

        $this->privateProject = Project::factory()
            ->for($this->owner, 'owner')
            ->create([
                'slug' => 'private-project',
                'is_public' => false,
                'name' => 'Test Private Project',
                'team_id' => $this->owner->personalTeam()->id,
            ]);
    }

    public function test_public_project_default_view()
    {
        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
        ]));

        // Debug the response if it's not 200
        if ($response->status() !== 200) {
            $content = $response->getContent();
            $this->fail("Expected 200 status but got {$response->status()}. Content: ".substr($content, 0, 500));
        }

        $response->assertStatus(200);
    }

    public function test_public_project_samples_tab_view()
    {
        $study = Study::factory()->for($this->publicProject)->create();

        // Create a sample for the study to avoid null access in StudyResource
        \App\Models\Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $this->publicProject->id,
        ]);

        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'samples',
        ]));

        $response->assertStatus(200);
    }

    public function test_public_project_files_tab_view()
    {
        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'files',
        ]));

        $response->assertStatus(200);
    }

    public function test_public_project_license_tab_view()
    {
        $license = License::factory()->create();
        $this->publicProject->update(['license_id' => $license->id]);

        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'license',
        ]));

        $response->assertStatus(200);
    }

    public function test_public_project_study_tab_view()
    {
        $study = Study::factory()->for($this->publicProject)->create([
            'slug' => 'test-study',
            'owner_id' => $this->owner->id,  // Ensure study has same owner as project
        ]);

        // Create a sample for the study to avoid null access in StudyResource
        \App\Models\Sample::factory()->create([
            'study_id' => $study->id,
            'project_id' => $this->publicProject->id,
        ]);

        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'study',
            'id' => $study->slug,
        ]));

        $response->assertStatus(200);
    }

    public function test_private_project_requires_authorization_for_unauthorized_user()
    {
        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->privateProject->slug,
        ]));

        $response->assertStatus(403);
    }

    public function test_private_project_accessible_to_owner()
    {
        $response = $this->actingAs($this->owner)
            ->get(route('public.project', [
                'owner' => $this->owner->username,
                'slug' => $this->privateProject->slug,
            ]));

        $response->assertStatus(200);
    }

    public function test_private_project_accessible_to_collaborator()
    {
        $collaborator = User::factory()->create();

        // Add collaborator to project
        $this->privateProject->users()->attach($collaborator, [
            'role' => 'collaborator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($collaborator)
            ->get(route('public.project', [
                'owner' => $this->owner->username,
                'slug' => $this->privateProject->slug,
            ]));

        $response->assertStatus(200);
    }

    public function test_nonexistent_project_returns_404()
    {
        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => 'nonexistent-project',
        ]));

        $response->assertStatus(404);
    }

    public function test_nonexistent_owner_returns_404()
    {
        $response = $this->get(route('public.project', [
            'owner' => 'nonexistent-user',
            'slug' => $this->publicProject->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_nonexistent_study_in_study_tab_returns_404()
    {
        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'study',
            'id' => 'nonexistent-study',
        ]));

        $response->assertStatus(404);
    }

    public function test_study_tab_with_wrong_project_returns_404()
    {
        $otherProject = Project::factory()
            ->for($this->owner, 'owner')
            ->create(['is_public' => true]);

        $study = Study::factory()->for($otherProject)->create(['slug' => 'other-study']);

        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'study',
            'id' => $study->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_invalid_tab_defaults_to_info()
    {
        $response = $this->get(route('public.project', [
            'owner' => $this->owner->username,
            'slug' => $this->publicProject->slug,
            'tab' => 'invalid-tab',
        ]));

        $response->assertStatus(200);
    }
}
