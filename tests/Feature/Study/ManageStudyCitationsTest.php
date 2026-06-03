<?php

namespace Tests\Feature\Study;

use App\Models\Citation;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageStudyCitationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_study_citation_can_be_synced(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study->users()->attach($user, ['role' => 'creator']);

        $citation = Citation::factory()->create();

        $body = [
            'citations' => [[
                'id' => $citation->id,
                'doi' => $citation->doi,
                'title' => $citation->title,
                'authors' => $citation->authors,
                'citation_text' => $citation->citation_text,
            ]],
        ];

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('citation.study.save', $study), $body);

        $response->assertOk();

        $this->assertDatabaseHas('citation_study', [
            'study_id' => $study->id,
            'citation_id' => $citation->id,
        ]);
    }

    public function test_study_citation_can_be_detached(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $license = License::factory()->create();
        $validation = Validation::factory()->create();
        $project = Project::factory()->create([
            'team_id' => $team->id,
            'owner_id' => $user->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);
        $study = Study::factory()->create([
            'project_id' => $project->id,
            'owner_id' => $user->id,
            'team_id' => $team->id,
            'license_id' => $license->id,
            'validation_id' => $validation->id,
        ]);

        $study->users()->attach($user, ['role' => 'creator']);

        $citation = Citation::factory()->create();
        $study->linkedCitations()->attach($citation->id, ['user' => (string) $user->id]);

        $body = [
            'citations' => [['id' => $citation->id]],
        ];

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('citation.study.delete', $study), $body);

        $response->assertOk();

        $this->assertDatabaseMissing('citation_study', [
            'study_id' => $study->id,
            'citation_id' => $citation->id,
        ]);
    }
}
