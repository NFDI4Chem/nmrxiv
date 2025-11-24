<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifyInactiveProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactivity_email_is_sent_for_old_projects(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'updated_at' => Carbon::now()->subMonths(7),
            'is_deleted' => false,
            'is_archived' => false,
        ]);

        // Run the command
        Artisan::call('nmrxiv:notify-inactive-projects');

        Notification::assertSentTo(
            [$owner],
            \App\Notifications\ProjectInactivityReminderNotification::class
        );
    }

    public function test_recent_projects_are_not_notified(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'updated_at' => Carbon::now()->subMonths(2),
            'is_deleted' => false,
            'is_archived' => false,
        ]);

        Artisan::call('nmrxiv:notify-inactive-projects');

        Notification::assertNothingSent();
    }

    public function test_archived_or_deleted_projects_are_not_notified(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        Project::factory()->create([
            'owner_id' => $owner->id,
            'updated_at' => Carbon::now()->subMonths(12),
            'is_deleted' => true,
            'is_archived' => false,
        ]);

        Project::factory()->create([
            'owner_id' => $owner->id,
            'updated_at' => Carbon::now()->subMonths(12),
            'is_deleted' => false,
            'is_archived' => true,
        ]);

        Artisan::call('nmrxiv:notify-inactive-projects');

        Notification::assertNothingSent();
    }
}
