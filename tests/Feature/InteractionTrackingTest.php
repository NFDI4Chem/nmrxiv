<?php

namespace Tests\Feature;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Project $project;

    private Study $study;

    private Dataset $dataset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
        $this->team = $this->user->currentTeam;

        [$userId, $teamId] = $this->user->getUserTeamData();

        $this->project = Project::factory()->create([
            'owner_id' => $userId,
            'team_id' => $teamId,
            'is_public' => true,
            'identifier' => 1,
            'views' => 0,
            'downloads' => 0,
        ]);

        $this->study = Study::factory()->create([
            'project_id' => $this->project->id,
            'owner_id' => $userId,
            'team_id' => $teamId,
            'is_public' => true,
            'identifier' => 1,
            'views' => 0,
            'downloads' => 0,
        ]);

        $this->dataset = Dataset::factory()->create([
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'owner_id' => $userId,
            'team_id' => $teamId,
            'is_public' => true,
            'identifier' => 1,
        ]);
    }

    public function test_public_project_view_increments_project_views_once_per_session(): void
    {
        $this->get('/project/P1')->assertOk();
        $this->get('/project/P1?tab=samples')->assertOk();

        $this->project->refresh();

        $this->assertSame(1, $this->project->views);
    }

    public function test_study_view_with_project_increments_project_not_study(): void
    {
        $this->get('/sample/S1')->assertOk();

        $this->project->refresh();
        $this->study->refresh();

        $this->assertSame(1, $this->project->views);
        $this->assertSame(0, $this->study->views);
    }

    public function test_standalone_study_view_increments_study_views(): void
    {
        $standaloneStudy = Study::factory()->create([
            'project_id' => null,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => true,
            'identifier' => 2,
            'views' => 0,
            'downloads' => 0,
        ]);

        $this->get('/sample/S2')->assertOk();

        $standaloneStudy->refresh();
        $this->project->refresh();

        $this->assertSame(1, $standaloneStudy->views);
        $this->assertSame(0, $this->project->views);
    }

    public function test_dataset_view_rolls_up_to_project(): void
    {
        $this->get('/dataset/D1')->assertOk();

        $this->project->refresh();
        $this->study->refresh();

        $this->assertSame(1, $this->project->views);
        $this->assertSame(0, $this->study->views);
    }

    public function test_standalone_dataset_view_rolls_up_to_study(): void
    {
        $standaloneStudy = Study::factory()->create([
            'project_id' => null,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => true,
            'identifier' => 3,
            'views' => 0,
        ]);

        $standaloneDataset = Dataset::factory()->create([
            'project_id' => null,
            'study_id' => $standaloneStudy->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => true,
            'identifier' => 2,
        ]);

        $this->get('/dataset/D2')->assertOk();

        $standaloneStudy->refresh();

        $this->assertSame(1, $standaloneStudy->views);
    }

    public function test_private_project_view_by_owner_does_not_increment_views(): void
    {
        $privateProject = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'identifier' => 4,
            'views' => 0,
        ]);

        $this->actingAs($this->user)
            ->get('/project/P4')
            ->assertOk();

        $privateProject->refresh();

        $this->assertSame(0, $privateProject->views);
    }

    public function test_download_beacon_increments_project_downloads_for_project_identifier(): void
    {
        $this->post('/track/download/P1')->assertNoContent();

        $this->project->refresh();

        $this->assertSame(1, $this->project->downloads);
    }

    public function test_download_beacon_rolls_study_downloads_up_to_project(): void
    {
        $this->post('/track/download/S1')->assertNoContent();

        $this->project->refresh();
        $this->study->refresh();

        $this->assertSame(1, $this->project->downloads);
        $this->assertSame(0, $this->study->downloads);
    }

    public function test_download_beacon_for_private_identifier_returns_204_without_incrementing(): void
    {
        $privateProject = Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'is_public' => false,
            'identifier' => 5,
            'downloads' => 0,
        ]);

        $this->post('/track/download/P5')->assertNoContent();

        $privateProject->refresh();

        $this->assertSame(0, $privateProject->downloads);
    }

    public function test_download_beacon_for_unknown_identifier_returns_204(): void
    {
        $this->post('/track/download/P99999')->assertNoContent();

        $this->project->refresh();

        $this->assertSame(0, $this->project->downloads);
    }

    public function test_download_beacon_dedupes_within_session(): void
    {
        $this->post('/track/download/P1')->assertNoContent();
        $this->post('/track/download/P1')->assertNoContent();

        $this->project->refresh();

        $this->assertSame(1, $this->project->downloads);
    }

    public function test_download_beacon_is_rate_limited(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->post('/track/download/P1')->assertNoContent();
        }

        $this->post('/track/download/P1')->assertStatus(429);
    }

    public function test_project_resource_exposes_view_and_download_stats(): void
    {
        Project::query()->whereKey($this->project->id)->update([
            'views' => 12,
            'downloads' => 3,
        ]);

        $response = $this->get('/project/P1');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('project.data.stats.views', 12)
            ->where('project.data.stats.downloads', 3));
    }

    public function test_download_from_project_returns_404_for_unknown_uuid(): void
    {
        $response = $this->get('/'.$this->user->username.'/download/'.$this->project->slug.'?uuid=00000000-0000-0000-0000-000000000000&key=test-key');

        $response->assertStatus(404);
    }

    public function test_download_from_project_ignores_user_supplied_bucket_parameter(): void
    {
        $fsObject = FileSystemObject::factory()->create([
            'type' => 'file',
            'name' => 'test-file.txt',
            'key' => 'test-key',
            'path' => '/test/file.txt',
        ]);

        $dataset = Dataset::factory()->create([
            'project_id' => $this->project->id,
            'study_id' => $this->study->id,
            'owner_id' => $this->user->id,
            'team_id' => $this->team->id,
            'slug' => 'bucket-test-dataset',
            'fs_id' => $fsObject->id,
        ]);

        $response = $this->get(
            '/'.$this->user->username.'/datasets/'.$this->project->slug.'/'.$this->study->slug.'/'.$dataset->slug.'?bucket=evil-bucket'
        );

        $this->assertNotSame(500, $response->getStatusCode());
    }
}
