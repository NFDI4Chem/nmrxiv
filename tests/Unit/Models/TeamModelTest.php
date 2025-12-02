<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Team as JetstreamTeam;
use Tests\TestCase;

class TeamModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_extends_jetstream_team(): void
    {
        $team = new Team;

        $this->assertInstanceOf(JetstreamTeam::class, $team);
    }

    public function test_it_has_many_projects(): void
    {
        $team = Team::factory()->create();
        $project1 = Project::factory()->create(['team_id' => $team->id]);
        $project2 = Project::factory()->create(['team_id' => $team->id]);

        $this->assertCount(2, $team->projects);
        $this->assertTrue($team->projects->contains($project1));
        $this->assertTrue($team->projects->contains($project2));
    }

    public function test_it_has_many_active_projects(): void
    {
        $team = Team::factory()->create();

        // Create active projects (not deleted, not archived)
        $activeProject1 = Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => false,
            'is_archived' => false,
        ]);
        $activeProject2 = Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => false,
            'is_archived' => false,
        ]);

        // Create inactive projects
        $deletedProject = Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => true,
            'is_archived' => false,
        ]);
        $archivedProject = Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => false,
            'is_archived' => true,
        ]);

        $activeProjects = $team->activeProjects;

        $this->assertCount(2, $activeProjects);
        $this->assertTrue($activeProjects->contains($activeProject1));
        $this->assertTrue($activeProjects->contains($activeProject2));
        $this->assertFalse($activeProjects->contains($deletedProject));
        $this->assertFalse($activeProjects->contains($archivedProject));
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'personal_team',
        ];

        $team = new Team;
        $this->assertEquals($fillable, $team->getFillable());
    }

    public function test_it_has_correct_appended_attributes(): void
    {
        $team = new Team;
        $expected = ['profile_photo_url'];

        $this->assertEquals($expected, $team->getAppends());
    }

    public function test_it_casts_personal_team_as_boolean(): void
    {
        $team = Team::factory()->create(['personal_team' => 1]);

        $this->assertIsBool($team->personal_team);
        $this->assertTrue($team->personal_team);
    }

    public function test_it_generates_profile_photo_url(): void
    {
        $team = Team::factory()->create(['name' => 'Test Team']);

        $expectedUrl = 'https://ui-avatars.com/api/?name='.urlencode('Test Team').'&color=7F9CF5&background=EBF4FF';
        $this->assertEquals($expectedUrl, $team->profile_photo_url);
    }

    public function test_it_can_be_created_with_factory(): void
    {
        $team = Team::factory()->create();

        $this->assertInstanceOf(Team::class, $team);
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $team->name,
            'personal_team' => $team->personal_team,
        ]);
    }

    public function test_it_has_timestamps(): void
    {
        $team = Team::factory()->create();

        $this->assertNotNull($team->created_at);
        $this->assertNotNull($team->updated_at);
    }

    public function test_it_can_be_created_with_specific_attributes(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'name' => 'Research Team',
            'user_id' => $user->id,
            'personal_team' => false,
        ]);

        $this->assertEquals('Research Team', $team->name);
        $this->assertEquals($user->id, $team->user_id);
        $this->assertFalse($team->personal_team);
    }

    public function test_projects_relationship_is_has_many(): void
    {
        $team = Team::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $team->projects());
    }

    public function test_active_projects_relationship_is_has_many(): void
    {
        $team = Team::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $team->activeProjects());
    }

    public function test_factory_creates_unique_team_names(): void
    {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $this->assertNotEquals($team1->name, $team2->name);
    }

    public function test_factory_creates_personal_teams_by_default(): void
    {
        $team = Team::factory()->create();

        $this->assertTrue($team->personal_team);
    }

    public function test_it_can_create_non_personal_teams(): void
    {
        $team = Team::factory()->create(['personal_team' => false]);

        $this->assertFalse($team->personal_team);
    }

    public function test_fillable_fields_can_be_mass_assigned(): void
    {
        // Only test the fields that are actually fillable
        $data = [
            'name' => 'Development Team',
            'personal_team' => false,
        ];

        $user = User::factory()->create();

        // Create team using factory then update with fillable data
        $team = Team::factory()->create(['user_id' => $user->id]);
        $team->update($data);

        $this->assertEquals('Development Team', $team->name);
        $this->assertFalse($team->personal_team);
    }

    public function test_team_model_uses_factory_trait(): void
    {
        $this->assertTrue(method_exists(Team::class, 'factory'));
    }

    public function test_profile_photo_url_handles_special_characters(): void
    {
        $team = Team::factory()->create(['name' => 'Team & Co.']);

        $expectedUrl = 'https://ui-avatars.com/api/?name='.urlencode('Team & Co.').'&color=7F9CF5&background=EBF4FF';
        $this->assertEquals($expectedUrl, $team->profile_photo_url);
    }

    public function test_active_projects_excludes_both_deleted_and_archived(): void
    {
        $team = Team::factory()->create();

        $activeProject = Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => false,
            'is_archived' => false,
        ]);

        $bothDeletedAndArchived = Project::factory()->create([
            'team_id' => $team->id,
            'is_deleted' => true,
            'is_archived' => true,
        ]);

        $activeProjects = $team->activeProjects;

        $this->assertCount(1, $activeProjects);
        $this->assertTrue($activeProjects->contains($activeProject));
        $this->assertFalse($activeProjects->contains($bothDeletedAndArchived));
    }

    public function test_it_has_jetstream_events_configured(): void
    {
        $team = new Team;

        // Test that the events property exists and is configured
        $reflection = new \ReflectionClass($team);
        $property = $reflection->getProperty('dispatchesEvents');
        $property->setAccessible(true);
        $events = $property->getValue($team);

        $expectedEvents = [
            'created' => \Laravel\Jetstream\Events\TeamCreated::class,
            'updated' => \Laravel\Jetstream\Events\TeamUpdated::class,
            'deleted' => \Laravel\Jetstream\Events\TeamDeleted::class,
        ];

        $this->assertEquals($expectedEvents, $events);
    }
}
