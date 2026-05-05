<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\AddProjectMember;
use App\Events\AddingProjectMember;
use App\Events\ProjectMemberAdded;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AddProjectMemberTest extends TestCase
{
    use RefreshDatabase;

    private AddProjectMember $action;

    private User $owner;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new AddProjectMember;
        $this->owner = User::factory()->withPersonalTeam()->create();
        $this->project = Project::factory()->create([
            'owner_id' => $this->owner->id,
            'team_id' => $this->owner->currentTeam->id,
        ]);
    }

    public function test_can_add_project_member_with_valid_email()
    {
        Event::fake();
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->action->add($this->owner, $this->project, $newMember->email, 'reviewer');

        $this->project->refresh();
        $this->assertTrue($this->project->hasUserWithEmail($newMember->email));

        $addedUser = $this->project->users()->where('user_id', $newMember->id)->first();
        $this->assertNotNull($addedUser, 'User should be added to project');
        $this->assertEquals('reviewer', $addedUser->projectMembership->role);
    }

    public function test_adding_member_dispatches_events()
    {
        Event::fake();
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->action->add($this->owner, $this->project, $newMember->email, 'collaborator');

        Event::assertDispatched(AddingProjectMember::class, function ($event) use ($newMember) {
            return $event->project->id === $this->project->id && $event->user->id === $newMember->id;
        });

        Event::assertDispatched(ProjectMemberAdded::class, function ($event) use ($newMember) {
            return $event->project->id === $this->project->id && $event->user->id === $newMember->id;
        });
    }

    public function test_cannot_add_member_without_authorization()
    {
        $unauthorizedUser = User::factory()->withPersonalTeam()->create();
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->expectException(AuthorizationException::class);

        $this->action->add($unauthorizedUser, $this->project, $newMember->email, 'reviewer');
    }

    public function test_cannot_add_member_with_invalid_email()
    {
        $this->expectException(ValidationException::class);

        $this->action->add($this->owner, $this->project, 'invalid-email', 'reviewer');
    }

    public function test_cannot_add_member_with_nonexistent_email()
    {
        $this->expectException(ValidationException::class);

        $this->action->add($this->owner, $this->project, 'nonexistent@example.com', 'reviewer');
    }

    public function test_cannot_add_member_with_invalid_role()
    {
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->expectException(ValidationException::class);

        $this->action->add($this->owner, $this->project, $newMember->email, 'invalid-role');
    }

    public function test_cannot_add_existing_project_member()
    {
        $existingMember = User::factory()->withPersonalTeam()->create();
        $this->project->users()->attach($existingMember, ['role' => 'reviewer']);

        $this->expectException(ValidationException::class);

        $this->action->add($this->owner, $this->project, $existingMember->email, 'collaborator');
    }

    public function test_can_add_member_with_collaborator_role()
    {
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->action->add($this->owner, $this->project, $newMember->email, 'collaborator');

        $this->project->refresh();
        $this->assertTrue($this->project->hasUserWithEmail($newMember->email));

        $addedUser = $this->project->users()->where('user_id', $newMember->id)->first();
        $this->assertNotNull($addedUser, 'User should be added to project');
        $this->assertEquals('collaborator', $addedUser->projectMembership->role);
    }

    public function test_can_add_member_with_reviewer_role()
    {
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->action->add($this->owner, $this->project, $newMember->email, 'reviewer');

        $this->project->refresh();
        $this->assertTrue($this->project->hasUserWithEmail($newMember->email));

        $addedUser = $this->project->users()->where('user_id', $newMember->id)->first();
        $this->assertNotNull($addedUser, 'User should be added to project');
        $this->assertEquals('reviewer', $addedUser->projectMembership->role);
    }

    public function test_validate_email_is_required()
    {
        $this->expectException(ValidationException::class);

        $this->action->add($this->owner, $this->project, '', 'reviewer');
    }

    public function test_validate_role_is_required()
    {
        $newMember = User::factory()->withPersonalTeam()->create();

        $this->expectException(ValidationException::class);

        $this->action->add($this->owner, $this->project, $newMember->email, null);
    }

    public function test_can_add_multiple_members_to_same_project()
    {
        $member1 = User::factory()->withPersonalTeam()->create();
        $member2 = User::factory()->withPersonalTeam()->create();

        $this->action->add($this->owner, $this->project, $member1->email, 'reviewer');
        $this->action->add($this->owner, $this->project, $member2->email, 'collaborator');

        $this->project->refresh();
        $this->assertTrue($this->project->hasUserWithEmail($member1->email));
        $this->assertTrue($this->project->hasUserWithEmail($member2->email));
        $this->assertEquals(2, $this->project->users()->count());
    }

    public function test_authorization_is_checked_via_gate()
    {
        Gate::shouldReceive('forUser')->with($this->owner)->andReturnSelf();
        Gate::shouldReceive('authorize')->with('addProjectMember', $this->project)->once();

        $newMember = User::factory()->withPersonalTeam()->create();

        $this->action->add($this->owner, $this->project, $newMember->email, 'reviewer');
    }

    public function test_validation_error_contains_custom_message_for_existing_user()
    {
        $existingMember = User::factory()->withPersonalTeam()->create();
        $this->project->users()->attach($existingMember, ['role' => 'reviewer']);

        try {
            $this->action->add($this->owner, $this->project, $existingMember->email, 'collaborator');
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('This user already belongs to the project', $e->getMessage());
        }
    }

    public function test_validation_error_contains_custom_message_for_nonexistent_user()
    {
        try {
            $this->action->add($this->owner, $this->project, 'nonexistent@example.com', 'reviewer');
            $this->fail('Expected ValidationException was not thrown');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('We were unable to find a registered user with this email address', $e->getMessage());
        }
    }
}
