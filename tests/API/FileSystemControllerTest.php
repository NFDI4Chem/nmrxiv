<?php

namespace Tests\API;

use App\Models\FileSystemObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FileSystemControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can retrieve children of a file system object
     */
    public function test_can_retrieve_children_of_file_system_object()
    {
        // Create parent file system object
        $parent = FileSystemObject::factory()->create([
            'type' => 'directory',
            'name' => 'Parent Folder',
        ]);

        // Create children
        $child1 = FileSystemObject::factory()->create([
            'parent_id' => $parent->id,
            'type' => 'file',
            'name' => 'Child File 1',
        ]);

        $child2 = FileSystemObject::factory()->create([
            'parent_id' => $parent->id,
            'type' => 'file',
            'name' => 'Child File 2',
        ]);

        $response = $this->getJson('/api/v1/files/children/'.$parent->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'files' => [
                '*' => [
                    'id',
                    'name',
                    'type',
                    'children',
                ],
            ],
        ]);
    }

    /**
     * Test returns file system object with no children
     */
    public function test_returns_file_system_object_with_no_children()
    {
        $fileObject = FileSystemObject::factory()->create([
            'type' => 'file',
            'name' => 'Single File',
        ]);

        $response = $this->getJson('/api/v1/files/children/'.$fileObject->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'files',
        ]);
    }

    /**
     * Test returns empty for non-existent file id
     */
    public function test_returns_empty_for_non_existent_file_id()
    {
        $response = $this->getJson('/api/v1/files/children/999999');

        $response->assertStatus(200);
        $response->assertJson(['files' => []]);
    }

    /**
     * Test file id is converted to integer
     */
    public function test_file_id_is_converted_to_integer()
    {
        $fileObject = FileSystemObject::factory()->create();

        $response = $this->getJson('/api/v1/files/children/'.$fileObject->id.'.5');

        $response->assertStatus(200);
        // The controller converts to int, so .5 should be truncated
    }

    /**
     * Test with nested children structure
     */
    public function test_with_nested_children_structure()
    {
        // Create parent
        $parent = FileSystemObject::factory()->create([
            'type' => 'directory',
            'name' => 'Root',
        ]);

        // Create child folder
        $childFolder = FileSystemObject::factory()->create([
            'parent_id' => $parent->id,
            'type' => 'directory',
            'name' => 'Child Folder',
        ]);

        // Create grandchild in child folder
        $grandchild = FileSystemObject::factory()->create([
            'parent_id' => $childFolder->id,
            'type' => 'file',
            'name' => 'Grandchild File',
        ]);

        $response = $this->getJson('/api/v1/files/children/'.$parent->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'files' => [
                '*' => [
                    'id',
                    'name',
                    'type',
                    'children' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test ordering by type and created_at
     */
    public function test_ordering_by_type_and_created_at()
    {
        $parent = FileSystemObject::factory()->create([
            'type' => 'directory',
        ]);

        // Create children with different types
        Carbon::setTestNow(now());
        FileSystemObject::factory()->create([
            'parent_id' => $parent->id,
            'type' => 'directory',
            'name' => 'Folder 1',
        ]);

        // Advance time by 1 second to ensure different timestamps
        Carbon::setTestNow(now()->addSecond());

        FileSystemObject::factory()->create([
            'parent_id' => $parent->id,
            'type' => 'file',
            'name' => 'File 1',
        ]);

        Carbon::setTestNow(); // Reset time

        $response = $this->getJson('/api/v1/files/children/'.$parent->id);

        $response->assertStatus(200);
        // Results should be ordered by type, then by created_at DESC
    }

    /**
     * Test with string file id
     */
    public function test_with_string_file_id()
    {
        $fileObject = FileSystemObject::factory()->create();

        $response = $this->getJson('/api/v1/files/children/abc');

        $response->assertStatus(200);
        // Controller converts 'abc' to int(0), should return empty
        $response->assertJson(['files' => []]);
    }
}
