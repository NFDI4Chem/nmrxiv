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
                $projects = Project::with('users', 'owner')->where('team_id', $team->id)->get();
            } else {
                $projects = Project::with('users', 'owner')->where('owner_id', $user->id)
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

        $projects = $user->sharedProjects;

        $projects->load('owner');

        return Inertia::render('SharedWithMe', [
            'projects' => $projects,
        ]);
    }

    public function trashed(Request $request)
    {
        $user = $request->user();
        $projects = Project::where('owner_id', $user->id)
            ->where('is_archived', true)
            ->get();

        return Inertia::render('Trashed', [
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

        $projects = $user->projects->load('owner');

        foreach($user->allTeams() as $team){
            $projects = $projects->concat($team->projects->load('owner'));
        }

        $projects = $projects->unique()->sortByDesc(function ($project) {
            return $project['updated_at'];
        })->values();

        return Inertia::render('Recent', [
            'team' => $team,
            'projects' => $projects,
        ]);
    }
}