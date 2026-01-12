<?php

namespace Tests\Unit\Models;

use App\Models\Announcement;
use App\Models\LinkedSocialAccount;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_linked_social_accounts(): void
    {
        $user = User::factory()->create();
        $account1 = new LinkedSocialAccount([
            'provider_name' => 'github',
            'provider_id' => '12345',
        ]);
        $account1->user()->associate($user);
        $account1->save();

        $account2 = new LinkedSocialAccount([
            'provider_name' => 'google',
            'provider_id' => '67890',
        ]);
        $account2->user()->associate($user);
        $account2->save();

        $this->assertCount(2, $user->linkedSocialAccounts);
        $this->assertTrue($user->linkedSocialAccounts->contains($account1));
        $this->assertTrue($user->linkedSocialAccounts->contains($account2));
    }

    public function test_it_has_many_announcements(): void
    {
        $user = User::factory()->create();
        $announcement1 = Announcement::factory()->create(['user_id' => $user->id]);
        $announcement2 = Announcement::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->announcements);
        $this->assertTrue($user->announcements->contains($announcement1));
        $this->assertTrue($user->announcements->contains($announcement2));
    }

    public function test_it_belongs_to_many_projects(): void
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $user->projects()->attach($project1->id, ['role' => 'viewer']);
        $user->projects()->attach($project2->id, ['role' => 'editor']);

        $this->assertCount(2, $user->projects);
        $this->assertTrue($user->projects->contains($project1));
        $this->assertTrue($user->projects->contains($project2));
    }

    public function test_it_has_active_projects(): void
    {
        $user = User::factory()->create();
        $activeProject = Project::factory()->create(['is_deleted' => false]);
        $deletedProject = Project::factory()->create(['is_deleted' => true]);

        $user->projects()->attach($activeProject->id, ['role' => 'viewer']);
        $user->projects()->attach($deletedProject->id, ['role' => 'viewer']);

        $activeProjects = $user->activeProjects;
        $this->assertCount(1, $activeProjects);
        $this->assertTrue($activeProjects->contains($activeProject));
        $this->assertFalse($activeProjects->contains($deletedProject));
    }

    public function test_it_has_shared_projects(): void
    {
        $user = User::factory()->create();
        $ownedProject = Project::factory()->create(['owner_id' => $user->id]);
        $sharedProject = Project::factory()->create();

        $user->projects()->attach($ownedProject->id, ['role' => 'creator']);
        $user->projects()->attach($sharedProject->id, ['role' => 'viewer']);

        $sharedProjects = $user->sharedProjects();
        $this->assertCount(1, $sharedProjects->get());
        $this->assertTrue($sharedProjects->get()->contains($sharedProject));
        $this->assertFalse($sharedProjects->get()->contains($ownedProject));
    }

    public function test_it_belongs_to_many_studies(): void
    {
        $user = User::factory()->create();
        $study1 = Study::factory()->create();
        $study2 = Study::factory()->create();

        $user->studies()->attach($study1->id, ['role' => 'viewer']);
        $user->studies()->attach($study2->id, ['role' => 'editor']);

        $this->assertCount(2, $user->studies);
        $this->assertTrue($user->studies->contains($study1));
        $this->assertTrue($user->studies->contains($study2));
    }

    public function test_it_has_shared_studies(): void
    {
        $user = User::factory()->create();
        $ownedStudy = Study::factory()->create(['owner_id' => $user->id]);
        $sharedStudy = Study::factory()->create();

        $user->studies()->attach($ownedStudy->id, ['role' => 'creator']);
        $user->studies()->attach($sharedStudy->id, ['role' => 'viewer']);

        $sharedStudies = $user->sharedStudies();
        $this->assertCount(1, $sharedStudies->get());
        $this->assertTrue($sharedStudies->get()->contains($sharedStudy));
        $this->assertFalse($sharedStudies->get()->contains($ownedStudy));
    }

    public function test_it_has_recent_studies(): void
    {
        $user = User::factory()->create();
        $oldStudy = Study::factory()->create(['updated_at' => now()->subDays(5)]);
        $newStudy = Study::factory()->create(['updated_at' => now()]);

        $user->studies()->attach($oldStudy->id, ['role' => 'viewer']);
        $user->studies()->attach($newStudy->id, ['role' => 'viewer']);

        $recentStudies = $user->recentStudies();
        $studiesCollection = $recentStudies->get();

        $this->assertEquals($newStudy->id, $studiesCollection->first()->id);
        $this->assertEquals($oldStudy->id, $studiesCollection->last()->id);
    }

    public function test_it_generates_default_profile_photo_url(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $expectedUrl = 'https://ui-avatars.com/api/?name='.urlencode('John+Doe').'&color=7F9CF5&background=EBF4FF';
        $this->assertEquals($expectedUrl, $user->profile_photo_url);
    }

    public function test_it_can_scope_order_by_name(): void
    {
        $userZ = User::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Zulu',
        ]);
        $userA = User::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Alpha',
        ]);
        $userB = User::factory()->create([
            'first_name' => 'Charlie',
            'last_name' => 'Beta',
        ]);

        $users = User::orderByName()->get();

        $this->assertEquals($userA->id, $users->first()->id); // Alpha comes first
        $this->assertEquals($userZ->id, $users->last()->id);  // Zulu comes last
    }

    public function test_it_can_filter_by_search_term(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);
        $user2 = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
        ]);
        $user3 = User::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Johnson',
            'email' => 'bob.johnson@example.com',
        ]);

        // Test search by first name
        $results = User::filter(['search' => 'John'])->get();
        $this->assertCount(2, $results); // John Doe and Bob Johnson
        $this->assertTrue($results->contains($user1));
        $this->assertTrue($results->contains($user3));

        // Test search by email
        $results = User::filter(['search' => 'jane.smith'])->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($user2));
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'name', 'first_name', 'last_name', 'username', 'email', 'password', 'onboarded', 'orcid_id', 'affiliation', 'ror_id',
        ];

        $user = new User;
        $this->assertEquals($fillable, $user->getFillable());
    }

    public function test_it_has_correct_hidden_attributes(): void
    {
        $hidden = [
            'password',
            'remember_token',
            'two_factor_recovery_codes',
            'two_factor_secret',
        ];

        $user = new User;
        $this->assertEquals($hidden, $user->getHidden());
    }

    public function test_it_has_correct_appended_attributes(): void
    {
        $appends = ['profile_photo_url'];

        $user = new User;
        $this->assertEquals($appends, $user->getAppends());
    }

    public function test_it_casts_email_verified_at_as_datetime(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 12:00:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    public function test_it_has_teams_when_using_jetstream(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        // Check if user has teams through ownedTeams (personal team)
        $this->assertNotEmpty($user->ownedTeams);
        $this->assertTrue($user->ownedTeams->first()->personal_team);
    }

    public function test_it_has_owned_teams(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $ownedTeam = Team::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($user->ownedTeams);
        $this->assertTrue($user->ownedTeams->contains($ownedTeam));
    }

    public function test_it_can_own_a_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->ownsTeam($team));
    }

    public function test_it_cannot_own_someone_elses_team(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $otherUser->id]);

        $this->assertFalse($user->ownsTeam($team));
    }

    public function test_it_can_belong_to_a_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $user->teams()->attach($team->id, ['role' => 'member']);

        $this->assertTrue($user->belongsToTeam($team));
    }

    public function test_it_cannot_belong_to_unrelated_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $this->assertFalse($user->belongsToTeam($team));
    }

    public function test_it_has_current_team(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        $this->assertNotNull($user->currentTeam);
        $this->assertInstanceOf(Team::class, $user->currentTeam);
    }

    public function test_it_can_switch_teams(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $newTeam = Team::factory()->create();
        $user->teams()->attach($newTeam->id, ['role' => 'member']);

        $originalTeam = $user->currentTeam;
        $user->switchTeam($newTeam);

        $this->assertNotEquals($originalTeam->id, $user->fresh()->current_team_id);
        $this->assertEquals($newTeam->id, $user->fresh()->current_team_id);
    }

    public function test_it_can_scope_where_role(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $viewerRole = Role::create(['name' => 'viewer']);

        // Create users and assign roles
        $adminUser = User::factory()->create();
        $editorUser = User::factory()->create();
        $viewerUser = User::factory()->create();
        $noRoleUser = User::factory()->create();

        $adminUser->assignRole($adminRole);
        $editorUser->assignRole($editorRole);
        $viewerUser->assignRole($viewerRole);

        // Test filtering by admin role
        $adminUsers = User::whereRole('admin')->get();
        $this->assertCount(1, $adminUsers);
        $this->assertTrue($adminUsers->contains($adminUser));
        $this->assertFalse($adminUsers->contains($editorUser));
        $this->assertFalse($adminUsers->contains($viewerUser));
        $this->assertFalse($adminUsers->contains($noRoleUser));

        // Test filtering by editor role
        $editorUsers = User::whereRole('editor')->get();
        $this->assertCount(1, $editorUsers);
        $this->assertFalse($editorUsers->contains($adminUser));
        $this->assertTrue($editorUsers->contains($editorUser));
        $this->assertFalse($editorUsers->contains($viewerUser));
        $this->assertFalse($editorUsers->contains($noRoleUser));

        // Test filtering by non-existent role
        $nonExistentUsers = User::whereRole('non-existent')->get();
        $this->assertCount(0, $nonExistentUsers);
    }

    public function test_it_can_filter_by_role(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);

        // Create users and assign roles
        $adminUser = User::factory()->create(['first_name' => 'Admin', 'last_name' => 'User']);
        $editorUser = User::factory()->create(['first_name' => 'Editor', 'last_name' => 'User']);
        $noRoleUser = User::factory()->create(['first_name' => 'No', 'last_name' => 'Role']);

        $adminUser->assignRole($adminRole);
        $editorUser->assignRole($editorRole);

        // Test filtering by role in combination with search
        $results = User::filter(['role' => 'admin'])->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($adminUser));
        $this->assertFalse($results->contains($editorUser));
        $this->assertFalse($results->contains($noRoleUser));

        // Test filtering by role and search term combined
        $results = User::filter(['role' => 'editor', 'search' => 'Editor'])->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($editorUser));
        $this->assertFalse($results->contains($adminUser));

        // Test filtering with empty filters
        $results = User::filter([])->get();
        $this->assertCount(3, $results);
    }

    public function test_it_can_mark_notification_as_read(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        // Create a fake notification manually in the database
        $notification = DatabaseNotification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => ['message' => 'Test notification'],
            'read_at' => null,
        ]);

        $this->assertNull($notification->read_at);

        // Mark the notification as read
        $user->markNotificationAsRead($notification->id);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_it_can_determine_impersonation_ability(): void
    {
        // Create roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $developerRole = Role::create(['name' => 'developer']);
        $adminRole = Role::create(['name' => 'admin']);

        // Create users
        $superAdminUser = User::factory()->create();
        $developerUser = User::factory()->create();
        $adminUser = User::factory()->create();
        $regularUser = User::factory()->create();

        // Assign roles
        $superAdminUser->assignRole($superAdminRole);
        $developerUser->assignRole($developerRole);
        $adminUser->assignRole($adminRole);

        // Test canImpersonate - only super-admin and developer can impersonate
        $this->assertTrue($superAdminUser->canImpersonate());
        $this->assertTrue($developerUser->canImpersonate());
        $this->assertFalse($adminUser->canImpersonate());
        $this->assertFalse($regularUser->canImpersonate());
    }

    public function test_it_can_determine_if_can_be_impersonated(): void
    {
        // Create roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $developerRole = Role::create(['name' => 'developer']);
        $adminRole = Role::create(['name' => 'admin']);

        // Create users
        $superAdminUser = User::factory()->create();
        $developerUser = User::factory()->create();
        $adminUser = User::factory()->create();
        $regularUser = User::factory()->create();

        // Assign roles
        $superAdminUser->assignRole($superAdminRole);
        $developerUser->assignRole($developerRole);
        $adminUser->assignRole($adminRole);

        // Test canBeImpersonated - super-admin and developer cannot be impersonated
        $this->assertFalse($superAdminUser->canBeImpersonated());
        $this->assertFalse($developerUser->canBeImpersonated());
        $this->assertTrue($adminUser->canBeImpersonated());
        $this->assertTrue($regularUser->canBeImpersonated());
    }

    public function test_it_can_get_user_team_data_for_personal_team(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $personalTeam = $user->currentTeam;

        [$userId, $teamId, $team] = $user->getUserTeamData();

        $this->assertEquals($user->id, $userId);
        $this->assertEquals($personalTeam->id, $teamId);
        $this->assertEquals($personalTeam->id, $team->id);
        $this->assertTrue($team->personal_team);
    }

    public function test_it_can_get_user_team_data_for_non_personal_team(): void
    {
        $teamOwner = User::factory()->create();
        $teamMember = User::factory()->create();

        // Create a non-personal team
        $team = Team::factory()->create([
            'user_id' => $teamOwner->id,
            'personal_team' => false,
        ]);

        // Add team member to the team
        $teamMember->teams()->attach($team->id, ['role' => 'member']);
        $teamMember->current_team_id = $team->id;
        $teamMember->save();

        [$userId, $teamId, $returnedTeam] = $teamMember->getUserTeamData();

        $this->assertEquals($teamOwner->id, $userId); // Team owner's ID
        $this->assertEquals($team->id, $teamId); // Current team ID
        $this->assertEquals($team->id, $returnedTeam->id);
        $this->assertFalse($returnedTeam->personal_team);
    }

    public function test_it_handles_edge_cases_in_filter_scope(): void
    {
        $user1 = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);
        $user2 = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
        ]);

        // Test with null filters
        $results = User::filter(['search' => null, 'role' => null])->get();
        $this->assertCount(2, $results);

        // Test with empty string search
        $results = User::filter(['search' => ''])->get();
        $this->assertCount(2, $results);

        // Test case insensitive search (testing proper case)
        $results = User::filter(['search' => 'John'])->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($user1));

        // Test partial email search
        $results = User::filter(['search' => 'jane.smith'])->get();
        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($user2));
    }
}
