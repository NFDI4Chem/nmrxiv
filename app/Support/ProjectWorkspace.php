<?php

namespace App\Support;

use App\Actions\License\GetLicense;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Jetstream;

final class ProjectWorkspace
{
    /**
     * Shared team, members, roles, and permission flags for anyone who may view the project workspace.
     * Null when unauthenticated or when the viewer fails the viewProject gate.
     *
     * @return array<string, mixed>|null
     */
    public static function memberWorkspaceContext(Request $request, Project $project, GetLicense $getLicense): ?array
    {
        $user = $request->user();
        if ($user === null || ! Gate::forUser($user)->check('viewProject', $project)) {
            return null;
        }

        $team = $project->nonPersonalTeam;
        $license = null;
        if ($project->license_id) {
            $license = $getLicense->getLicensebyId($project->license_id);
        }

        return [
            'team' => $team ? $team->load(['users', 'owner']) : null,
            'members' => $project->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'role' => $project->userProjectRole($user->email),
            'teamRole' => $user->belongsToTeam($team) ? $user->teamRole($team) : null,
            'license' => $license,
            'projectPermissions' => [
                'canDeleteProject' => Gate::forUser($user)->check('deleteProject', $project),
                'canUpdateProject' => Gate::forUser($user)->check('updateProject', $project),
                'canManageSettings' => Gate::forUser($user)->check('manageSettings', $project),
            ],
        ];
    }

    /**
     * Flat props merged into {@see ProjectController::show} (dashboard project home without a public id).
     *
     * @return array<string, mixed>
     */
    public static function dashboardShowCompanionProps(Request $request, Project $project, GetLicense $getLicense): array
    {
        return self::memberWorkspaceContext($request, $project, $getLicense) ?? [];
    }

    /**
     * Inertia props for authenticated project members on the unified public project UI.
     * Empty when the viewer is a guest or is not allowed to view the project.
     *
     * @return array<string, mixed>
     */
    public static function inertiaPropsForPublicProject(Request $request, Project $project, GetLicense $getLicense): array
    {
        $ctx = self::memberWorkspaceContext($request, $project, $getLicense);
        if ($ctx === null) {
            return [];
        }

        return [
            'workspace' => array_merge($ctx, [
                'preview' => false,
                'dashboardProject' => $project->load([
                    'projectInvitations',
                    'tags',
                    'authors',
                    'citations',
                    'fundingReferences',
                    'owner',
                    'draft',
                ]),
            ]),
        ];
    }
}
