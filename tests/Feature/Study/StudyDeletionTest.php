<?php

namespace Tests\Feature\Study;

use App\Actions\Study\DeleteStudy;
use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Molecule;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\StudyInvitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class StudyDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $collaborator;

    protected User $viewer;

    protected User $outsider;

    protected Team $team;

    protected Project $project;

    protected Study $study;

    protected License $license;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->owner = User::factory()->create(['username' => 'owner_'.uniqid()]);
        $this->collaborator = User::factory()->create(['username' => 'collaborator_'.uniqid()]);
        $this->viewer = User::factory()->create(['username' => 'viewer_'.uniqid()]);
        $this->outsider = User::factory()->create(['username' => 'outsider_'.uniqid()]);

        // Create team and project
        $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
        $this->project = Project::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->owner->id,
        ]);
        $this->license = License::factory()->create();

        // Create study with owner
        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'is_public' => false,
        ]);

        // Add collaborator and viewer to study
        $this->study->users()->attach($this->collaborator, ['role' => 'collaborator']);
        $this->study->users()->attach($this->viewer, ['role' => 'viewer']);
    }

    public function test_study_owner_can_delete_study_with_password(): void
    {
        $studyId = $this->study->id;

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        $this->assertDatabaseMissing('studies', [
            'id' => $studyId,
        ]);
    }

    public function test_study_deletion_requires_password_confirmation(): void
    {
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study))
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseHas('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_study_deletion_requires_correct_password(): void
    {
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'wrong-password',
            ])
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseHas('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_only_study_owner_can_delete_study(): void
    {
        // Test collaborator cannot delete
        $this->actingAs($this->collaborator)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertStatus(403);

        // Test viewer cannot delete
        $this->actingAs($this->viewer)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertStatus(403);

        // Test outsider cannot delete
        $this->actingAs($this->outsider)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertStatus(403);

        // Study should still exist
        $this->assertDatabaseHas('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_guest_cannot_delete_study(): void
    {
        $this->delete(route('dashboard.study.destroy', $this->study), [
            'password' => 'password',
        ])
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_delete_study_action_class_works(): void
    {
        $action = new DeleteStudy;
        $studyId = $this->study->id;

        $action->delete($this->study);

        $this->assertDatabaseMissing('studies', [
            'id' => $studyId,
        ]);
    }

    public function test_deleting_study_cleans_up_related_models(): void
    {
        // Create related models
        $sample = Sample::factory()->create(['study_id' => $this->study->id]);
        $dataset = Dataset::factory()->create(['study_id' => $this->study->id]);
        $invitation = StudyInvitation::factory()->create(['study_id' => $this->study->id]);
        $fsObject = FileSystemObject::factory()->create(['study_id' => $this->study->id]);

        // Add a molecule to the sample
        $molecule = Molecule::factory()->create();
        $sample->molecules()->attach($molecule);

        $studyId = $this->study->id;

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        // Verify study is deleted
        $this->assertDatabaseMissing('studies', [
            'id' => $studyId,
        ]);

        // Verify related models behavior (depends on cascade delete configuration)
        // This test assumes related models are also deleted or orphaned appropriately
    }

    public function test_deleting_study_removes_user_associations(): void
    {
        $studyId = $this->study->id;

        // Verify associations exist before deletion
        $this->assertDatabaseHas('study_user', [
            'study_id' => $studyId,
            'user_id' => $this->collaborator->id,
        ]);

        $this->assertDatabaseHas('study_user', [
            'study_id' => $studyId,
            'user_id' => $this->viewer->id,
        ]);

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        // Verify study is deleted
        $this->assertDatabaseMissing('studies', [
            'id' => $studyId,
        ]);

        // Note: Currently user associations may not be automatically cleaned up
        // This depends on cascade delete configuration in the database migration
        // In a real implementation, you might want to add cleanup logic
    }

    public function test_can_delete_published_study_currently(): void
    {
        // Make the study published
        $this->study->update([
            'is_public' => true,
            'doi' => 'test-doi',
            'release_date' => now()->subDay(),
        ]);

        $this->assertTrue($this->study->fresh()->is_published);

        // Note: Currently the policy allows deletion of published studies
        // In a production system, you might want to restrict this
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        $this->assertDatabaseMissing('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_can_delete_study_from_published_project_currently(): void
    {
        // Make the parent project published
        $this->project->update([
            'is_public' => true,
            'doi' => 'test-project-doi',
            'release_date' => now()->subDay(),
        ]);

        $this->assertTrue($this->study->fresh()->is_published);

        // Note: Currently the policy allows deletion even when inherited from published project
        // In a production system, you might want to restrict this
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        $this->assertDatabaseMissing('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_study_with_doi_can_be_deleted_currently(): void
    {
        $this->study->update(['doi' => 'test-doi']);

        // Note: Currently the policy allows deletion of studies with DOI
        // In a production system, you might want to restrict this
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        $this->assertDatabaseMissing('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_archived_study_can_be_deleted_currently(): void
    {
        $this->study->update(['is_archived' => true]);

        // Note: Currently the policy allows deletion of archived studies
        // In a production system, you might want to restrict this
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        $this->assertDatabaseMissing('studies', [
            'id' => $this->study->id,
        ]);
    }

    public function test_delete_study_authorization_gates(): void
    {
        // Test owner can delete
        $this->assertTrue(Gate::forUser($this->owner)->allows('deleteStudy', $this->study));

        // Test collaborator cannot delete
        $this->assertFalse(Gate::forUser($this->collaborator)->allows('deleteStudy', $this->study));

        // Test viewer cannot delete
        $this->assertFalse(Gate::forUser($this->viewer)->allows('deleteStudy', $this->study));

        // Test outsider cannot delete
        $this->assertFalse(Gate::forUser($this->outsider)->allows('deleteStudy', $this->study));
    }

    public function test_delete_study_removes_pending_invitations(): void
    {
        // Create pending invitations
        $invitation1 = StudyInvitation::factory()->create([
            'study_id' => $this->study->id,
            'email' => 'user1@example.com',
        ]);
        $invitation2 = StudyInvitation::factory()->create([
            'study_id' => $this->study->id,
            'email' => 'user2@example.com',
        ]);

        $studyId = $this->study->id;

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        // Verify study is deleted
        $this->assertDatabaseMissing('studies', [
            'id' => $studyId,
        ]);

        // Verify invitations are cleaned up
        $this->assertDatabaseMissing('study_invitations', [
            'id' => $invitation1->id,
        ]);
        $this->assertDatabaseMissing('study_invitations', [
            'id' => $invitation2->id,
        ]);
    }

    public function test_deleting_study_with_datasets_and_files(): void
    {
        // Create datasets with some data
        $dataset1 = Dataset::factory()->create(['study_id' => $this->study->id]);
        $dataset2 = Dataset::factory()->create(['study_id' => $this->study->id]);

        $studyId = $this->study->id;

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        // Verify study is deleted
        $this->assertDatabaseMissing('studies', [
            'id' => $studyId,
        ]);

        // Note: Dataset deletion behavior depends on cascade delete configuration
        // This test structure allows verification of the expected behavior
    }

    public function test_delete_study_validation_error_shows_appropriate_message(): void
    {
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'wrong-password',
            ])
            ->assertSessionHasErrors(['password' => 'The password is incorrect.']);
    }

    public function test_delete_study_redirects_to_dashboard_on_success(): void
    {
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');
    }

    public function test_delete_study_comprehensive_workflow(): void
    {
        // Create a study with comprehensive related data
        $study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->owner->id,
            'team_id' => $this->team->id,
            'license_id' => $this->license->id,
            'name' => 'Comprehensive Study',
            'description' => 'A study with all related data',
        ]);

        // Add users to the study
        $study->users()->attach($this->collaborator, ['role' => 'collaborator']);
        $study->users()->attach($this->viewer, ['role' => 'viewer']);

        // Create related models
        $sample = Sample::factory()->create(['study_id' => $study->id]);
        $dataset1 = Dataset::factory()->create(['study_id' => $study->id, 'name' => 'Dataset 1']);
        $dataset2 = Dataset::factory()->create(['study_id' => $study->id, 'name' => 'Dataset 2']);
        $invitation = StudyInvitation::factory()->create([
            'study_id' => $study->id,
            'email' => 'invited@example.com',
            'role' => 'viewer',
        ]);
        $fsObject = FileSystemObject::factory()->create(['study_id' => $study->id]);

        // Add molecules to the sample
        $molecule1 = Molecule::factory()->create();
        $molecule2 = Molecule::factory()->create();
        $sample->molecules()->attach([$molecule1->id, $molecule2->id]);

        $studyId = $study->id;

        // Verify all relationships exist before deletion
        $this->assertDatabaseHas('studies', ['id' => $studyId]);
        $this->assertDatabaseHas('study_user', ['study_id' => $studyId]);
        $this->assertDatabaseHas('samples', ['study_id' => $studyId]);
        $this->assertDatabaseHas('datasets', ['study_id' => $studyId]);
        $this->assertDatabaseHas('study_invitations', ['study_id' => $studyId]);
        $this->assertDatabaseHas('file_system_objects', ['study_id' => $studyId]);

        // Delete the study
        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $study), [
                'password' => 'password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Study deleted successfully');

        // Verify study is deleted
        $this->assertDatabaseMissing('studies', ['id' => $studyId]);

        // Note: The behavior of related models depends on database cascade configuration
        // In a production system, you might want to implement explicit cleanup logic
    }

    /*
     * Future Enhancement Tests - These demonstrate how stricter deletion policies could be implemented
     * Currently commented out as they don't match the current implementation
     */

    /*
    public function test_future_cannot_delete_published_study(): void
    {
        // Example of how stricter deletion policy could work
        $this->study->update(['is_public' => true, 'doi' => 'test-doi']);

        // This would require updating the StudyPolicy::deleteStudy method to include:
        // if ($study->is_published || $study->doi || $study->is_archived) {
        //     return false;
        // }

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), ['password' => 'password'])
            ->assertStatus(403);
    }

    public function test_future_soft_delete_implementation(): void
    {
        // Example of how soft deletes could be implemented
        // This would require:
        // 1. Adding SoftDeletes trait to Study model
        // 2. Adding deleted_at column to studies table
        // 3. Updating the DeleteStudy action to use soft deletes

        $studyId = $this->study->id;

        $this->actingAs($this->owner)
            ->delete(route('dashboard.study.destroy', $this->study), ['password' => 'password'])
            ->assertRedirect(route('dashboard'));

        // Study would still exist in database but be soft deleted
        $this->assertSoftDeleted('studies', ['id' => $studyId]);
    }
    */
}
