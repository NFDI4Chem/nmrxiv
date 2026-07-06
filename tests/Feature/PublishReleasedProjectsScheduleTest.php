<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class PublishReleasedProjectsScheduleTest extends TestCase
{
    public function test_publish_embargo_projects_runs_daily_at_midnight(): void
    {
        $event = collect(app(Schedule::class)->events())->first(
            fn ($event) => str_contains($event->command ?? '', 'nmrxiv:publish-embargo-projects')
        );

        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
    }
}
