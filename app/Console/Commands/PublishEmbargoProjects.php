<?php

namespace App\Console\Commands;

use App\Actions\Project\PublishEmbargoProject;
use App\Models\EmbargoReminder;
use App\Models\Project;
use App\Notifications\EmbargoReleaseReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class PublishEmbargoProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:publish-embargo-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send embargo release reminders and publish embargoed projects if the release date is reached';

    /**
     * Execute the console command.
     */
    public function handle(PublishEmbargoProject $embargoPublisher): int
    {
        $now = Carbon::now();
        $remindersSent = 0;

        // Send 1 week reminders
        $this->sendReminders($now->copy()->addDays(7), 7, $remindersSent);

        // Send 3 days reminders
        $this->sendReminders($now->copy()->addDays(3), 3, $remindersSent);

        // Send 1 day reminders
        $this->sendReminders($now->copy()->addDays(1), 1, $remindersSent);

        // Publish projects that have reached their release date
        $publishedCount = $this->publishReadyProjects($embargoPublisher, $now);

        // Clean up old reminder records for published projects
        $this->cleanupOldReminders();

        $this->info("Sent {$remindersSent} embargo release reminders.");
        $this->info("Published {$publishedCount} embargo projects.");

        return Command::SUCCESS;
    }

    /**
     * Send reminder notifications for projects approaching release date
     */
    private function sendReminders(Carbon $targetDate, int $daysUntilRelease, int &$remindersSent): void
    {
        $projects = Project::where([
            ['status', 'embargo'],
            ['is_public', false],
            ['is_archived', false],
        ])->whereNotNull('release_date')
            ->whereNotNull('identifier')
            ->whereDate('release_date', $targetDate->toDateString())
            ->whereDoesntHave('embargoReminders', function ($query) use ($daysUntilRelease) {
                $query->where('days_before_release', $daysUntilRelease);
            })
            ->with('owner')
            ->get();

        foreach ($projects as $project) {
            try {
                $this->info("Sending {$daysUntilRelease} day reminder for project: {$project->name} (ID: {$project->id})");

                // Send reminder notification
                Notification::send($project->owner, new EmbargoReleaseReminderNotification($project, $daysUntilRelease));

                // Record that reminder was sent
                EmbargoReminder::create([
                    'project_id' => $project->id,
                    'days_before_release' => $daysUntilRelease,
                    'sent_at' => Carbon::now(),
                ]);

                $remindersSent++;
                $this->info("Successfully sent {$daysUntilRelease} day reminder for: {$project->name}");

            } catch (\Exception $e) {
                $this->error("Failed to send reminder for project {$project->name}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Publish projects that have reached their release date
     */
    private function publishReadyProjects(PublishEmbargoProject $embargoPublisher, Carbon $now): int
    {
        $this->info("Looking for projects to publish with release_date <= {$now->toDateString()}");

        $projects = Project::where([
            ['status', 'embargo'],
            ['is_public', false],
            ['is_archived', false],
        ])->whereNotNull('release_date')
            ->whereNotNull('identifier')
            ->whereDate('release_date', '<=', $now->toDateString())
            ->get();

        $this->info("Found {$projects->count()} projects eligible for publishing");

        $publishedCount = 0;

        foreach ($projects as $project) {
            try {
                $this->info("Publishing embargo project: {$project->name} (ID: {$project->id})");

                $result = $embargoPublisher->publish($project);

                $publishedCount++;
                $this->info("Successfully queued project for publication: {$project->name}");

            } catch (\InvalidArgumentException $e) {
                $this->error("Failed to publish project {$project->name}: {$e->getMessage()}");
            } catch (\Exception $e) {
                $this->error("Unexpected error publishing project {$project->name}: {$e->getMessage()}");
            }
        }

        return $publishedCount;
    }

    /**
     * Clean up old embargo reminder records for projects that are no longer embargoed
     */
    private function cleanupOldReminders(): void
    {
        $deletedCount = EmbargoReminder::whereHas('project', function ($query) {
            $query->where('status', '!=', 'embargo')
                ->orWhere('is_public', true)
                ->orWhere('is_archived', true);
        })->delete();

        if ($deletedCount > 0) {
            $this->info("Cleaned up {$deletedCount} old embargo reminder records.");
        }
    }
}
