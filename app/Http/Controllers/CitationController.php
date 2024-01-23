<?php

namespace App\Http\Controllers;

use App\Actions\Project\UpdateProject;
use App\Models\Citation;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CitationController extends Controller
{
    /**
     * Save and sync updated citation details for a project.
     *
     * @param  \Actions\Project\UpdateProject  $updater
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, UpdateProject $updater, Project $project)
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }

        $user = $request->user();
        $citations = $request->get('citations');
        //dd($citations);
        if (count($citations) > 0) {
            $processedCitations = [];

            foreach ($citations as $citation) {
                $title = $citation['title'];

                Validator::make($citation, [
                    'title' => ['required', 'string'],
                    'authors' => ['required', 'string'],
                ])->validate();

                if (! is_null($title)) {
                    $_citation = $project->citations->filter(function ($a) use ($title) {
                        return $title === $a->title;
                    })->first();
                    //dd($_author);
                    if ($_citation) {
                        $_citation->update([
                            'doi' => array_key_exists('doi', $citation) ? $citation['doi'] : null,
                            'title' => array_key_exists('title', $citation) ? $citation['title'] : null,
                            'authors' => array_key_exists('authors', $citation) ? $citation['authors'] : null,
                            'citation_text' => array_key_exists('citation_text', $citation) ? $citation['citation_text'] : null,
                        ]);
                    } else {
                        $_citation = Citation::create([
                            'doi' => array_key_exists('doi', $citation) ? $citation['doi'] : null,
                            'title' => array_key_exists('title', $citation) ? $citation['title'] : null,
                            'authors' => array_key_exists('authors', $citation) ? $citation['authors'] : null,
                            'citation_text' => array_key_exists('citation_text', $citation) ? $citation['citation_text'] : null,
                        ]);
                    }
                    array_push($processedCitations, $_citation);
                }
            }
            $updater->syncCitations($project, $processedCitations, $user);

            return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Citation updated successfully');
        }
    }

    /**
     * Delete citation for a project.
     *
     * @param  \Actions\Project\UpdateProject  $updater
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, UpdateProject $updater, Project $project)
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }

        $citation = $request->get('citations');

        if (count($citation) > 0) {
            $updater->detachCitation($project, $citation[0]['id']);
        }

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Citation deleted successfully');
    }
}
