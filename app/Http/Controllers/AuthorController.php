<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Actions\Project\UpdateProject;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorController extends Controller
{
    /**
     * Save and sync updated author details for a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Actions\Project\UpdateProject  $updater
     * @param  \App\Models\Project  $project
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, UpdateProject $updater, Project $project)
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }
        
        $authors = $request->get('authors');
        if (count($authors) > 0) {
            $processedAuthors = [];

            foreach ($authors as $author) {
                $family_name = $author['family_name'];
                $given_name = $author['given_name'];

                Validator::make($author, [
                    'given_name' => ['required', 'string', 'max:255'],
                    'family_name' => ['required', 'string', 'max:255'],
                ])->validate();

                if (! is_null($family_name) && ! is_null($given_name)) {
                    $_author = $project->authors->filter(function ($a) use ($family_name, $given_name) {
                        return $family_name.$given_name === $a->family_name.$a->given_name;
                    })->first();
                    //dd($_author);
                    if ($_author) {
                        $_author->update([
                            'title' => array_key_exists('title', $author) ? $author['title'] : null,
                            'given_name' => $given_name,
                            'family_name' => $family_name,
                            'orcid_id' => array_key_exists('orcid_id', $author) ? $author['orcid_id'] : null,
                            'email_id' => array_key_exists('email_id', $author) ? $author['email_id'] : null,
                            'affiliation' => array_key_exists('affiliation', $author) ? $author['affiliation'] : null,
                        ]);
                    } else {
                        $_author = Author::create([
                            'title' => array_key_exists('title', $author) ? $author['title'] : null,
                            'given_name' => $given_name,
                            'family_name' => $family_name,
                            'orcid_id' => array_key_exists('orcid_id', $author) ? $author['orcid_id'] : null,
                            'email_id' => array_key_exists('email_id', $author) ? $author['email_id'] : null,
                            'affiliation' => array_key_exists('affiliation', $author) ? $author['affiliation'] : null,
                        ]);
                    }
                    $_author->contributor_type = array_key_exists('contributor_type', $author) ? $author['contributor_type'] : 'Researcher';
                    array_push($processedAuthors, $_author);
                }
            }
            $updater->attachAuthor($project, $processedAuthors);
        }

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Authors updated successfully');
    }

    /**
     * Delete author for a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Actions\Project\UpdateProject  $updater
     * @param  \App\Models\Project  $project
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, UpdateProject $updater, Project $project)
    {
        $authors = $request->get('authors');

        if (count($authors) > 0) {
            $updater->detachAuthor($project, $authors[0]['id']);

            // #todo: check if the author is already associated with any other project and if not delete the author
        }

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Author deleted successfully');
    }

    /**
     * Update existing Contributor type for a given author in a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Actions\Project\UpdateProject  $updater
     * @param  \App\Models\Project  $project
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRole(Request $request, UpdateProject $updater, Project $project)
    {
        $contributorTypes = Config::get('doi.'.Config::get('doi.default').'.contributor_types');
        $roleExist = in_array($request->role, $contributorTypes);

        if ($roleExist && $request->author_id && $request->role) {
            $updater->updateContributorType($project, $request->author_id, $request->role);

            return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Author updated successfully');
        } else {
            return $request->wantsJson() ? new JsonResponse('', 500) : back()->with('error', 'Error occurred while updating Author role');
        }
    }
}
