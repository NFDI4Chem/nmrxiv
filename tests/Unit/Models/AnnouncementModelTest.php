<?php

namespace Tests\Unit\Models;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_owner(): void
    {
        $user = User::factory()->create();
        $announcement = Announcement::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $announcement->owner);
        $this->assertEquals($user->id, $announcement->owner->id);
    }

    public function test_it_has_correct_fillable_attributes(): void
    {
        $fillable = [
            'title',
            'message',
            'status',
            'start_time',
            'end_time',
            'user_id',
        ];

        $announcement = new Announcement;
        $this->assertEquals($fillable, $announcement->getFillable());
    }

    public function test_it_can_get_active_announcements(): void
    {
        $now = Carbon::now();

        // Create active announcement (current time within start/end range)
        $activeAnnouncement = Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->subDays(1),
            'end_time' => $now->copy()->addDays(1),
        ]);

        // Create inactive announcement (status inactive)
        Announcement::factory()->create([
            'status' => 'inactive',
            'start_time' => $now->copy()->subDays(1),
            'end_time' => $now->copy()->addDays(1),
        ]);

        // Create announcement that hasn't started yet
        Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->addDays(1),
            'end_time' => $now->copy()->addDays(2),
        ]);

        // Create announcement that has ended
        Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->subDays(2),
            'end_time' => $now->copy()->subDays(1),
        ]);

        $activeAnnouncements = Announcement::active();

        $this->assertCount(1, $activeAnnouncements);
        $this->assertEquals($activeAnnouncement->id, $activeAnnouncements->first()->id);
    }

    public function test_active_announcements_must_have_active_status(): void
    {
        $now = Carbon::now();

        // Create announcement with inactive status but valid time range
        Announcement::factory()->create([
            'status' => 'inactive',
            'start_time' => $now->copy()->subDays(1),
            'end_time' => $now->copy()->addDays(1),
        ]);

        $activeAnnouncements = Announcement::active();
        $this->assertCount(0, $activeAnnouncements);
    }

    public function test_active_announcements_must_be_within_time_range(): void
    {
        $now = Carbon::now();

        // Create announcement with active status but outside time range (future)
        Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->addDays(1),
            'end_time' => $now->copy()->addDays(2),
        ]);

        // Create announcement with active status but outside time range (past)
        Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->subDays(2),
            'end_time' => $now->copy()->subDays(1),
        ]);

        $activeAnnouncements = Announcement::active();
        $this->assertCount(0, $activeAnnouncements);
    }

    public function test_active_announcements_includes_current_time_boundaries(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        try {
            // Create announcement that starts exactly now
            $startsNow = Announcement::factory()->create([
                'status' => 'active',
                'start_time' => $now->copy(),
                'end_time' => $now->copy()->addDays(1),
            ]);

            // Create announcement that ends exactly now
            $endsNow = Announcement::factory()->create([
                'status' => 'active',
                'start_time' => $now->copy()->subDays(1),
                'end_time' => $now->copy(),
            ]);

            $activeAnnouncements = Announcement::active();
        } finally {
            Carbon::setTestNow();
        }

        $this->assertCount(2, $activeAnnouncements);
        $announcementIds = $activeAnnouncements->pluck('id')->toArray();
        $this->assertContains($startsNow->id, $announcementIds);
        $this->assertContains($endsNow->id, $announcementIds);
    }

    public function test_it_can_be_created_with_factory(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertInstanceOf(Announcement::class, $announcement);
        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'status' => $announcement->status,
        ]);
    }

    public function test_it_has_timestamps(): void
    {
        $announcement = Announcement::factory()->create();

        $this->assertNotNull($announcement->created_at);
        $this->assertNotNull($announcement->updated_at);
    }

    public function test_owner_relationship_returns_correct_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $announcement = Announcement::factory()->create(['user_id' => $user->id]);

        $owner = $announcement->owner;

        $this->assertEquals('John', $owner->first_name);
        $this->assertEquals('Doe', $owner->last_name);
        $this->assertEquals($user->id, $owner->id);
    }

    public function test_multiple_active_announcements_returned(): void
    {
        $now = Carbon::now();

        // Create multiple active announcements
        $announcement1 = Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->subDays(1),
            'end_time' => $now->copy()->addDays(1),
        ]);

        $announcement2 = Announcement::factory()->create([
            'status' => 'active',
            'start_time' => $now->copy()->subHours(2),
            'end_time' => $now->copy()->addHours(2),
        ]);

        $activeAnnouncements = Announcement::active();

        $this->assertCount(2, $activeAnnouncements);
        $announcementIds = $activeAnnouncements->pluck('id')->toArray();
        $this->assertContains($announcement1->id, $announcementIds);
        $this->assertContains($announcement2->id, $announcementIds);
    }
}
