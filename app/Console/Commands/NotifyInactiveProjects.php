<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectInactivityReminderNotification;
use App\Notifications\ProjectInactivityReportToAdmins;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NotifyInactiveProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:notify-inactive-projects {--months= : Number of months of inactivity (defaults to config inactivity.grace_months)} {--list : Only list inactive projects without notifying} {--report-admins : Email admins a report of inactive projects}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify project owners if the project has had no updates for N months (default 6).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = (int) ($this->option('months') ?? config('inactivity.grace_months', 6));
        $threshold = Carbon::now()->subMonths($months);

        return DB::transaction(function () use ($threshold) {
            // Identify inactive projects and mark them inactive
            $projects = Project::with('owner', 'users')
                ->where('is_public', false)
                ->where('is_deleted', false)
                ->where('is_archived', false)
                ->where('updated_at', '<', $threshold)
                ->get();

            if ($this->option('list')) {
                $this->table(['ID', 'Name', 'Owner Email', 'Updated At'], $projects->map(function ($p) {
                    return [$p->id, $p->name, optional($p->owner)->email, (string) $p->updated_at];
                })->toArray());
                $this->info('Total inactive projects: '.count($projects));

                return self::SUCCESS;
            }

            // Mark these projects as inactive in DB (idempotent) without touching updated_at
            if ($projects->count() > 0) {
                $ids = $projects->pluck('id');
                Project::withoutTimestamps(function () use ($ids) {
                    Project::whereIn('id', $ids)->update(['active' => false]);
                });
            }

            // Aggregate by recipient so each user gets a single digest listing all of their inactive projects
            $recipientProjects = [];
            foreach ($projects as $project) {
                foreach ($this->prepareSendList($project) as $recipient) {
                    $recipientProjects[$recipient->id]['user'] = $recipient;
                    $recipientProjects[$recipient->id]['projects'][] = $project;
                }
            }

            // Send one email per recipient with their list of inactive projects
            $sentCount = 0;
            foreach ($recipientProjects as $entry) {
                /** @var \App\Models\User $user */
                $user = $entry['user'];
                $list = collect($entry['projects'])->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'updated_at' => (string) $p->updated_at,
                        'url' => url(config('app.url').'/dashboard/projects/'.$p->id),
                    ];
                })->values()->all();

                Notification::send($user, new ProjectInactivityReminderNotification($list));
                $sentCount++;
            }

            $this->info('Inactive project digests sent: '.$sentCount.' (covering '.count($projects).' projects)');

            if ($this->option('report-admins') && $projects->count() > 0) {
                $payload = $projects->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'owner' => optional($p->owner)->email ?? 'N/A',
                        'updated_at' => (string) $p->updated_at,
                    ];
                })->values()->all();

                Notification::send(User::role(['super-admin'])->get(), new ProjectInactivityReportToAdmins($payload));
                $this->info('Admin report sent to super-admins.');
            }

            return self::SUCCESS;
        });
    }

    /**
     * Prepare recipients list (owner and creators).
     */
    protected function prepareSendList(Project $project): array
    {
        $sendTo = [];
        $add = function ($user) use (&$sendTo) {
            if ($user && isset($user->id)) {
                $sendTo[$user->id] = $user;
            }
        };

        foreach ($project->allUsers() as $member) {
            if ($member->projectMembership->role == 'creator' || $member->projectMembership->role == 'owner') {
                $add($member);
            }
        }

        $add($project->owner);

        return array_values($sendTo);
    }
}
