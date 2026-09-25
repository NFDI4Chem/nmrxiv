<?php

namespace Tests\Feature\Console;

use App\Models\Dataset;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class UnpublishProjectsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_unpublishes_project_and_returns_success(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();

        $project = Project::factory()->create([
            'name' => 'Caffeine NMR',
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'identifier' => 177,
            'is_public' => true,
            'status' => 'published',
            'process_logs' => json_encode(['published' => true]),
            'release_date' => now(),
            'draft_id' => null,
        ]);

        $study = Study::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => $project->id,
            'is_public' => true,
            'draft_id' => null,
        ]);

        $dataset = Dataset::factory()->create([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'project_id' => $project->id,
            'study_id' => $study->id,
            'is_public' => true,
            'draft_id' => null,
        ]);

        $folder = FileSystemObject::factory()->directory()->rootLevel()->forProject($project)->create([
            'relative_url' => '/samples',
            'path' => '/published/samples',
            'has_children' => true,
            'draft_id' => null,
        ]);

        $originalFilePath = 'published/samples/spectrum.jdx';
        $file = FileSystemObject::factory()->file()->forProject($project)->create([
            'parent_id' => $folder->id,
            'level' => 1,
            'relative_url' => '/samples/spectrum.jdx',
            'path' => $originalFilePath,
            'draft_id' => null,
        ]);
        Storage::disk('local')->put($originalFilePath, 'spectrum');

        $this->artisan('nmrxiv:unpublish', ['ids' => '177'])
            ->assertSuccessful();

        $project->refresh();
        $study->refresh();
        $dataset->refresh();
        $folder->refresh();
        $file->refresh();

        $draft = Draft::query()->findOrFail($project->draft_id);

        $this->assertFalse($project->is_public);
        $this->assertNull($project->release_date);
        $this->assertSame('draft', $project->status);
        $this->assertNull($project->process_logs);
        $this->assertSame($project->name, $draft->name);
        $this->assertSame(Str::slug($project->name), $draft->slug);
        $this->assertSame($owner->id, $draft->owner_id);
        $this->assertSame($team->id, $draft->team_id);
        $this->assertSame($draft->id, $study->draft_id);
        $this->assertSame($draft->id, $dataset->draft_id);
        $this->assertFalse($study->is_public);
        $this->assertFalse($dataset->is_public);
        $this->assertSame($draft->id, $folder->draft_id);
        $this->assertSame('/'.$draft->path.'/samples', $folder->path);
        $this->assertSame($draft->id, $file->draft_id);
        $this->assertSame('/'.$draft->path.'/samples/spectrum.jdx', $file->path);
        Storage::disk('local')->assertMissing($originalFilePath);
        Storage::disk('local')->assertExists($file->path);

        $this->artisan('nmrxiv:unpublish', ['ids' => '177'])
            ->assertSuccessful();

        $this->assertSame(1, Draft::query()->count());
        $project->refresh();
        $this->assertSame($draft->id, $project->draft_id);
    }

    public function test_command_reuses_existing_project_draft(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();
        $draft = Draft::factory()->create([
            'name' => 'Caffeine NMR',
            'owner_id' => $owner->id,
            'team_id' => $team->id,
        ]);

        $project = Project::factory()->create([
            'name' => 'Caffeine NMR',
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
            'identifier' => 177,
            'is_public' => true,
            'status' => 'published',
            'release_date' => now(),
            'draft_id' => $draft->id,
        ]);

        $this->artisan('nmrxiv:unpublish', ['ids' => '177'])
            ->assertSuccessful();

        $project->refresh();

        $this->assertSame(1, Draft::query()->count());
        $this->assertSame($draft->id, $project->draft_id);
        $this->assertFalse($project->is_public);
        $this->assertSame('draft', $project->status);
        $this->assertNull($project->release_date);
    }

    public function test_command_unpublishes_project_by_database_id(): void
    {
        $project = $this->publishedProject(['identifier' => 9001]);
        $projectWithMatchingIdentifier = $this->publishedProject([
            'identifier' => $project->id,
        ]);

        $this->artisan('nmrxiv:unpublish', [
            '--id' => [(string) $project->id],
        ])->assertSuccessful();

        $project->refresh();
        $projectWithMatchingIdentifier->refresh();

        $this->assertFalse($project->is_public);
        $this->assertSame('draft', $project->status);
        $this->assertTrue($projectWithMatchingIdentifier->is_public);
    }

    public function test_command_keeps_positional_values_as_identifiers(): void
    {
        $project = $this->publishedProject(['identifier' => 9001]);

        $this->artisan('nmrxiv:unpublish', [
            'ids' => (string) $project->id,
        ])->expectsOutput('No matching projects found.')
            ->assertFailed();

        $project->refresh();
        $this->assertTrue($project->is_public);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function publishedProject(array $overrides = []): Project
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        return Project::factory()->create(array_merge([
            'owner_id' => $owner->id,
            'team_id' => $team->id,
            'license_id' => License::factory()->create()->id,
            'validation_id' => Validation::factory()->create()->id,
            'is_public' => true,
            'status' => 'published',
            'release_date' => now(),
            'draft_id' => null,
        ], $overrides));
    }
}
