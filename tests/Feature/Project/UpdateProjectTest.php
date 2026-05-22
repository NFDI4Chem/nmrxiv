<?php

namespace Tests\Feature\Project;

use App\Jobs\ProcessSubmission;
use App\Models\Citation;
use App\Models\Draft;
use App\Models\License;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class UpdateProjectTest extends ProjectFeatureTestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $collaborator;

    private User $viewer;

    private User $outsideUser;

    private Team $team;

    private Project $project;

    private License $license;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->collaborator = User::factory()->create();
        $this->viewer = User::factory()->create();
        $this->outsideUser = User::factory()->create();
        $this->team = $this->owner->currentTeam;
        $this->license = License::factory()->create();

        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'name' => 'Original Project Name',
            'description' => 'Original description',
            'is_public' => false,
            'license_id' => $this->license->id,
            'validation_id' => null,
        ]);

        // Attach users with different roles
        $this->project->users()->attach($this->owner, ['role' => 'creator']);
        $this->project->users()->attach($this->collaborator, ['role' => 'collaborator']);
        $this->project->users()->attach($this->viewer, ['role' => 'viewer']);
    }

    public function test_project_owner_can_update_project_via_http()
    {
        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated project description',
            'color' => '#FF5733',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        // The controller returns a redirect with success message for form requests
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertEquals('Updated Project Name', $this->project->name);
        $this->assertEquals('Updated project description', $this->project->description);
        $this->assertEquals('#FF5733', $this->project->color);
    }

    public function test_project_update_can_suppress_success_flash_message()
    {
        $updateData = [
            'name' => 'Updated Without Toast',
            'description' => 'No flash please',
            'suppress_project_updated_flash' => true,
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionMissing('success');

        $this->project->refresh();
        $this->assertEquals('Updated Without Toast', $this->project->name);
    }

    public function test_project_collaborator_can_update_project_via_http()
    {
        $updateData = [
            'name' => 'Updated by Collaborator',
            'description' => 'Updated by collaborator',
        ];

        $response = $this->actingAs($this->collaborator)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertEquals('Updated by Collaborator', $this->project->name);
        $this->assertEquals('Updated by collaborator', $this->project->description);
    }

    public function test_project_viewer_cannot_update_project()
    {
        $updateData = [
            'name' => 'Attempted Update by Viewer',
            'description' => 'This should not work',
        ];

        $response = $this->actingAs($this->viewer)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
        $this->assertEquals('Original description', $this->project->description);
    }

    public function test_unauthorized_user_cannot_update_project()
    {
        $updateData = [
            'name' => 'Unauthorized Update',
            'description' => 'This should be blocked',
        ];

        $response = $this->actingAs($this->outsideUser)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
        $this->assertEquals('Original description', $this->project->description);
    }

    public function test_unauthenticated_user_cannot_update_project()
    {
        $updateData = [
            'name' => 'Unauthenticated Update',
            'description' => 'This should redirect to login',
        ];

        $response = $this->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertRedirect('/login');

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
    }

    public function test_project_update_validates_required_fields()
    {
        $updateData = [
            'name' => '', // Empty name should fail validation
            'description' => 'Valid description',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        // Depending on validation rules, this might be 422 or redirect with errors
        $this->assertContains($response->getStatusCode(), [302, 422]);

        if ($response->getStatusCode() === 302) {
            $response->assertSessionHasErrors(['name']);
        }

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name); // Should not change
    }

    public function test_project_update_handles_unique_name_constraint()
    {
        // Create another project with a specific name
        $existingProject = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Existing Project Name',
        ]);

        $updateData = [
            'name' => 'Existing Project Name', // Duplicate name
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        // Should fail validation due to unique constraint
        $this->assertContains($response->getStatusCode(), [302, 422]);

        if ($response->getStatusCode() === 302) {
            $response->assertSessionHasErrors(['name']);
        }

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name); // Should not change
    }

    public function test_project_update_allows_keeping_its_current_name()
    {
        $updateData = [
            'name' => 'Original Project Name',
            'description' => 'Updated description while keeping same name',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');
        $response->assertSessionDoesntHaveErrors(['name']);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
        $this->assertEquals('Updated description while keeping same name', $this->project->description);
    }

    public function test_project_update_with_existing_name_returns_json_validation_error()
    {
        Project::factory()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Existing Project Name',
        ]);

        $updateData = [
            'name' => 'Existing Project Name',
            'description' => 'Should fail with duplicate name',
        ];

        $response = $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
    }

    public function test_project_update_can_change_license()
    {
        $newLicense = License::factory()->create();

        $updateData = [
            'name' => 'Project with New License',
            'license_id' => $newLicense->id,
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertEquals($newLicense->id, $this->project->license_id);
        $this->assertEquals('Project with New License', $this->project->name);
    }

    public function test_published_project_cannot_be_updated()
    {
        // Make project public (which makes is_published return true)
        $this->project->update(['is_public' => true]);

        $updateData = [
            'name' => 'Should Not Update Published Project',
            'description' => 'This should be blocked',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
    }

    public function test_archived_project_cannot_be_updated()
    {
        // Archive the project - ensure boolean true
        $this->project->is_archived = true;
        $this->project->save();

        $updateData = [
            'name' => 'Should Not Update Archived Project',
            'description' => 'This should be blocked',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
    }

    public function test_deleted_project_cannot_be_updated()
    {
        // Mark project as deleted - ensure boolean true
        $this->project->is_deleted = true;
        $this->project->save();

        $updateData = [
            'name' => 'Should Not Update Deleted Project',
            'description' => 'This should be blocked',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(403);

        $this->project->refresh();
        $this->assertEquals('Original Project Name', $this->project->name);
    }

    public function test_project_update_preserves_unchanged_fields()
    {
        $originalCreatedAt = $this->project->created_at;
        $originalOwnerId = $this->project->owner_id;
        $originalTeamId = $this->project->team_id;

        $updateData = [
            'name' => 'Only Name Changed',
            // Don't include other fields in update
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertEquals('Only Name Changed', $this->project->name);
        $this->assertEquals('Original description', $this->project->description); // Unchanged
        $this->assertEquals('only-name-changed', $this->project->slug); // Slug updates when name changes
        $this->assertEquals($originalCreatedAt, $this->project->created_at);
        $this->assertEquals($originalOwnerId, $this->project->owner_id);
        $this->assertEquals($originalTeamId, $this->project->team_id);
    }

    public function test_project_update_updates_timestamps()
    {
        $originalUpdatedAt = $this->project->updated_at;

        // Wait a moment to ensure timestamp difference
        sleep(1);

        $updateData = [
            'name' => 'Updated Name for Timestamp Test',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertNotEquals($originalUpdatedAt, $this->project->updated_at);
        $this->assertTrue($this->project->updated_at->greaterThan($originalUpdatedAt));
    }

    public function test_project_update_handles_json_requests()
    {
        $updateData = [
            'name' => 'JSON Update Test',
            'description' => 'Updated via JSON',
        ];

        $response = $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([]); // Expects empty JSON response on success

        $this->project->refresh();
        $this->assertEquals('JSON Update Test', $this->project->name);
        $this->assertEquals('Updated via JSON', $this->project->description);
    }

    public function test_project_update_handles_form_requests()
    {
        $updateData = [
            'name' => 'Form Update Test',
            'description' => 'Updated via form submission',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertEquals('Form Update Test', $this->project->name);
        $this->assertEquals('Updated via form submission', $this->project->description);
    }

    public function test_project_update_response_includes_success_message()
    {
        $updateData = [
            'name' => 'Success Message Test',
        ];

        $response = $this->actingAs($this->owner)
            ->from('/dashboard/projects')
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertRedirect('/dashboard/projects');
        $response->assertSessionHas('success', 'Project updated successfully');
    }

    public function test_nonexistent_project_returns_404()
    {
        $updateData = [
            'name' => 'This should not work',
        ];

        $response = $this->actingAs($this->owner)
            ->put('/dashboard/projects/99999', $updateData);

        $response->assertStatus(404);
    }

    public function test_project_update_handles_special_characters()
    {
        $updateData = [
            'name' => 'Çhemïcal Prøject with Ñ & special chars',
            'description' => 'Description with émojis 🧪⚗️ and symbols @#$%',
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Project updated successfully');

        $this->project->refresh();
        $this->assertEquals('Çhemïcal Prøject with Ñ & special chars', $this->project->name);
        $this->assertEquals('Description with émojis 🧪⚗️ and symbols @#$%', $this->project->description);
    }

    public function test_project_update_handles_long_content()
    {
        $longName = str_repeat('A', 255); // Test max length
        $longDescription = str_repeat('This is a very long description.', 100);

        $updateData = [
            'name' => $longName,
            'description' => $longDescription,
        ];

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/update", $updateData);

        // Should either succeed or fail validation gracefully
        $this->assertContains($response->getStatusCode(), [302, 422]);

        if ($response->getStatusCode() === 302) {
            $response->assertSessionHas('success', 'Project updated successfully');
            $this->project->refresh();
            $this->assertEquals($longName, $this->project->name);
            $this->assertEquals($longDescription, $this->project->description);
        }
    }

    public function test_project_owner_can_update_release_date_via_http()
    {
        $releaseDate = now()->addDays(30)->format('Y-m-d');

        $response = $this->actingAs($this->owner)
            ->put("/dashboard/projects/{$this->project->id}/updateReleaseDate", [
                'name' => $this->project->name,
                'release_date' => $releaseDate,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', "Project's release date updated successfully");

        $this->project->refresh();
        $this->assertEquals($releaseDate, $this->project->release_date ?
            Carbon::parse($this->project->release_date)->format('Y-m-d') : null);
    }

    public function test_project_release_date_update_requires_authorization()
    {
        $releaseDate = now()->addDays(30)->format('Y-m-d H:i:s');

        $updateData = [
            'name' => $this->project->name,
            'release_date' => $releaseDate,
        ];

        $response = $this->actingAs($this->viewer)
            ->put("/dashboard/projects/{$this->project->id}/updateReleaseDate", $updateData);

        $response->assertForbidden();

        $this->project->refresh();
        $this->assertNull($this->project->release_date);
    }

    public function test_project_release_date_update_response_includes_success_message()
    {
        $releaseDate = now()->addDays(30)->format('Y-m-d H:i:s');

        $updateData = [
            'name' => $this->project->name,
            'release_date' => $releaseDate,
        ];

        $response = $this->actingAs($this->owner)
            ->from('/dashboard/projects')
            ->put("/dashboard/projects/{$this->project->id}/updateReleaseDate", $updateData);

        $response->assertRedirect('/dashboard/projects');
        $response->assertSessionHas('success', "Project's release date updated successfully");
    }

    public function test_project_release_date_can_be_updated_via_json()
    {
        $releaseDate = now()->addDays(30)->format('Y-m-d H:i:s');

        $updateData = [
            'name' => $this->project->name,
            'release_date' => $releaseDate,
        ];

        $response = $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/updateReleaseDate", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([]); // Expects empty JSON response on success

        $this->project->refresh();
        $this->assertEquals($releaseDate, $this->project->release_date ?
            Carbon::parse($this->project->release_date)->format('Y-m-d H:i:s') : null);
    }

    public function test_project_tags_can_be_cleared_when_project_tags_updated_flag_is_set(): void
    {
        $this->project->syncTagsWithType(['alpha', 'beta'], 'Project');
        $this->project->load('tags');
        $this->assertCount(2, $this->project->tags);

        $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/update", [
                'name' => $this->project->name,
                'description' => $this->project->description,
                'project_tags_updated' => true,
                'tags_array' => [],
            ])
            ->assertSuccessful();

        $this->project->refresh();
        $this->project->load('tags');
        $this->assertCount(0, $this->project->tags);
    }

    public function test_project_tags_cleared_when_project_tags_updated_without_tags_array_key(): void
    {
        $this->project->syncTagsWithType(['keep-me'], 'Project');
        $this->project->load('tags');
        $this->assertCount(1, $this->project->tags);

        $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/update", [
                'name' => $this->project->name,
                'description' => $this->project->description,
                'project_tags_updated' => true,
            ])
            ->assertSuccessful();

        $this->project->refresh();
        $this->project->load('tags');
        $this->assertCount(0, $this->project->tags);
    }

    public function test_project_species_can_be_cleared_when_project_species_updated_flag_is_set(): void
    {
        $this->project->update([
            'species' => json_encode([['id' => 'NCBITaxon_9606', 'label' => 'Homo sapiens']]),
        ]);

        $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/update", [
                'name' => $this->project->name,
                'description' => $this->project->description,
                'project_species_updated' => true,
                'species' => [],
            ])
            ->assertSuccessful();

        $this->project->refresh();
        $this->assertSame('[]', $this->project->species);
    }

    public function test_project_species_cleared_when_project_species_updated_without_species_key(): void
    {
        $this->project->update([
            'species' => json_encode([['id' => 'NCBITaxon_9606', 'label' => 'Homo sapiens']]),
        ]);

        $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$this->project->id}/update", [
                'name' => $this->project->name,
                'description' => $this->project->description,
                'project_species_updated' => true,
            ])
            ->assertSuccessful();

        $this->project->refresh();
        $this->assertSame('[]', $this->project->species);
    }

    public function test_embargo_project_show_loads_with_edit_release_date_query(): void
    {
        $embargoProject = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'doi' => '10.5281/nmrxiv.test-embargo',
            'release_date' => now()->addDays(20),
            'license_id' => $this->license->id,
            'status' => 'embargo',
        ]);
        $embargoProject->users()->attach($this->owner, ['role' => 'creator']);

        $response = $this->actingAs($this->owner)
            ->get("/dashboard/projects/{$embargoProject->id}?edit=release_date");

        $this->assertInertiaPageComponent($response, 'Project/Show');
    }

    public function test_project_owner_can_publish_overdue_embargo_project_via_release_now_route(): void
    {
        Queue::fake();
        config(['validations.embargo_release_now_pass.project' => []]);
        config(['validations.embargo_release_now_pass.study' => []]);
        config(['validations.embargo_release_now_pass.dataset' => []]);

        $draft = Draft::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'project_enabled' => true,
        ]);
        $validation = Validation::factory()->passed()->create();
        $embargoProject = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'is_public' => false,
            'status' => 'embargo',
            'doi' => '10.5281/nmrxiv.release-now',
            'release_date' => now()->subDay(),
            'draft_id' => $draft->id,
            'validation_id' => $validation->id,
            'schema_version' => 'embargo_release_now_pass',
        ]);
        $embargoProject->users()->attach($this->owner, ['role' => 'creator']);
        $citation = Citation::factory()->create(['doi' => '10.1234/citation']);
        $embargoProject->citations()->attach($citation->id, [
            'user' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->withHeader('X-Inertia', 'true')
            ->from('/dashboard/projects')
            ->put("/dashboard/projects/{$embargoProject->id}/releaseNow");

        $response->assertRedirect('/dashboard/projects');
        $response->assertSessionHas('success', 'Your submission has been queued for processing.');

        $embargoProject->refresh();
        $this->assertSame('queued', $embargoProject->status);
        $this->assertSame(now()->startOfDay()->toDateString(), $embargoProject->release_date->toDateString());
        Queue::assertPushed(ProcessSubmission::class, fn (ProcessSubmission $job) => $job->project->id === $embargoProject->id);
    }

    public function test_release_now_returns_validation_report_when_embargo_project_fails_validation(): void
    {
        Queue::fake();
        config(['validations.embargo_release_now_fail.project' => [
            'citations' => 'required',
        ]]);
        config(['validations.embargo_release_now_fail.study' => []]);
        config(['validations.embargo_release_now_fail.dataset' => []]);

        $draft = Draft::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'project_enabled' => true,
        ]);
        $validation = Validation::factory()->passed()->create();
        $originalReleaseDate = now()->subDays(3);
        $embargoProject = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'is_public' => false,
            'status' => 'embargo',
            'doi' => '10.5281/nmrxiv.release-now-validation',
            'release_date' => $originalReleaseDate,
            'draft_id' => $draft->id,
            'validation_id' => $validation->id,
            'schema_version' => 'embargo_release_now_fail',
        ]);
        $embargoProject->users()->attach($this->owner, ['role' => 'creator']);

        $response = $this->actingAs($this->owner)
            ->putJson("/dashboard/projects/{$embargoProject->id}/releaseNow");

        $response->assertStatus(422)
            ->assertJsonPath('errors', 'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us at info.nmrxiv@uni-jena.de')
            ->assertJsonPath('validation.report.project.status', false)
            ->assertJsonPath('validation.report.project.citations', 'false|required');

        $embargoProject->refresh();
        $this->assertSame('embargo', $embargoProject->status);
        $this->assertSame($originalReleaseDate->toDateString(), $embargoProject->release_date->toDateString());
        Queue::assertNothingPushed();
    }
}
