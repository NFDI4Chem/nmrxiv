<?php

namespace Tests\Unit\Models;

use App\Models\EmbargoReminder;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EmbargoReminderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_expected_fillable_attributes(): void
    {
        $reminder = new EmbargoReminder;

        $this->assertSame([
            'project_id',
            'days_before_release',
            'sent_at',
        ], $reminder->getFillable());
    }

    public function test_it_casts_sent_at_to_datetime(): void
    {
        $reminder = EmbargoReminder::create([
            'project_id' => Project::factory()->create()->id,
            'days_before_release' => 7,
            'sent_at' => '2026-05-21 12:00:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $reminder->sent_at);
    }

    public function test_it_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $reminder = EmbargoReminder::create([
            'project_id' => $project->id,
            'days_before_release' => 3,
            'sent_at' => now(),
        ]);

        $this->assertInstanceOf(BelongsTo::class, $reminder->project());
        $this->assertTrue($reminder->project->is($project));
    }
}
