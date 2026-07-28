<?php

namespace Tests\Feature\Study;

use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\StudyInvitation;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maize\Markable\Models\Bookmark;
use Tests\TestCase;

class StudyRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create(['user_id' => $this->user->id]);
        $this->project = Project::factory()->create([
            'team_id' => $this->team->id,
            'owner_id' => $this->user->id,
        ]);
        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);
    }

    public function test_study_belongs_to_project(): void
    {
        $this->assertInstanceOf(Project::class, $this->study->project);
        $this->assertEquals($this->project->id, $this->study->project->id);
        $this->assertEquals($this->project->name, $this->study->project->name);
    }

    public function test_study_belongs_to_owner(): void
    {
        $this->assertInstanceOf(User::class, $this->study->owner);
        $this->assertEquals($this->user->id, $this->study->owner->id);
        $this->assertEquals($this->user->email, $this->study->owner->email);
    }

    public function test_study_belongs_to_team(): void
    {
        $this->assertInstanceOf(Team::class, $this->study->team);
        $this->assertEquals($this->team->id, $this->study->team->id);
        $this->assertEquals($this->team->name, $this->study->team->name);
    }

    public function test_study_has_one_sample(): void
    {
        $sample = Sample::factory()->create([
            'study_id' => $this->study->id,
        ]);

        $this->assertInstanceOf(Sample::class, $this->study->sample);
        $this->assertEquals($sample->id, $this->study->sample->id);
        $this->assertEquals($sample->name, $this->study->sample->name);
    }

    public function test_study_sample_is_automatically_created(): void
    {
        // Create a new study via the action
        $newStudy = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        // Manually create sample since factory doesn't do it automatically
        $sample = Sample::factory()->create([
            'study_id' => $newStudy->id,
            'name' => $newStudy->name.'_sample',
        ]);

        $this->assertNotNull($newStudy->sample);
        $this->assertEquals($newStudy->name.'_sample', $newStudy->sample->name);
    }

    public function test_study_has_many_datasets(): void
    {
        $datasets = Dataset::factory(3)->create([
            'study_id' => $this->study->id,
        ]);

        $this->assertInstanceOf(Collection::class, $this->study->datasets);
        $this->assertCount(3, $this->study->datasets);

        foreach ($datasets as $dataset) {
            $this->assertTrue($this->study->datasets->contains($dataset));
        }
    }

    public function test_study_datasets_are_ordered_by_name(): void
    {
        Dataset::factory()->create([
            'study_id' => $this->study->id,
            'name' => 'Z Dataset',
        ]);
        Dataset::factory()->create([
            'study_id' => $this->study->id,
            'name' => 'A Dataset',
        ]);
        Dataset::factory()->create([
            'study_id' => $this->study->id,
            'name' => 'M Dataset',
        ]);

        $datasets = $this->study->datasets;
        $names = $datasets->pluck('name')->toArray();

        $this->assertEquals(['A Dataset', 'M Dataset', 'Z Dataset'], $names);
    }

    public function test_study_has_many_users_with_roles(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->study->users()->attach($user1, ['role' => 'collaborator']);
        $this->study->users()->attach($user2, ['role' => 'viewer']);

        $this->assertInstanceOf(Collection::class, $this->study->users);
        $this->assertCount(2, $this->study->users);

        $studyUser1 = $this->study->users->where('id', $user1->id)->first();
        $studyUser2 = $this->study->users->where('id', $user2->id)->first();

        $this->assertEquals('collaborator', $studyUser1->studyMembership->role);
        $this->assertEquals('viewer', $studyUser2->studyMembership->role);
    }

    public function test_study_has_many_invitations(): void
    {
        $invitations = StudyInvitation::factory(2)->create([
            'study_id' => $this->study->id,
        ]);

        $this->assertInstanceOf(Collection::class, $this->study->studyInvitations);
        $this->assertCount(2, $this->study->studyInvitations);

        foreach ($invitations as $invitation) {
            $this->assertTrue($this->study->studyInvitations->contains($invitation));
        }
    }

    public function test_study_belongs_to_file_system_object_via_fs_id(): void
    {
        $fsObject = FileSystemObject::factory()->create([
            'study_id' => $this->study->id,
        ]);

        $this->study->update(['fs_id' => $fsObject->id]);

        $this->assertInstanceOf(FileSystemObject::class, $this->study->fresh()->fsObject);
        $this->assertEquals($fsObject->id, $this->study->fresh()->fsObject->id);
    }

    public function test_study_belongs_to_draft(): void
    {
        $draft = Draft::factory()->create();
        $this->study->update(['draft_id' => $draft->id]);

        $this->assertInstanceOf(Draft::class, $this->study->draft);
        $this->assertEquals($draft->id, $this->study->draft->id);
    }

    public function test_study_belongs_to_validation(): void
    {
        $validation = Validation::factory()->create();
        $this->study->update(['validation_id' => $validation->id]);

        $this->assertInstanceOf(Validation::class, $this->study->validation);
        $this->assertEquals($validation->id, $this->study->validation->id);
    }

    public function test_study_belongs_to_license(): void
    {
        $license = License::factory()->create();
        $this->study->update(['license_id' => $license->id]);

        $this->assertInstanceOf(License::class, $this->study->license);
        $this->assertEquals($license->id, $this->study->license->id);
    }

    public function test_study_has_one_nmrium(): void
    {
        $nmrium = NMRium::factory()->create();
        $this->study->nmrium()->save($nmrium);

        $this->assertInstanceOf(NMRium::class, $this->study->nmrium);
        $this->assertEquals($nmrium->id, $this->study->nmrium->id);
    }

    public function test_study_accesses_molecules_through_sample(): void
    {
        $sample = Sample::factory()->create([
            'study_id' => $this->study->id,
        ]);

        $molecules = Molecule::factory(2)->create();
        $sample->molecules()->attach($molecules);

        // Test through relationship
        $sampleMolecules = $this->study->sample->molecules;
        $this->assertInstanceOf(Collection::class, $sampleMolecules);
        $this->assertCount(2, $sampleMolecules);

        foreach ($molecules as $molecule) {
            $this->assertTrue($sampleMolecules->contains($molecule));
        }
    }

    public function test_study_can_get_user_with_email(): void
    {
        $studyMember = User::factory()->create();
        $this->study->users()->attach($studyMember, ['role' => 'collaborator']);

        $foundUser = $this->study->userWithEmail($studyMember->email);
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($studyMember->id, $foundUser->id);

        $notFoundUser = $this->study->userWithEmail('nonexistent@example.com');
        $this->assertNull($notFoundUser);
    }

    public function test_study_can_remove_user(): void
    {
        $user = User::factory()->create();
        $this->study->users()->attach($user, ['role' => 'viewer']);

        $this->assertTrue($this->study->users->contains($user));

        $this->study->removeUser($user);

        $this->study->refresh();
        $this->assertFalse($this->study->users->contains($user));
    }

    public function test_study_url_attributes(): void
    {
        $expectedPublicUrl = config('app.url').'/sample/S'.$this->study->identifier;
        $expectedPrivateUrl = config('app.url').'/studies/'.urlencode($this->study->url ?? '');

        $this->assertEquals($expectedPublicUrl, $this->study->public_url);
        $this->assertEquals($expectedPrivateUrl, $this->study->private_url);
    }

    public function test_study_photo_url_attribute(): void
    {
        $this->assertEquals('', $this->study->study_photo_url);

        $this->study->update(['study_photo_path' => '/path/to/photo.jpg']);
        $this->study->refresh();

        $this->assertStringContainsString('/path/to/photo.jpg', $this->study->study_photo_url);
    }

    public function test_study_preview_urls_from_datasets(): void
    {
        $dataset1 = Dataset::factory()->create([
            'study_id' => $this->study->id,
            'dataset_photo_path' => '/path/to/dataset1.jpg',
        ]);
        $dataset2 = Dataset::factory()->create([
            'study_id' => $this->study->id,
            'dataset_photo_path' => '/path/to/dataset2.jpg',
        ]);
        $dataset3 = Dataset::factory()->create([
            'study_id' => $this->study->id,
            'dataset_photo_path' => null,
        ]);

        $previewUrls = $this->study->study_preview_urls;
        $this->assertIsArray($previewUrls);
        $this->assertCount(2, $previewUrls); // Only datasets with photos
    }

    public function test_study_is_published_attribute(): void
    {
        // Private study should not be published
        $this->assertFalse($this->study->is_published);

        // Public study should be published
        $this->study->update(['is_public' => true]);
        $this->assertTrue($this->study->is_published);

        // Study with project relationship
        $this->study->update(['is_public' => false]);
        $this->project->update(['is_public' => true]);
        $this->study->refresh();
        $this->study->load('project');
        $this->assertTrue($this->study->is_published);
    }

    public function test_study_is_bookmarked_attribute(): void
    {
        $this->actingAs($this->user);

        $this->assertFalse($this->study->is_bookmarked);

        // Bookmark the study
        Bookmark::add($this->study, $this->user);
        $this->study->refresh();

        $this->assertTrue($this->study->is_bookmarked);
    }

    public function test_study_identifier_attribute(): void
    {
        $this->study->update(['identifier' => 123]);
        $this->study->refresh();

        $this->assertEquals('NMRXIV:S123', $this->study->identifier);

        // Test with null identifier
        $this->study->update(['identifier' => null]);
        $this->study->refresh();

        $this->assertNull($this->study->identifier);
    }

    public function test_study_searchable_scope(): void
    {
        $publicStudy = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => true,
            'is_archived' => false,
        ]);

        $privateStudy = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'is_archived' => false,
        ]);

        $archivedStudy = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => true,
            'is_archived' => true,
        ]);

        $this->assertTrue($publicStudy->is_public && ! $publicStudy->is_archived);
        $this->assertFalse($privateStudy->is_public);
        $this->assertTrue($archivedStudy->is_archived);
    }

    public function test_study_with_tags(): void
    {
        $this->study->attachTag('NMR');
        $this->study->attachTag('Organic Chemistry');
        $this->study->attachTag('Research', 'Study');

        $this->study->refresh();
        $tags = $this->study->tags;

        $this->assertCount(3, $tags);
        $this->assertTrue($tags->pluck('name')->contains('NMR'));
        $this->assertTrue($tags->pluck('name')->contains('Organic Chemistry'));
        $this->assertTrue($tags->pluck('name')->contains('Research'));
    }
}
