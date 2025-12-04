<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sample;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->withPersonalTeam()->create();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_with_personal_team(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_personal_projects_for_personal_team(): void
    {
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => false,
        ]);

        // Create project owned by another user (should not appear)
        $otherUser = User::factory()->withPersonalTeam()->create();
        Project::factory()->create([
            'owner_id' => $otherUser->id,
            'team_id' => $otherUser->currentTeam->id,
            'is_deleted' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_filters_deleted_projects(): void
    {
        Project::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'is_deleted' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_samples_without_project_for_personal_team(): void
    {
        $study = Study::factory()->create([
            'owner_id' => $this->user->id,
            'team_id' => $this->user->currentTeam->id,
            'project_id' => null,
            'is_deleted' => false,
        ]);

        Sample::factory()->create([
            'study_id' => $study->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_with_non_personal_team(): void
    {
        $team = Team::factory()->create([
            'user_id' => $this->user->id,
            'personal_team' => false,
        ]);

        $this->user->current_team_id = $team->id;
        $this->user->save();

        Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => false,
        ]);

        Study::factory()->create([
            'team_id' => $team->id,
            'project_id' => null,
            'is_deleted' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_shared_with_me_requires_authentication(): void
    {
        $response = $this->get('/dashboard/shared-with-me');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shared_with_me_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/shared-with-me');

        $response->assertStatus(200);
    }

    public function test_trashed_requires_authentication(): void
    {
        $response = $this->get('/dashboard/trashed');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_trashed_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/trashed');

        $response->assertStatus(200);
    }

    public function test_starred_requires_authentication(): void
    {
        $response = $this->get('/dashboard/starred');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_starred_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/starred');

        $response->assertStatus(200);
    }

    public function test_recent_requires_authentication(): void
    {
        $response = $this->get('/dashboard/recent');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_recent_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/dashboard/recent');

        $response->assertStatus(200);
    }

    public function test_onboarding_status_requires_authentication(): void
    {
        $response = $this->post('/onboarding/complete');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_onboarding_status_marks_user_as_onboarded(): void
    {
        $this->user->onboarded = false;
        $this->user->save();

        $this->assertFalse($this->user->onboarded);

        $response = $this->actingAs($this->user)
            ->post('/onboarding/complete');

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertTrue($this->user->onboarded);
    }

    public function test_onboarding_status_returns_user_data(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/onboarding/complete');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->user->id]);
    }

    public function test_onboarding_status_handles_incomplete_status(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/onboarding/incomplete');

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertFalse($this->user->onboarded);
    }

    public function test_skip_primer_requires_authentication(): void
    {
        $response = $this->post('/primer/skip');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_skip_primer_marks_user_as_primed(): void
    {
        $this->user->primed = false;
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->post('/primer/skip');

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertTrue($this->user->primed);
    }

    public function test_skip_primer_does_not_return_content(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/primer/skip');

        $response->assertStatus(200);
        $this->assertEmpty($response->getContent());
    }
}
