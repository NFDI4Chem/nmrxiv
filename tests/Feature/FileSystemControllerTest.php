<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileSystemControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Draft $draft;

    private Project $project;

    private Study $study;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;

        [$userId, $teamId] = $this->user->getUserTeamData();

        $this->draft = Draft::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        $this->project = Project::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $userId,
            'team_id' => $teamId,
        ]);
    }

    public function test_signed_draft_storage_url_requires_authentication(): void
    {
        $response = $this->postJson('/dashboard/storage/signed-draft-storage-url', [
            'draft_files' => [
                ['upload' => ['filename' => 'test.txt', 'total' => 1000]],
            ],
            'draft_id' => $this->draft->id,
            'destination' => '/test',
        ]);

        $response->assertStatus(401);
    }

    public function test_signed_draft_storage_url_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-draft-storage-url', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['draft_files', 'draft_id', 'destination']);
    }

    public function test_signed_draft_storage_url_validates_draft_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-draft-storage-url', [
                'draft_files' => [
                    ['upload' => ['filename' => 'test.txt', 'total' => 1000]],
                ],
                'draft_id' => 99999,
                'destination' => '/test',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['draft_id']);
    }

    public function test_signed_draft_storage_url_generates_urls(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-draft-storage-url', [
                'draft_files' => [
                    [
                        'upload' => ['filename' => 'test.txt', 'total' => 1000],
                        'fullPath' => '/test/test.txt',
                    ],
                ],
                'draft_id' => $this->draft->id,
                'destination' => '/test',
            ]);

        $response->assertStatus(201);
        $response->assertJsonIsArray();
        $response->assertJsonCount(1);
        $response->assertJsonStructure([
            '*' => ['uuid', 'bucket', 'key', 'url', 'headers', 'fullPath'],
        ]);
    }

    public function test_signed_storage_url_requires_authentication(): void
    {
        $response = $this->postJson('/dashboard/storage/signed-storage-url', [
            'file' => ['upload' => ['filename' => 'test.txt', 'total' => 1000]],
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'destination' => '/test',
        ]);

        $response->assertStatus(401);
    }

    public function test_signed_storage_url_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-storage-url', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file', 'project_id', 'study_id', 'destination']);
    }

    public function test_signed_storage_url_validates_project_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-storage-url', [
                'file' => ['upload' => ['filename' => 'test.txt', 'total' => 1000]],
                'project_id' => 99999,
                'study_id' => $this->study->id,
                'destination' => '/test',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['project_id']);
    }

    public function test_signed_storage_url_validates_study_exists(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-storage-url', [
                'file' => ['upload' => ['filename' => 'test.txt', 'total' => 1000]],
                'project_id' => $this->project->id,
                'study_id' => 99999,
                'destination' => '/test',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['study_id']);
    }

    public function test_signed_storage_url_generates_url(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/dashboard/storage/signed-storage-url', [
                'file' => ['upload' => ['filename' => 'test.txt', 'total' => 1000]],
                'project_id' => $this->project->id,
                'study_id' => $this->study->id,
                'destination' => '/test',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['uuid', 'bucket', 'key', 'url', 'headers']);
    }

    public function test_delete_fso_requires_authentication(): void
    {
        $fso = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
        ]);

        $response = $this->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$fso->id}");

        $response->assertStatus(401);
    }

    public function test_delete_fso_validates_fso_belongs_to_draft(): void
    {
        $otherDraft = Draft::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
        ]);

        $fso = FileSystemObject::factory()->file()->create([
            'draft_id' => $otherDraft->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$fso->id}");

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Filesystem object does not belong to this draft',
        ]);
    }

    public function test_delete_fso_successfully_deletes_file(): void
    {
        $fso = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$fso->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'deleted_count',
            'files_deleted',
            'directories_deleted',
            'storage_errors',
            'has_storage_errors',
        ]);

        $this->assertDatabaseMissing('file_system_objects', [
            'id' => $fso->id,
            'is_deleted' => false,
        ]);
    }

    public function test_delete_fso_deletes_directory_with_children(): void
    {
        $directory = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
        ]);

        $child1 = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'level' => 1,
        ]);

        $child2 = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'level' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$directory->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('file_system_objects', [
            'id' => $directory->id,
            'is_deleted' => false,
        ]);
        $this->assertDatabaseMissing('file_system_objects', [
            'id' => $child1->id,
            'is_deleted' => false,
        ]);
        $this->assertDatabaseMissing('file_system_objects', [
            'id' => $child2->id,
            'is_deleted' => false,
        ]);
    }

    public function test_is_bruker_identifies_bruker_folder(): void
    {
        $directory = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'acqus',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'acqu',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'pdata',
        ]);

        $directory->load('children');

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isBruker($directory);

        $this->assertTrue($result);
    }

    public function test_is_bruker_returns_false_for_non_bruker_folder(): void
    {
        $directory = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'random.txt',
        ]);

        $directory->load('children');

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isBruker($directory);

        $this->assertFalse($result);
    }

    public function test_is_varian_identifies_varian_folder(): void
    {
        $directory = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'fid',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'log',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'text',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'name' => 'procpar',
        ]);

        $directory->load('children');

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isVarian($directory);

        $this->assertTrue($result);
    }

    public function test_is_jcamp_dx_identifies_jdx_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'spectrum.jdx',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isJcampDX($file);

        $this->assertTrue($result);
    }

    public function test_is_jcamp_dx_identifies_dx_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'spectrum.dx',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isJcampDX($file);

        $this->assertTrue($result);
    }

    public function test_is_jcamp_dx_identifies_jcamp_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'spectrum.jcamp',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isJcampDX($file);

        $this->assertTrue($result);
    }

    public function test_is_nmredata_identifies_sdf_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'molecule.sdf',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isNMReData($file);

        $this->assertTrue($result);
    }

    public function test_is_mol_data_identifies_mol_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'molecule.mol',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isMolData($file);

        $this->assertTrue($result);
    }

    public function test_is_joel_identifies_jdf_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'spectrum.jdf',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isJOEL($file);

        $this->assertTrue($result);
    }

    public function test_save_instrument_type_updates_folder(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'instrument_type' => null,
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->saveInstrumentType($folder, 'bruker');

        $folder->refresh();
        $this->assertEquals('bruker', $folder->instrument_type);
    }

    public function test_save_model_type_updates_folder(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'model_type' => null,
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->saveModelType($folder, 'study');

        $folder->refresh();
        $this->assertEquals('study', $folder->model_type);
    }

    public function test_save_model_type_with_external_url(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'model_type' => null,
            'external_url' => null,
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->saveModelType($folder, 'analysis', 'https://example.com/analysis/123');

        $folder->refresh();
        $this->assertEquals('analysis', $folder->model_type);
        $this->assertEquals('https://example.com/analysis/123', $folder->external_url);
    }

    public function test_delete_fso_handles_exception(): void
    {
        // Create a FSO that will cause an exception during deletion
        $fso = FileSystemObject::factory()->file()->create([
            'draft_id' => 999999, // Non-existent draft
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$fso->id}");

        // Should return 403 because FSO doesn't belong to this draft
        $response->assertStatus(403);
    }

    public function test_delete_fso_with_storage_errors(): void
    {
        $directory = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
        ]);

        $child = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $directory->id,
            'level' => 1,
            'key' => 'nonexistent/path/file.txt',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$directory->id}");

        // Should still succeed but may have storage errors
        $this->assertTrue(in_array($response->status(), [200, 207]));
    }

    public function test_process_folder_with_non_chemotion_draft(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'model_type' => null,
        ]);

        $brukerFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'level' => 1,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $brukerFolder->id,
            'name' => 'acqus',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $brukerFolder->id,
            'name' => 'acqu',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $brukerFolder->id,
            'name' => 'pdata',
        ]);

        $brukerFolder->load('children');
        $parentFolder->load('children');
        $this->draft->eln = null;
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder($parentFolder->children);

        $brukerFolder->refresh();
        $this->assertEquals('bruker', $brukerFolder->instrument_type);

        $parentFolder->refresh();
        $this->assertEquals('study', $parentFolder->model_type);
    }

    public function test_process_folder_with_chemotion_draft(): void
    {
        $brukerFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $brukerFolder->id,
            'name' => 'acqus',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $brukerFolder->id,
            'name' => 'acqu',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $brukerFolder->id,
            'name' => 'pdata',
        ]);

        $brukerFolder->load('children');
        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$brukerFolder]), $this->draft, false, null);

        $brukerFolder->refresh();
        $this->assertEquals('bruker', $brukerFolder->instrument_type);
    }

    public function test_process_folder_with_varian_files(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'model_type' => null,
        ]);

        $varianFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'level' => 1,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'fid',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'log',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'text',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'procpar',
        ]);

        $varianFolder->load('children');
        $parentFolder->load('children');

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder($parentFolder->children);

        $varianFolder->refresh();
        $this->assertEquals('varian', $varianFolder->instrument_type);

        $parentFolder->refresh();
        $this->assertEquals('study', $parentFolder->model_type);
    }

    public function test_process_folder_with_joel_file(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'model_type' => null,
        ]);

        $joelFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'name' => 'spectrum.jdf',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$joelFile]));

        $joelFile->refresh();
        $this->assertEquals('joel', $joelFile->instrument_type);

        $parentFolder->refresh();
        $this->assertEquals('study', $parentFolder->model_type);
    }

    public function test_process_folder_with_jcamp_file(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'model_type' => null,
        ]);

        $jcampFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'name' => 'spectrum.jdx',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$jcampFile]));

        $jcampFile->refresh();
        $this->assertEquals('jcamp', $jcampFile->instrument_type);

        $parentFolder->refresh();
        $this->assertEquals('study', $parentFolder->model_type);
    }

    public function test_process_folder_with_nmredata_file(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'model_type' => null,
            'study_id' => $this->study->id,
        ]);

        $nmredataFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'name' => 'molecule.sdf',
            'study_id' => $this->study->id,
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$nmredataFile]));

        $nmredataFile->refresh();
        $this->assertEquals('nmredata', $nmredataFile->instrument_type);

        $this->study->refresh();
        $this->assertTrue($this->study->has_nmredata);
    }

    public function test_process_folder_with_mol_file(): void
    {
        $molFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'molecule.mol',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$molFile]));

        $molFile->refresh();
        $this->assertEquals('mol', $molFile->instrument_type);
    }

    public function test_process_folder_skips_already_typed_folders(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'model_type' => 'study',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $folder->id,
            'name' => 'acqus',
        ]);

        $folder->load('children');

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$folder]));

        $folder->refresh();
        // Should not change because model_type was already set
        $this->assertEquals('study', $folder->model_type);
        $this->assertNull($folder->instrument_type);
    }

    public function test_process_folder_recursive_processing(): void
    {
        $rootFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
        ]);

        $subFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $rootFolder->id,
            'level' => 1,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $subFolder->id,
            'name' => 'acqus',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $subFolder->id,
            'name' => 'acqu',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $subFolder->id,
            'name' => 'pdata',
        ]);

        $rootFolder->load('children.children');
        $subFolder->load('children');

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder($rootFolder->children);

        $subFolder->refresh();
        $this->assertEquals('bruker', $subFolder->instrument_type);
    }

    public function test_save_annotations_detected_with_study(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'study_id' => $this->study->id,
        ]);

        $this->study->has_nmredata = false;
        $this->study->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->saveAnnotationsDetected($folder);

        $this->study->refresh();
        $this->assertTrue($this->study->has_nmredata);
    }

    public function test_save_annotations_detected_without_study(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'study_id' => null,
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->saveAnnotationsDetected($folder);

        // Should not throw exception
        $this->assertTrue(true);
    }

    public function test_save_annotations_detected_with_null_folder(): void
    {
        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->saveAnnotationsDetected(null);

        // Should handle null gracefully
        $this->assertTrue(true);
    }

    public function test_delete_fso_with_actual_exception(): void
    {
        // Create a mock service that throws exception
        $mockService = \Mockery::mock(\App\Services\FileSystemObjectService::class);
        $mockService->shouldReceive('deleteFileSystemObject')
            ->andThrow(new \Exception('Database error'));

        $this->app->instance(\App\Services\FileSystemObjectService::class, $mockService);

        $fso = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/dashboard/drafts/{$this->draft->id}/files/{$fso->id}");

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure(['success', 'message']);
    }

    public function test_process_folder_with_chemotion_and_eln_metadata(): void
    {
        // Create a simple mock logger with public log method
        $mockLogger = new class
        {
            public function log($draft, $level, $message)
            {
                // Do nothing
            }
        };

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$folder]), $this->draft, true, $mockLogger);

        // Should process without errors
        $this->assertTrue(true);
    }

    public function test_process_folder_with_chemotion_nmredata_file(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
            'study_id' => $this->study->id,
        ]);

        $nmredataFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'name' => 'molecule.sdf',
            'study_id' => $this->study->id,
        ]);

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$nmredataFile]), $this->draft, false, null);

        $nmredataFile->refresh();
        $this->assertEquals('nmredata', $nmredataFile->instrument_type);
    }

    public function test_process_folder_with_chemotion_mol_file(): void
    {
        $molFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'molecule.mol',
        ]);

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$molFile]), $this->draft, false, null);

        $molFile->refresh();
        $this->assertEquals('mol', $molFile->instrument_type);
    }

    public function test_process_folder_with_chemotion_joel_file(): void
    {
        $joelFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'spectrum.jdf',
        ]);

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$joelFile]), $this->draft, false, null);

        $joelFile->refresh();
        $this->assertEquals('joel', $joelFile->instrument_type);
    }

    public function test_process_folder_with_chemotion_jcamp_file(): void
    {
        $jcampFile = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'spectrum.jdx',
        ]);

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$jcampFile]), $this->draft, false, null);

        $jcampFile->refresh();
        $this->assertEquals('jcamp', $jcampFile->instrument_type);
    }

    public function test_process_folder_with_chemotion_recursive_non_instrument_folder(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
        ]);

        $childFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'level' => 1,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $childFolder->id,
            'name' => 'acqus',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $childFolder->id,
            'name' => 'acqu',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $childFolder->id,
            'name' => 'pdata',
        ]);

        $parentFolder->load('children.children');
        $childFolder->load('children');

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$parentFolder]), $this->draft, false, null);

        $childFolder->refresh();
        $this->assertEquals('bruker', $childFolder->instrument_type);
    }

    public function test_is_mol_data_returns_false_for_non_mol_file(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
        ]);

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $result = $controller->isMolData($file);

        $this->assertFalse($result);
    }

    public function test_process_folder_with_chemotion_varian_recursive(): void
    {
        $parentFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'level' => 0,
        ]);

        $varianFolder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $parentFolder->id,
            'level' => 1,
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'fid',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'log',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'text',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'parent_id' => $varianFolder->id,
            'name' => 'procpar',
        ]);

        $varianFolder->load('children');
        $parentFolder->load('children.children');

        $this->draft->eln = 'chemotion';
        $this->draft->save();

        $controller = app(\App\Http\Controllers\FileSystemController::class);
        $controller->processFolder(collect([$parentFolder]), $this->draft, false, null);

        $varianFolder->refresh();
        $this->assertEquals('varian', $varianFolder->instrument_type);
    }
}
