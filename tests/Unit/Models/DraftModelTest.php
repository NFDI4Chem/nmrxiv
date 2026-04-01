<?php

namespace Tests\Unit\Models;

use App\Models\Draft;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_owner()
    {
        $user = User::factory()->create();
        $draft = Draft::factory()->create(['owner_id' => $user->id]);

        $this->assertInstanceOf(User::class, $draft->owner);
        $this->assertEquals($user->id, $draft->owner->id);
    }

    public function test_it_belongs_to_team()
    {
        $team = Team::factory()->create();
        $draft = Draft::factory()->create(['team_id' => $team->id]);

        $this->assertInstanceOf(Team::class, $draft->team);
        $this->assertEquals($team->id, $draft->team->id);
    }

    public function test_it_has_one_project()
    {
        $draft = Draft::factory()->create();
        $project = Project::factory()->create(['draft_id' => $draft->id]);

        $this->assertInstanceOf(Project::class, $draft->project);
        $this->assertEquals($project->id, $draft->project->id);
    }

    public function test_it_has_many_files()
    {
        $draft = Draft::factory()->create();

        // Test the relationship exists and is correct type
        $relationship = $draft->files();
        $this->assertInstanceOf(HasMany::class, $relationship);

        // Test initial empty collection
        $this->assertInstanceOf(Collection::class, $draft->files);
        $this->assertCount(0, $draft->files);
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $fillable = [
            'name',
            'slug',
            'description',
            'relative_url',
            'path',
            'key',
            'is_deleted',
            'owner_id',
            'team_id',
            'settings',
            'info',
            'project_enabled',
            'current_step',
            'eln',
            'external_id',
            'callback_url',
            'zip_url',
            'release_date',
            'status',
            'processing_logs',
            'tracking_item_name',
        ];

        $draft = new Draft;

        $this->assertEquals($fillable, $draft->getFillable());
    }

    public function test_it_can_be_created_with_factory()
    {
        $draft = Draft::factory()->create();

        $this->assertInstanceOf(Draft::class, $draft);
        $this->assertNotNull($draft->id);
    }

    public function test_it_has_timestamps()
    {
        $draft = Draft::factory()->create();

        $this->assertNotNull($draft->created_at);
        $this->assertNotNull($draft->updated_at);
    }

    public function test_it_casts_processing_logs_to_array()
    {
        $draft = Draft::factory()->create([
            'processing_logs' => ['step1' => 'completed', 'step2' => 'pending'],
        ]);

        $this->assertIsArray($draft->processing_logs);
        $this->assertEquals(['step1' => 'completed', 'step2' => 'pending'], $draft->processing_logs);
    }

    public function test_it_casts_release_date_to_date()
    {
        $draft = Draft::factory()->create(['release_date' => '2023-12-25']);

        $this->assertInstanceOf(Carbon::class, $draft->release_date);
        $this->assertEquals('2023-12-25', $draft->release_date->format('Y-m-d'));
    }

    public function test_owner_relationship_is_belongs_to()
    {
        $draft = Draft::factory()->create();
        $relationship = $draft->owner();

        $this->assertInstanceOf(BelongsTo::class, $relationship);
    }

    public function test_team_relationship_is_belongs_to()
    {
        $draft = Draft::factory()->create();
        $relationship = $draft->team();

        $this->assertInstanceOf(BelongsTo::class, $relationship);
    }

    public function test_project_relationship_is_has_one()
    {
        $draft = Draft::factory()->create();
        $relationship = $draft->project();

        $this->assertInstanceOf(HasOne::class, $relationship);
    }

    public function test_files_relationship_is_has_many()
    {
        $draft = Draft::factory()->create();
        $relationship = $draft->files();

        $this->assertInstanceOf(HasMany::class, $relationship);
    }

    public function test_it_uses_has_factory_trait()
    {
        $this->assertTrue(method_exists(Draft::class, 'factory'));
    }

    public function test_it_uses_has_tags_trait()
    {
        $this->assertTrue(method_exists(Draft::class, 'tags'));
        $this->assertTrue(method_exists(Draft::class, 'attachTag'));
        $this->assertTrue(method_exists(Draft::class, 'detachTag'));
    }

    public function test_it_can_be_created_with_specific_attributes()
    {
        $attributes = [
            'name' => 'Test Draft',
            'slug' => 'test-draft',
            'description' => 'A test draft for unit testing',
            'status' => 'pending',
            'current_step' => 1,
            'project_enabled' => true,
        ];

        $draft = Draft::factory()->create($attributes);

        $this->assertEquals($attributes['name'], $draft->name);
        $this->assertEquals($attributes['slug'], $draft->slug);
        $this->assertEquals($attributes['description'], $draft->description);
        $this->assertEquals($attributes['status'], $draft->status);
        $this->assertEquals($attributes['current_step'], $draft->current_step);
        $this->assertEquals($attributes['project_enabled'], $draft->project_enabled);
    }

    public function test_processing_logs_can_be_null()
    {
        $draft = Draft::factory()->create(['processing_logs' => null]);

        $this->assertNull($draft->processing_logs);
    }

    public function test_release_date_can_be_null()
    {
        $draft = Draft::factory()->create(['release_date' => null]);

        $this->assertNull($draft->release_date);
    }

    public function test_all_required_fields_are_fillable()
    {
        $requiredFields = [
            'name', 'slug', 'description', 'relative_url', 'path', 'key',
            'is_deleted', 'owner_id', 'team_id', 'settings', 'info',
            'project_enabled', 'current_step', 'eln', 'external_id',
            'callback_url', 'zip_url', 'release_date', 'status',
            'processing_logs', 'tracking_item_name',
        ];
        $fillable = (new Draft)->getFillable();

        foreach ($requiredFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_boolean_fields_work_correctly()
    {
        $draft = Draft::factory()->create([
            'is_deleted' => true,
            'project_enabled' => false,
        ]);

        $this->assertTrue($draft->is_deleted);
        $this->assertFalse($draft->project_enabled);
    }
}
