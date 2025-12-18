<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\ProjectInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectInvitationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_project()
    {
        $project = Project::factory()->create();
        $invitation = new ProjectInvitation;
        $invitation->project_id = $project->id;
        $invitation->email = 'test@example.com';
        $invitation->role = 'contributor';
        $invitation->save();

        $this->assertInstanceOf(Project::class, $invitation->project);
        $this->assertEquals($project->id, $invitation->project->id);
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $fillable = [
            'project_id',
            'email',
            'role',
            'message',
            'invited_by',
        ];

        $invitation = new ProjectInvitation;

        $this->assertEquals($fillable, $invitation->getFillable());
    }

    public function test_it_can_be_created_with_attributes()
    {
        $invitation = new ProjectInvitation;

        $invitation->email = 'test@example.com';
        $invitation->role = 'collaborator';
        $invitation->message = 'Please join our project';
        $invitation->invited_by = 1;

        $this->assertEquals('test@example.com', $invitation->email);
        $this->assertEquals('collaborator', $invitation->role);
        $this->assertEquals('Please join our project', $invitation->message);
        $this->assertEquals(1, $invitation->invited_by);
    }

    public function test_it_has_timestamps()
    {
        $invitation = new ProjectInvitation;

        $this->assertTrue($invitation->usesTimestamps());
    }

    public function test_project_relationship_is_belongs_to()
    {
        $invitation = new ProjectInvitation;
        $relationship = $invitation->project();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relationship);
    }

    public function test_all_required_fields_are_fillable()
    {
        $requiredFields = ['email', 'role', 'message', 'invited_by'];
        $fillable = (new ProjectInvitation)->getFillable();

        foreach ($requiredFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_email_field_can_store_email_addresses()
    {
        $invitation = new ProjectInvitation;

        $emails = [
            'user@example.com',
            'test.email+tag@domain.co.uk',
            'very.long.email.address@subdomain.example.org',
        ];

        foreach ($emails as $email) {
            $invitation->email = $email;
            $this->assertEquals($email, $invitation->email);
        }
    }

    public function test_role_field_can_store_different_roles()
    {
        $invitation = new ProjectInvitation;

        $roles = ['viewer', 'contributor', 'collaborator', 'owner'];

        foreach ($roles as $role) {
            $invitation->role = $role;
            $this->assertEquals($role, $invitation->role);
        }
    }

    public function test_message_field_can_be_null()
    {
        $invitation = new ProjectInvitation;
        $invitation->message = null;

        $this->assertNull($invitation->message);
    }

    public function test_message_field_can_store_long_text()
    {
        $invitation = new ProjectInvitation;
        $longMessage = 'This is a very long invitation message that explains the project in detail and provides comprehensive information about what the invited user will be working on and their expected responsibilities within the research collaboration.';

        $invitation->message = $longMessage;
        $this->assertEquals($longMessage, $invitation->message);
    }

    public function test_invited_by_field_stores_user_id()
    {
        $invitation = new ProjectInvitation;

        $userIds = [1, 42, 1000, 99999];

        foreach ($userIds as $userId) {
            $invitation->invited_by = $userId;
            $this->assertEquals($userId, $invitation->invited_by);
        }
    }

    public function test_it_can_be_created_with_project_relationship()
    {
        $project = Project::factory()->create();

        $invitation = new ProjectInvitation([
            'email' => 'colleague@university.edu',
            'role' => 'contributor',
            'message' => 'Join our research project!',
            'invited_by' => 1,
        ]);

        $invitation->project_id = $project->id;
        $invitation->save();

        $this->assertEquals($project->id, $invitation->project_id);
        $this->assertEquals('colleague@university.edu', $invitation->email);
        $this->assertEquals('contributor', $invitation->role);
    }

    public function test_project_invitation_model_uses_correct_table()
    {
        $invitation = new ProjectInvitation;
        $this->assertEquals('project_invitations', $invitation->getTable());
    }
}
