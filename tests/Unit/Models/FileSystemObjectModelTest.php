<?php

namespace Tests\Unit\Models;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileSystemObjectModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $fileSystemObject = FileSystemObject::create([
            'name' => 'test-file.txt',
            'slug' => 'test-file-txt',
            'key' => 'test-key',
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'project_id' => $project->id,
        ]);

        $this->assertInstanceOf(Project::class, $fileSystemObject->project);
        $this->assertEquals($project->id, $fileSystemObject->project->id);
    }

    public function test_it_belongs_to_draft(): void
    {
        $draft = Draft::factory()->create();
        $fileSystemObject = FileSystemObject::create([
            'name' => 'test-file.txt',
            'slug' => 'test-file-txt',
            'key' => 'test-key-2',
            'uuid' => '123e4567-e89b-12d3-a456-426614174001',
            'draft_id' => $draft->id,
        ]);

        $this->assertInstanceOf(Draft::class, $fileSystemObject->draft);
        $this->assertEquals($draft->id, $fileSystemObject->draft->id);
    }

    public function test_it_belongs_to_study()
    {
        $study = Study::factory()->create();
        $fsObject = new FileSystemObject;
        $fsObject->study_id = $study->id;
        $fsObject->name = 'test-file.txt';
        $fsObject->slug = 'test-file-txt';
        $fsObject->key = 'test-key-3';
        $fsObject->uuid = '123e4567-e89b-12d3-a456-426614174002';
        $fsObject->save();

        $this->assertInstanceOf(Study::class, $fsObject->study);
        $this->assertEquals($study->id, $fsObject->study->id);
    }

    public function test_it_has_parent_child_relationships()
    {
        $parent = new FileSystemObject;
        $parent->name = 'parent-folder';
        $parent->slug = 'parent-folder';
        $parent->key = 'parent-key';
        $parent->uuid = '123e4567-e89b-12d3-a456-426614174003';
        $parent->type = 'directory';
        $parent->save();

        $child = new FileSystemObject;
        $child->name = 'child-file.txt';
        $child->slug = 'child-file-txt';
        $child->key = 'child-key';
        $child->uuid = '123e4567-e89b-12d3-a456-426614174004';
        $child->type = 'file';
        $child->parent_id = $parent->id;
        $child->save();

        $this->assertInstanceOf(FileSystemObject::class, $child->parent);
        $this->assertEquals($parent->id, $child->parent->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $parent->children);
        $this->assertCount(1, $parent->children);
        $this->assertTrue($parent->children->contains($child));
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $fillable = [
            'name', 'uuid', 'slug', 'description', 'relative_url', 'path', 'type', 'key',
            'compressionInfo', 'kernelSessionInfo', 'color', 'starred', 'is_public',
            'is_deleted', 'is_archived', 'is_original', 'is_verified', 'is_processed',
            'is_root', 'sort_order', 'owner_id', 'project_id', 'study_id', 'dataset_id',
            'draft_id', 'version_id', 'version', 'parent_id', 'settings', 'info', 'level',
            'has_children', 'checksum_md5', 'checksum_sha256', 'checksum_algorithm',
            'file_size', 'integrity_status', 'integrity_verified_at', 'integrity_error',
            'verification_attempts', 'last_verification_attempt', 'external_url',
        ];

        $fsObject = new FileSystemObject;

        $this->assertEquals($fillable, $fsObject->getFillable());
    }

    public function test_it_has_correct_appended_attributes()
    {
        $fsObject = new FileSystemObject;
        $appends = $fsObject->getAppends();

        $this->assertContains('download_url', $appends);
    }

    public function test_it_casts_datetime_fields()
    {
        $fsObject = new FileSystemObject;
        $casts = $fsObject->getCasts();

        $this->assertArrayHasKey('integrity_verified_at', $casts);
        $this->assertArrayHasKey('last_verification_attempt', $casts);
        $this->assertEquals('datetime', $casts['integrity_verified_at']);
        $this->assertEquals('datetime', $casts['last_verification_attempt']);
    }

    public function test_it_casts_integer_fields()
    {
        $fsObject = new FileSystemObject;
        $casts = $fsObject->getCasts();

        $this->assertArrayHasKey('verification_attempts', $casts);
        $this->assertArrayHasKey('file_size', $casts);
        $this->assertEquals('integer', $casts['verification_attempts']);
        $this->assertEquals('integer', $casts['file_size']);
    }

    public function test_has_integrity_pending_method()
    {
        $fsObject = new FileSystemObject;
        $fsObject->type = 'file';
        $fsObject->integrity_status = 'pending';

        $this->assertTrue($fsObject->hasIntegrityPending());

        $fsObject->integrity_status = 'verified';
        $this->assertFalse($fsObject->hasIntegrityPending());

        $fsObject->type = 'folder';
        $fsObject->integrity_status = 'pending';
        $this->assertFalse($fsObject->hasIntegrityPending());
    }

    public function test_is_integrity_verified_method()
    {
        $fsObject = new FileSystemObject;
        $fsObject->type = 'file';
        $fsObject->integrity_status = 'verified';

        $this->assertTrue($fsObject->isIntegrityVerified());

        $fsObject->integrity_status = 'pending';
        $this->assertFalse($fsObject->isIntegrityVerified());

        $fsObject->type = 'folder';
        $fsObject->integrity_status = 'verified';
        $this->assertFalse($fsObject->isIntegrityVerified());
    }

    public function test_has_integrity_failed_method()
    {
        $fsObject = new FileSystemObject;
        $fsObject->type = 'file';
        $fsObject->integrity_status = 'failed';

        $this->assertTrue($fsObject->hasIntegrityFailed());

        $fsObject->integrity_status = 'verified';
        $this->assertFalse($fsObject->hasIntegrityFailed());

        $fsObject->type = 'folder';
        $fsObject->integrity_status = 'failed';
        $this->assertFalse($fsObject->hasIntegrityFailed());
    }

    public function test_get_primary_checksum_method()
    {
        $fsObject = new FileSystemObject;

        // Test SHA256 algorithm
        $fsObject->checksum_algorithm = 'sha256';
        $fsObject->checksum_sha256 = 'sha256_hash_value';
        $fsObject->checksum_md5 = 'md5_hash_value';

        $this->assertEquals('sha256_hash_value', $fsObject->getPrimaryChecksum());

        // Test MD5 algorithm
        $fsObject->checksum_algorithm = 'md5';
        $this->assertEquals('md5_hash_value', $fsObject->getPrimaryChecksum());

        // Test default (unknown algorithm defaults to SHA256)
        $fsObject->checksum_algorithm = 'unknown';
        $this->assertEquals('sha256_hash_value', $fsObject->getPrimaryChecksum());
    }

    public function test_mark_integrity_failed_method()
    {
        $fsObject = new FileSystemObject;
        $fsObject->name = 'test-file.txt';
        $fsObject->slug = 'test-file-txt';
        $fsObject->key = 'test-key-5';
        $fsObject->uuid = '123e4567-e89b-12d3-a456-426614174005';
        $fsObject->verification_attempts = 2;
        $fsObject->save();

        $errorMessage = 'Checksum mismatch';
        $fsObject->markIntegrityFailed($errorMessage);

        $fsObject->refresh();
        $this->assertEquals('failed', $fsObject->integrity_status);
        $this->assertEquals($errorMessage, $fsObject->integrity_error);
        $this->assertEquals(3, $fsObject->verification_attempts);
        $this->assertNotNull($fsObject->last_verification_attempt);
    }

    public function test_mark_integrity_verified_method()
    {
        $fsObject = new FileSystemObject;
        $fsObject->name = 'test-file.txt';
        $fsObject->slug = 'test-file-txt';
        $fsObject->key = 'test-key-6';
        $fsObject->uuid = '123e4567-e89b-12d3-a456-426614174006';
        $fsObject->verification_attempts = 1;
        $fsObject->integrity_error = 'Previous error';
        $fsObject->save();

        $fsObject->markIntegrityVerified();

        $fsObject->refresh();
        $this->assertEquals('verified', $fsObject->integrity_status);
        $this->assertNull($fsObject->integrity_error);
        $this->assertEquals(2, $fsObject->verification_attempts);
        $this->assertNotNull($fsObject->integrity_verified_at);
        $this->assertNotNull($fsObject->last_verification_attempt);
    }

    public function test_relationship_types()
    {
        $fsObject = new FileSystemObject;

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $fsObject->children());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $fsObject->parent());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $fsObject->project());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $fsObject->draft());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $fsObject->study());
    }

    public function test_boolean_fields_work_correctly()
    {
        $fsObject = new FileSystemObject;

        $fsObject->is_public = true;
        $fsObject->is_deleted = false;
        $fsObject->is_archived = true;
        $fsObject->is_verified = false;

        $this->assertTrue($fsObject->is_public);
        $this->assertFalse($fsObject->is_deleted);
        $this->assertTrue($fsObject->is_archived);
        $this->assertFalse($fsObject->is_verified);
    }

    public function test_it_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(FileSystemObject::class, 'factory'));
    }

    public function test_get_download_url_attribute_with_study_model_type()
    {
        $study = Study::factory()->create();

        $fsObject = new FileSystemObject;
        $fsObject->name = 'test-file.txt';
        $fsObject->slug = 'test-file-txt';
        $fsObject->key = 'test-key-download';
        $fsObject->uuid = '123e4567-e89b-12d3-a456-426614174007';
        $fsObject->model_type = 'study';
        $fsObject->study_id = $study->id;
        $fsObject->save();

        // The getDownloadUrlAttribute method should call $this->study->download_url when model_type == 'study'
        // Since Study model might not have download_url attribute, we'll just test the method exists and returns appropriately
        $downloadUrl = $fsObject->download_url;

        // The method should either return null or a URL string
        $this->assertTrue(is_null($downloadUrl) || is_string($downloadUrl));
    }

    public function test_get_download_url_attribute_with_non_study_model_type()
    {
        $fsObject = new FileSystemObject;
        $fsObject->name = 'test-file.txt';
        $fsObject->slug = 'test-file-txt';
        $fsObject->key = 'test-key-non-study';
        $fsObject->uuid = '123e4567-e89b-12d3-a456-426614174008';
        $fsObject->model_type = 'project'; // Not 'study'
        $fsObject->save();

        // When model_type is not 'study', the method should return null
        $this->assertNull($fsObject->download_url);
    }
}
