<?php

namespace Tests\Unit\Models;

use App\Models\Study;
use App\Models\StudyInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudyInvitationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_study(): void
    {
        $study = Study::factory()->create();
        $invitation = new StudyInvitation;
        $invitation->study_id = $study->id;
        $invitation->email = 'test@example.com';
        $invitation->role = 'collaborator';
        $invitation->save();

        $this->assertInstanceOf(Study::class, $invitation->study);
        $this->assertEquals($study->id, $invitation->study->id);
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'study_id',
            'email',
            'role',
            'message',
            'invited_by',
        ];

        $invitation = new StudyInvitation;
        $this->assertEquals($fillable, $invitation->getFillable());
    }

    public function test_model_uses_correct_table(): void
    {
        $invitation = new StudyInvitation;
        $this->assertEquals('study_invitations', $invitation->getTable());
    }

    public function test_model_has_correct_relationships(): void
    {
        $invitation = new StudyInvitation;
        $this->assertTrue(method_exists($invitation, 'study'));
    }

    public function test_it_can_be_created_with_fillable_attributes(): void
    {
        $study = Study::factory()->create();

        $invitationData = [
            'study_id' => $study->id,
            'email' => 'collaborator@example.com',
            'role' => 'viewer',
            'message' => 'Please join our research study',
            'invited_by' => 1,
        ];

        $invitation = StudyInvitation::create($invitationData);

        $this->assertInstanceOf(StudyInvitation::class, $invitation);
        $this->assertEquals('collaborator@example.com', $invitation->email);
        $this->assertEquals('viewer', $invitation->role);
        $this->assertEquals('Please join our research study', $invitation->message);
        $this->assertEquals(1, $invitation->invited_by);
        $this->assertEquals($study->id, $invitation->study_id);
    }

    public function test_it_stores_email_correctly(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'researcher@university.edu',
            'role' => 'collaborator',
        ]);

        $this->assertEquals('researcher@university.edu', $invitation->email);
    }

    public function test_it_stores_role_correctly(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        $this->assertEquals('admin', $invitation->role);
    }

    public function test_it_stores_message_correctly(): void
    {
        $study = Study::factory()->create();
        $message = 'Welcome to our NMR spectroscopy research project. We would love to have you as a collaborator.';

        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'collaborator',
            'message' => $message,
        ]);

        $this->assertEquals($message, $invitation->message);
    }

    public function test_it_stores_invited_by_correctly(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'viewer',
            'invited_by' => 42,
        ]);

        $this->assertEquals(42, $invitation->invited_by);
    }

    public function test_it_can_handle_null_message(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'collaborator',
            'message' => null,
        ]);

        $this->assertNull($invitation->message);
    }

    public function test_it_can_handle_null_invited_by(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'collaborator',
            'invited_by' => null,
        ]);

        $this->assertNull($invitation->invited_by);
    }

    public function test_multiple_invitations_for_same_study(): void
    {
        $study = Study::factory()->create();

        $invitation1 = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'user1@example.com',
            'role' => 'collaborator',
        ]);

        $invitation2 = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'user2@example.com',
            'role' => 'viewer',
        ]);

        $this->assertEquals($study->id, $invitation1->study_id);
        $this->assertEquals($study->id, $invitation2->study_id);
        $this->assertNotEquals($invitation1->id, $invitation2->id);
    }

    public function test_invitation_with_different_roles(): void
    {
        $study = Study::factory()->create();

        $roles = ['admin', 'collaborator', 'viewer', 'editor'];

        foreach ($roles as $role) {
            $invitation = StudyInvitation::create([
                'study_id' => $study->id,
                'email' => "user-{$role}@example.com",
                'role' => $role,
            ]);

            $this->assertEquals($role, $invitation->role);
        }
    }

    public function test_it_has_timestamps(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'collaborator',
        ]);

        $this->assertNotNull($invitation->created_at);
        $this->assertNotNull($invitation->updated_at);
        $this->assertInstanceOf(Carbon::class, $invitation->created_at);
        $this->assertInstanceOf(Carbon::class, $invitation->updated_at);
    }

    public function test_it_can_be_updated(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'original@example.com',
            'role' => 'viewer',
            'message' => 'Original message',
        ]);

        $invitation->update([
            'email' => 'updated@example.com',
            'role' => 'collaborator',
            'message' => 'Updated message',
        ]);

        $invitation->refresh();

        $this->assertEquals('updated@example.com', $invitation->email);
        $this->assertEquals('collaborator', $invitation->role);
        $this->assertEquals('Updated message', $invitation->message);
    }

    public function test_it_can_be_deleted(): void
    {
        $study = Study::factory()->create();
        $invitation = StudyInvitation::create([
            'study_id' => $study->id,
            'email' => 'test@example.com',
            'role' => 'collaborator',
        ]);

        $invitationId = $invitation->id;
        $invitation->delete();

        $this->assertNull(StudyInvitation::find($invitationId));
    }

    public function test_relationship_returns_correct_study(): void
    {
        $study1 = Study::factory()->create(['name' => 'Study One']);
        $study2 = Study::factory()->create(['name' => 'Study Two']);

        $invitation = StudyInvitation::create([
            'study_id' => $study1->id,
            'email' => 'test@example.com',
            'role' => 'collaborator',
        ]);

        $relatedStudy = $invitation->study;

        $this->assertEquals($study1->id, $relatedStudy->id);
        $this->assertEquals('Study One', $relatedStudy->name);
        $this->assertNotEquals($study2->id, $relatedStudy->id);
    }
}
