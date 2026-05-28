<?php

namespace Tests\Unit\Policies;

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_publish_overdue_private_embargo_project(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'is_public' => false,
            'status' => 'embargo',
            'release_date' => now()->subDay(),
            'doi' => '10.1234/embargo',
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->assertTrue((new ProjectPolicy)->publishProject($owner, $project));
    }

    public function test_owner_can_update_overdue_private_embargo_project(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'is_public' => false,
            'status' => 'embargo',
            'release_date' => now()->subDay(),
            'doi' => '10.1234/embargo',
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->assertTrue((new ProjectPolicy)->updateProject($owner, $project));
    }

    public function test_public_project_cannot_be_published_again(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'is_public' => true,
            'status' => 'published',
            'doi' => '10.1234/public',
        ]);
        $project->users()->attach($owner, ['role' => 'creator']);

        $this->assertFalse((new ProjectPolicy)->publishProject($owner, $project));
    }
}
