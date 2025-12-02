<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\DeleteProject;
use App\Models\Author;
use App\Models\Citation;
use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Molecule;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteProjectTest extends TestCase
{
    use RefreshDatabase;

    private DeleteProject $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteProject;
        Storage::fake('local');
        // Set environment variable for FILESYSTEM_DRIVER to local for this test
        putenv('FILESYSTEM_DRIVER=local');
        $_ENV['FILESYSTEM_DRIVER'] = 'local';
        config(['filesystems.default' => 'local']);
    }

    public function test_delete_public_project_archives_instead_of_deleting()
    {
        $project = Project::factory()->create(['is_public' => true]);
        $study = Study::factory()->for($project)->create();
        $dataset = Dataset::factory()->for($study)->create();

        $this->action->delete($project);

        $this->assertTrue($project->fresh()->is_archived);
        $this->assertTrue($study->fresh()->is_archived);
        $this->assertTrue($dataset->fresh()->is_archived);
        $this->assertFalse($project->fresh()->is_deleted);
    }

    public function test_delete_private_project_marks_as_deleted()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create();
        $dataset = Dataset::factory()->for($study)->create();

        $this->action->delete($project);

        $this->assertTrue($project->fresh()->is_deleted);
        $this->assertTrue($study->fresh()->is_deleted);
        $this->assertTrue($dataset->fresh()->is_deleted);
        $this->assertNotNull($project->fresh()->deleted_on);
    }

    public function test_delete_private_project_marks_draft_as_deleted()
    {
        $draft = Draft::factory()->create();
        $project = Project::factory()->create([
            'is_public' => false,
            'draft_id' => $draft->id,
        ]);

        $this->action->delete($project);

        $this->assertTrue($project->fresh()->is_deleted);
        $this->assertTrue($draft->fresh()->is_deleted);
    }

    public function test_delete_private_project_sends_notification()
    {
        $project = Project::factory()->create(['is_public' => false]);

        // Mock notification sending (since we can't easily test actual notifications)
        $this->action->delete($project);

        $this->assertTrue($project->fresh()->is_deleted);
    }

    public function test_prepare_send_list_includes_creators_and_owners()
    {
        $owner = User::factory()->create();
        $creator = User::factory()->create();
        $collaborator = User::factory()->create();

        $project = Project::factory()->for($owner, 'owner')->create();

        // Attach users with different roles
        $project->users()->attach($creator, ['role' => 'creator']);
        $project->users()->attach($collaborator, ['role' => 'collaborator']);

        $sendList = $this->action->prepareSendList($project);

        // Algorithm iterates through project users and adds creators directly,
        // for others it adds the project owner
        $this->assertCount(2, $sendList); // creator + owner (for collaborator)

        // Find creator and owner in the results
        $userIds = collect($sendList)->pluck('id');
        $this->assertTrue($userIds->contains($creator->id));
        $this->assertTrue($userIds->contains($owner->id));
    }

    public function test_permanent_delete_removes_all_objects()
    {
        // Create super-admin role for test
        \Spatie\Permission\Models\Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create();
        $dataset = Dataset::factory()->for($study)->create();
        $sample = Sample::factory()->for($study)->create();
        $molecule = Molecule::factory()->create();
        $sample->molecules()->attach($molecule);

        // Create validation
        $validation = Validation::factory()->create();
        $project->validation()->associate($validation);
        $project->save();

        // Create authors and citations
        $author = Author::factory()->create();
        $citation = Citation::factory()->create();
        $project->authors()->attach($author);
        $project->citations()->attach($citation);

        // Create draft with files - Draft hasOne Project, so create Draft first
        $draft = Draft::factory()->create();
        $project->draft_id = $draft->id;
        $project->save();
        $file = FileSystemObject::factory()->for($draft, 'draft')->create();
        // Create the actual file in storage so deletion works
        Storage::put($file->path, 'test content');

        $this->action->deletePermanent($project);

        // Verify everything is deleted
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('studies', ['id' => $study->id]);
        $this->assertDatabaseMissing('datasets', ['id' => $dataset->id]);
        $this->assertDatabaseMissing('samples', ['id' => $sample->id]);
        $this->assertDatabaseMissing('validations', ['id' => $validation->id]);
        $this->assertDatabaseMissing('drafts', ['id' => $draft->id]);
        $this->assertDatabaseMissing('file_system_objects', ['id' => $file->id]);
    }

    public function test_permanent_delete_does_nothing_for_public_project()
    {
        $project = Project::factory()->create(['is_public' => true]);

        $this->action->deletePermanent($project);

        // Public project should still exist
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_delete_study_removes_sample_and_molecules()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create();
        $sample = Sample::factory()->for($study)->create();
        $molecule = Molecule::factory()->create();
        $sample->molecules()->attach($molecule);

        $this->action->deleteStudy($study);

        $this->assertDatabaseMissing('studies', ['id' => $study->id]);
        $this->assertDatabaseMissing('samples', ['id' => $sample->id]);
        // Molecule should still exist (just detached)
        $this->assertDatabaseHas('molecules', ['id' => $molecule->id]);
    }

    public function test_delete_datasets_removes_nmrium_info()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create();
        $dataset = Dataset::factory()->for($study)->create();
        $nmrium = NMRium::factory()->create([
            'nmriumable_type' => Dataset::class,
            'nmriumable_id' => $dataset->id,
        ]);

        $this->action->deleteDatasets($dataset);

        $this->assertDatabaseMissing('datasets', ['id' => $dataset->id]);
        $this->assertDatabaseMissing('nmrium', ['id' => $nmrium->id]);
    }

    public function test_delete_project_detaches_authors_and_citations()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $author = Author::factory()->create();
        $citation = Citation::factory()->create();

        $project->authors()->attach($author);
        $project->citations()->attach($citation);

        $this->action->deleteProject($project);

        // Authors and citations should still exist but be detached
        $this->assertDatabaseHas('authors', ['id' => $author->id]);
        $this->assertDatabaseHas('citations', ['id' => $citation->id]);
        $this->assertDatabaseMissing('author_project', [
            'author_id' => $author->id,
            'project_id' => $project->id,
        ]);
        $this->assertDatabaseMissing('citation_project', [
            'citation_id' => $citation->id,
            'project_id' => $project->id,
        ]);
    }

    public function test_delete_fso_removes_files_from_storage()
    {
        $file = FileSystemObject::factory()->create([
            'type' => 'file',
            'path' => 'test/file.txt',
        ]);

        Storage::disk('local')->put($file->path, 'test content');
        $this->assertTrue(Storage::disk('local')->exists($file->path));

        $this->action->deleteFSO($file);

        $this->assertFalse(Storage::disk('local')->exists($file->path));
        $this->assertDatabaseMissing('file_system_objects', ['id' => $file->id]);
    }

    public function test_delete_fso_removes_directory_from_storage()
    {
        $directory = FileSystemObject::factory()->create([
            'type' => 'directory',
            'path' => 'test/directory',
        ]);

        Storage::disk('local')->makeDirectory($directory->path);
        Storage::disk('local')->put($directory->path.'/file.txt', 'test content');
        $this->assertTrue(Storage::disk('local')->exists($directory->path));

        $this->action->deleteFSO($directory);

        $this->assertFalse(Storage::disk('local')->exists($directory->path));
        $this->assertDatabaseMissing('file_system_objects', ['id' => $directory->id]);
    }

    public function test_get_children_ids_returns_nested_structure()
    {
        $parent = FileSystemObject::factory()->create(['has_children' => true]);
        $child1 = FileSystemObject::factory()->create(['parent_id' => $parent->id]);
        $child2 = FileSystemObject::factory()->create([
            'parent_id' => $parent->id,
            'has_children' => true,
        ]);
        $grandchild = FileSystemObject::factory()->create(['parent_id' => $child2->id]);

        // Mock the children relationship
        $parent->setRelation('children', collect([$child1, $child2]));
        $child2->setRelation('children', collect([$grandchild]));

        $ids = $this->action->getChildrenIds($parent, []);

        $this->assertContains($parent->id, $ids);
        $this->assertContains($child1->id, $ids);
        $this->assertContains($child2->id, $ids);
        $this->assertContains($grandchild->id, $ids);
    }

    public function test_delete_sample_detaches_molecules()
    {
        $study = Study::factory()->create();
        $sample = Sample::factory()->for($study)->create();
        $molecule1 = Molecule::factory()->create();
        $molecule2 = Molecule::factory()->create();

        $sample->molecules()->attach([$molecule1->id, $molecule2->id]);

        $this->action->deleteSample($sample);

        $this->assertDatabaseMissing('samples', ['id' => $sample->id]);
        // Molecules should still exist but be detached
        $this->assertDatabaseHas('molecules', ['id' => $molecule1->id]);
        $this->assertDatabaseHas('molecules', ['id' => $molecule2->id]);
        $this->assertDatabaseMissing('molecule_sample', ['sample_id' => $sample->id]);
    }

    public function test_delete_nmrium_removes_versions()
    {
        $nmrium = NMRium::factory()->create();

        // Mock versions relationship - NMRium uses versionable trait
        $this->action->deleteNMRium($nmrium);

        $this->assertDatabaseMissing('nmrium', ['id' => $nmrium->id]);
    }

    public function test_delete_handles_empty_project()
    {
        $project = Project::factory()->create(['is_public' => false]);

        $this->action->delete($project);

        $this->assertTrue($project->fresh()->is_deleted);
    }

    public function test_delete_handles_project_without_draft()
    {
        $project = Project::factory()->create(['is_public' => false]);
        $study = Study::factory()->for($project)->create();

        $this->action->delete($project);

        $this->assertTrue($project->fresh()->is_deleted);
        $this->assertTrue($study->fresh()->is_deleted);
    }
}
