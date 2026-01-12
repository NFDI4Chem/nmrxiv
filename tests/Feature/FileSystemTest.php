<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileSystemTest extends TestCase
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

    // FileIntegrityService Tests

    public function test_store_checksums_updates_file_with_md5(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $checksums = ['md5' => 'abc123def456'];
        $fileSize = 1024;

        $service->storeChecksums($file, $checksums, $fileSize);

        $file->refresh();
        $this->assertEquals('abc123def456', $file->checksum_md5);
        $this->assertEquals('md5', $file->checksum_algorithm);
        $this->assertEquals(1024, $file->file_size);
        $this->assertEquals('pending', $file->integrity_status);
    }

    public function test_store_checksums_updates_file_with_sha256(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $checksums = ['sha256' => 'abc123def456789'];
        $fileSize = 2048;

        $service->storeChecksums($file, $checksums, $fileSize);

        $file->refresh();
        $this->assertEquals('abc123def456789', $file->checksum_sha256);
        $this->assertEquals('sha256', $file->checksum_algorithm);
        $this->assertEquals(2048, $file->file_size);
        $this->assertEquals('pending', $file->integrity_status);
    }

    public function test_store_checksums_prefers_sha256_over_md5(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $checksums = [
            'md5' => 'md5hash',
            'sha256' => 'sha256hash',
        ];

        $service->storeChecksums($file, $checksums, 1024);

        $file->refresh();
        $this->assertEquals('md5hash', $file->checksum_md5);
        $this->assertEquals('sha256hash', $file->checksum_sha256);
        $this->assertEquals('sha256', $file->checksum_algorithm);
    }

    public function test_store_checksums_ignores_directories(): void
    {
        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'name' => 'folder',
            'type' => 'directory',
        ]);

        $originalChecksum = $folder->checksum_md5;
        $originalAlgorithm = $folder->checksum_algorithm;

        $service = app(\App\Services\FileIntegrityService::class);
        $checksums = ['md5' => 'abc123'];

        $service->storeChecksums($folder, $checksums, 1024);

        $folder->refresh();
        // Should not update directories
        $this->assertEquals($originalChecksum, $folder->checksum_md5);
        $this->assertEquals($originalAlgorithm, $folder->checksum_algorithm);
    }

    public function test_verify_file_integrity_succeeds_with_matching_checksum(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $fileContent = 'test file content';
        $sha256 = hash('sha256', $fileContent);

        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
            'path' => '/drafts/test.txt',
            'checksum_sha256' => $sha256,
            'checksum_algorithm' => 'sha256',
            'file_size' => strlen($fileContent),
            'integrity_status' => 'pending',
        ]);

        \Illuminate\Support\Facades\Storage::disk('local')->put('drafts/test.txt', $fileContent);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->verifyFileIntegrity($file);

        $this->assertTrue($result);
        $file->refresh();
        $this->assertEquals('verified', $file->integrity_status);
        $this->assertNotNull($file->integrity_verified_at);
        $this->assertNull($file->integrity_error);
    }

    public function test_verify_file_integrity_fails_with_mismatched_checksum(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $fileContent = 'test file content';
        $wrongChecksum = 'wrongchecksumvalue';

        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
            'path' => '/drafts/test.txt',
            'checksum_sha256' => $wrongChecksum,
            'checksum_algorithm' => 'sha256',
            'file_size' => strlen($fileContent),
            'integrity_status' => 'pending',
        ]);

        \Illuminate\Support\Facades\Storage::disk('local')->put('drafts/test.txt', $fileContent);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->verifyFileIntegrity($file);

        $this->assertFalse($result);
        $file->refresh();
        $this->assertEquals('failed', $file->integrity_status);
        $this->assertNotNull($file->integrity_error);
        $this->assertStringContainsString('Checksum mismatch', $file->integrity_error);
    }

    public function test_verify_file_integrity_fails_with_size_mismatch(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $fileContent = 'test file content';
        $sha256 = hash('sha256', $fileContent);

        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
            'path' => '/drafts/test.txt',
            'checksum_sha256' => $sha256,
            'checksum_algorithm' => 'sha256',
            'file_size' => 999999, // Wrong size
            'integrity_status' => 'pending',
        ]);

        \Illuminate\Support\Facades\Storage::disk('local')->put('drafts/test.txt', $fileContent);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->verifyFileIntegrity($file);

        $this->assertFalse($result);
        $file->refresh();
        $this->assertEquals('failed', $file->integrity_status);
        $this->assertStringContainsString('File size mismatch', $file->integrity_error);
    }

    public function test_verify_file_integrity_fails_when_file_not_in_storage(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'missing.txt',
            'path' => '/drafts/missing.txt',
            'checksum_sha256' => 'somechecksum',
            'checksum_algorithm' => 'sha256',
            'integrity_status' => 'pending',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->verifyFileIntegrity($file);

        $this->assertFalse($result);
        $file->refresh();
        $this->assertEquals('failed', $file->integrity_status);
        $this->assertStringContainsString('File not found in storage', $file->integrity_error);
    }

    public function test_verify_file_integrity_fails_when_no_checksum(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
            'path' => '/drafts/test.txt',
            'checksum_sha256' => null,
            'checksum_md5' => null,
            'integrity_status' => 'pending',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->verifyFileIntegrity($file);

        $this->assertFalse($result);
        $file->refresh();
        $this->assertEquals('failed', $file->integrity_status);
        $this->assertStringContainsString('No checksum available', $file->integrity_error);
    }

    public function test_verify_file_integrity_throws_exception_for_directory(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Can only verify integrity of files, not directories');

        $folder = FileSystemObject::factory()->directory()->create([
            'draft_id' => $this->draft->id,
            'name' => 'folder',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $service->verifyFileIntegrity($folder);
    }

    public function test_download_file_from_storage_returns_content(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $fileContent = 'test file content';
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
            'path' => '/drafts/test.txt',
        ]);

        \Illuminate\Support\Facades\Storage::disk('local')->put('drafts/test.txt', $fileContent);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->downloadFileFromStorage($file);

        $this->assertEquals($fileContent, $result);
    }

    public function test_download_file_from_storage_returns_null_when_missing(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'missing.txt',
            'path' => '/drafts/missing.txt',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $result = $service->downloadFileFromStorage($file);

        $this->assertNull($result);
    }

    public function test_get_files_pending_verification(): void
    {
        // Clear any existing files
        FileSystemObject::where('draft_id', $this->draft->id)->delete();

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'checksum_sha256' => 'hash1',
            'integrity_status' => 'pending',
            'type' => 'file',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'checksum_md5' => 'hash2',
            'integrity_status' => 'pending',
            'type' => 'file',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'checksum_sha256' => 'hash3',
            'integrity_status' => 'verified',
            'type' => 'file',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $pending = $service->getFilesPendingVerification();

        $this->assertGreaterThanOrEqual(2, $pending->count());
    }

    public function test_get_files_with_failed_integrity(): void
    {
        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'failed',
            'integrity_error' => 'test error',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'failed',
            'integrity_error' => 'another error',
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'verified',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $failed = $service->getFilesWithFailedIntegrity();

        $this->assertCount(2, $failed);
    }

    public function test_calculate_checksum_with_sha256(): void
    {
        $content = 'test content';
        $expected = hash('sha256', $content);

        $result = \App\Services\FileIntegrityService::calculateChecksum($content, 'sha256');

        $this->assertEquals($expected, $result);
    }

    public function test_calculate_checksum_with_md5(): void
    {
        $content = 'test content';
        $expected = md5($content);

        $result = \App\Services\FileIntegrityService::calculateChecksum($content, 'md5');

        $this->assertEquals($expected, $result);
    }

    public function test_calculate_checksum_with_sha1(): void
    {
        $content = 'test content';
        $expected = sha1($content);

        $result = \App\Services\FileIntegrityService::calculateChecksum($content, 'sha1');

        $this->assertEquals($expected, $result);
    }

    public function test_calculate_checksum_defaults_to_sha256(): void
    {
        $content = 'test content';
        $expected = hash('sha256', $content);

        $result = \App\Services\FileIntegrityService::calculateChecksum($content);

        $this->assertEquals($expected, $result);
    }

    public function test_get_integrity_statistics(): void
    {
        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'pending',
        ]);

        FileSystemObject::factory()->file()->count(2)->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'verified',
            'integrity_verified_at' => now(),
        ]);

        FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'failed',
        ]);

        $service = app(\App\Services\FileIntegrityService::class);
        $stats = $service->getIntegrityStatistics();

        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(2, $stats['verified']);
        $this->assertEquals(1, $stats['failed']);
        $this->assertEquals(4, $stats['total_files']);
    }

    public function test_retry_failed_verifications(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $fileContent = 'test file content';
        $correctChecksum = hash('sha256', $fileContent);

        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'name' => 'test.txt',
            'path' => '/drafts/test.txt',
            'checksum_sha256' => $correctChecksum,
            'checksum_algorithm' => 'sha256',
            'file_size' => strlen($fileContent),
            'integrity_status' => 'failed',
            'integrity_error' => 'previous error',
            'verification_attempts' => 1,
        ]);

        \Illuminate\Support\Facades\Storage::disk('local')->put('drafts/test.txt', $fileContent);

        $service = app(\App\Services\FileIntegrityService::class);
        $results = $service->retryFailedVerifications();

        $this->assertEquals(1, $results['total']);
        $this->assertEquals(1, $results['success']);
        $this->assertEquals(0, $results['failed']);

        $file->refresh();
        $this->assertEquals('verified', $file->integrity_status);
    }

    public function test_file_system_object_has_integrity_pending(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'pending',
        ]);

        $this->assertTrue($file->hasIntegrityPending());
        $this->assertFalse($file->isIntegrityVerified());
        $this->assertFalse($file->hasIntegrityFailed());
    }

    public function test_file_system_object_is_integrity_verified(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'verified',
        ]);

        $this->assertTrue($file->isIntegrityVerified());
        $this->assertFalse($file->hasIntegrityPending());
        $this->assertFalse($file->hasIntegrityFailed());
    }

    public function test_file_system_object_has_integrity_failed(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'failed',
        ]);

        $this->assertTrue($file->hasIntegrityFailed());
        $this->assertFalse($file->hasIntegrityPending());
        $this->assertFalse($file->isIntegrityVerified());
    }

    public function test_file_system_object_get_primary_checksum_sha256(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'checksum_sha256' => 'sha256hash',
            'checksum_algorithm' => 'sha256',
        ]);

        $this->assertEquals('sha256hash', $file->getPrimaryChecksum());
    }

    public function test_file_system_object_get_primary_checksum_md5(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'checksum_md5' => 'md5hash',
            'checksum_algorithm' => 'md5',
        ]);

        $this->assertEquals('md5hash', $file->getPrimaryChecksum());
    }

    public function test_file_system_object_mark_integrity_failed(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'pending',
            'verification_attempts' => 0,
        ]);

        $file->markIntegrityFailed('Test error message');

        $file->refresh();
        $this->assertEquals('failed', $file->integrity_status);
        $this->assertEquals('Test error message', $file->integrity_error);
        $this->assertEquals(1, $file->verification_attempts);
        $this->assertNotNull($file->last_verification_attempt);
    }

    public function test_file_system_object_mark_integrity_verified(): void
    {
        $file = FileSystemObject::factory()->file()->create([
            'draft_id' => $this->draft->id,
            'integrity_status' => 'pending',
            'verification_attempts' => 0,
        ]);

        $file->markIntegrityVerified();

        $file->refresh();
        $this->assertEquals('verified', $file->integrity_status);
        $this->assertNull($file->integrity_error);
        $this->assertEquals(1, $file->verification_attempts);
        $this->assertNotNull($file->integrity_verified_at);
        $this->assertNotNull($file->last_verification_attempt);
    }

    public function test_storage_signed_url_service_generate_multiple_signed_urls(): void
    {
        $service = new \App\Services\StorageSignedUrlService;

        $filePaths = [
            'test/file1.txt' => ['size' => 1000, 'type' => 'text/plain'],
            'test/file2.txt' => ['size' => 2000, 'type' => 'text/plain'],
        ];

        $result = $service->generateMultipleSignedUrls($filePaths);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        foreach ($result as $index => $signedUrl) {
            $this->assertArrayHasKey('uuid', $signedUrl);
            $this->assertArrayHasKey('bucket', $signedUrl);
            $this->assertArrayHasKey('key', $signedUrl);
            $this->assertArrayHasKey('url', $signedUrl);
            $this->assertArrayHasKey('headers', $signedUrl);
            $this->assertArrayHasKey('size', $signedUrl);
            $this->assertArrayHasKey('type', $signedUrl);
        }
    }

    public function test_storage_signed_url_service_generate_multiple_signed_urls_with_custom_bucket(): void
    {
        $service = new \App\Services\StorageSignedUrlService;

        $customBucket = 'custom-bucket';
        $filePaths = [
            'test/file1.txt' => ['metadata' => 'test'],
        ];

        $result = $service->generateMultipleSignedUrls($filePaths, $customBucket);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($customBucket, $result[0]['bucket']);
        $this->assertArrayHasKey('metadata', $result[0]);
    }

    public function test_storage_signed_url_service_get_client(): void
    {
        $service = new \App\Services\StorageSignedUrlService;

        $client = $service->getClient();

        $this->assertInstanceOf(\Aws\S3\S3Client::class, $client);
    }

    public function test_path_generator_service_parse_directories(): void
    {
        $service = new \App\Services\PathGeneratorService;

        $path = '/folder1/folder2/folder3/file.txt';
        $filename = 'file.txt';

        $directories = $service->parseDirectories($path, $filename);

        $this->assertIsArray($directories);
        $this->assertCount(3, $directories);
        $this->assertEquals(['folder1', 'folder2', 'folder3'], $directories);
    }

    public function test_path_generator_service_parse_directories_with_no_folders(): void
    {
        $service = new \App\Services\PathGeneratorService;

        $path = '/file.txt';
        $filename = 'file.txt';

        $directories = $service->parseDirectories($path, $filename);

        $this->assertIsArray($directories);
        $this->assertEmpty($directories);
    }

    public function test_path_generator_service_has_directories_with_path(): void
    {
        $service = new \App\Services\PathGeneratorService;

        $result = $service->hasDirectories('folder/file.txt', '/');

        $this->assertTrue($result);
    }

    public function test_path_generator_service_has_directories_with_non_root_destination(): void
    {
        $service = new \App\Services\PathGeneratorService;

        $result = $service->hasDirectories(null, '/folder');

        $this->assertTrue($result);
    }

    public function test_path_generator_service_has_directories_returns_false(): void
    {
        $service = new \App\Services\PathGeneratorService;

        $result = $service->hasDirectories(null, '/');

        $this->assertFalse($result);
    }
}
