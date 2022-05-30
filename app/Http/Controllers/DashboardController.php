<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        if ($team) {
            $team->users = $team->allUsers();
            if (!$team->personal_team) {
                $projects = Project::where('team_id', $team->id)->get();
            } else {
                $projects = Project::where('owner_id', $user->id)
                    ->where('team_id', $team->id)
                    ->get();
            }
        }

        return Inertia::render('Dashboard', [
            'team' => $team,
            'projects' => $projects,
            'teamRole' => $user->teamRole($team)
        ]);
    }

    public function sharedWithMe(Request $request)
    {
        $user = $request->user();

        $projects = $user->projects;

        $projects->load('owner');

        return Inertia::render('SharedWithMe', [
            'projects' => $projects,
        ]);
    }

    public function archive(Request $request)
    {
        $user = $request->user();
        $projects = Project::where('owner_id', $user->id)
            ->where('is_archived', true)
            ->get();

        return Inertia::render('Archive', [
            'projects' => $projects,
        ]);
    }

    public function starred(Request $request)
    {
        $user = $request->user();

        $projects = Project::whereHasLike(auth()->user())->get();

        return Inertia::render('Starred', [
            'projects' => $projects,
        ]);
    }

    public function recent(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        if ($team) {
            $team->users = $team->allUsers();
            if (!$team->personal_team) {
                $projects = Project::where('team_id', $team->id)
                    ->orderBy('updated_at', 'DESC')
                    ->get()
                    ->merge($user->recentProjects)
                    ->load('owner');
            } else {
                $projects = Project::where('owner_id', $user->id)
                    ->where('team_id', $team->id)
                    ->orderBy('updated_at', 'DESC')
                    ->get()
                    ->merge($user->recentProjects)
                    ->load('owner');
            }
        }

        // $projects = $projects->sortByDesc('updated_at');

        // $projects->all();

        return Inertia::render('Recent', [
            'team' => $team,
            'projects' => $projects,
        ]);
    }
}
