<?php

namespace App\Console\Commands;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProjectStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:update-project-statuses {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-time command to update project status column based on existing flags (is_deleted, is_archived, is_public, release_date)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('🚀 LIVE MODE - Project statuses will be updated');
        }

        $this->newLine();

        return DB::transaction(function () use ($isDryRun) {
            $now = Carbon::now();
            $updatedCount = 0;

            // Get all projects with their current data
            $projects = Project::all();
            $this->info("📊 Found {$projects->count()} total projects to analyze");

            // Track updates by status type
            $statusCounts = [
                'deleted' => 0,
                'archived' => 0,
                'embargo' => 0,
                'published' => 0,
                'draft' => 0,
            ];

            foreach ($projects as $project) {
                $newStatus = $this->determineProjectStatus($project, $now);
                $oldStatus = $project->status;

                if ($newStatus !== $oldStatus) {
                    $this->info("📝 Project {$project->id} ({$project->name}): '{$oldStatus}' → '{$newStatus}'");

                    if (! $isDryRun) {
                        $project->update(['status' => $newStatus]);
                    }

                    $updatedCount++;
                    $statusCounts[$newStatus]++;
                } else {
                    $this->line("✓ Project {$project->id} already has correct status: '{$newStatus}'");
                }
            }

            $this->newLine();
            $this->info('📈 Status Distribution:');
            foreach ($statusCounts as $status => $count) {
                if ($count > 0) {
                    $this->line("  • {$status}: {$count} projects");
                }
            }

            $this->newLine();
            if ($isDryRun) {
                $this->info("🔍 DRY RUN COMPLETE: {$updatedCount} projects would be updated");
                $this->info('💡 Run without --dry-run to apply changes');
            } else {
                $this->info("✅ SUCCESS: Updated {$updatedCount} project statuses");
            }

            return 0;
        });
    }

    /**
     * Determine the correct status for a project based on its flags
     */
    private function determineProjectStatus(Project $project, Carbon $now): string
    {
        // Priority order based on business logic:

        // 1. If deleted, status is 'deleted'
        if ($project->is_deleted) {
            return 'deleted';
        }

        // 2. If archived, status is 'archived'
        if ($project->is_archived) {
            return 'archived';
        }

        // 3. If public, status is 'published'
        if ($project->is_public) {
            return 'published';
        }

        // 4. If has future release_date, status is 'embargo'
        if ($project->release_date && Carbon::parse($project->release_date)->isAfter($now)) {
            return 'embargo';
        }

        // 5. Default to 'draft' for everything else
        return 'draft';
    }
}
