<?php

namespace App\Http\Controllers;

use App\Actions\Project\InviteProjectMember;
use App\Actions\Project\RemoveProjectMember;
use App\Actions\Project\UpdateProjectMemberRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectMemberController extends Controller
{
    /**
     * Add a new team member to a project.
     *
     * @param  int  $projectId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function memberStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        app(InviteProjectMember::class)->invite(
            $request->user(),
            $project,
            $request->email ?: '',
            $request->role,
            $request->message
        );

        return back(303);
    }

    /**
     * Update the given project member's role.
     *
     * @param  int  $projectId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMemberRole(Request $request, $projectId, $userId)
    {
        app(UpdateProjectMemberRole::class)->update(
            $request->user(),
            Project::findOrFail($projectId),
            $userId,
            $request->role
        );

        return back(303);
    }

    /**
     * Remove the given user from the given project.
     *
     * @param  int  $projectId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(Request $request, $projectId, $userId)
    {
        $project = Project::findOrFail($projectId);

        app(RemoveProjectMember::class)->remove(
            $request->user(),
            $project,
            $user = User::find($userId)
        );

        if ($request->user()->id === $user->id) {
            return redirect(config('fortify.home'));
        }

        return back(303);
    }
}
