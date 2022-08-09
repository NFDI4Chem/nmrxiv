<?php

namespace App\Http\Controllers;

use App\Actions\Project\UpdateProject;
use App\Models\Author;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function updateAuthor(Request $request, UpdateProject $updater, Project $project)
    {
        $input = $request->selectedAuthorsList;
        //DB transaction
        $authors = [];

        foreach ($input as $item) {
            $author = Author::firstOrCreate([
                'given_name' => array_key_exists('given_name', $item) ? $item['given_name'] : null,
                'family_name' => array_key_exists('family_name', $item) ? $item['family_name'] : null,
            ],
                ['orcid_id' => array_key_exists('orcid_id', $item) ? $item['orcid_id'] : null,
                    'email_id' => array_key_exists('email_id', $item) ? $item['email_id'] : null,

                    'affiliation' => array_key_exists('affiliation', $item) ? $item['affiliation'] : null,
                ]);
            array_push($authors, $author->id);
        }
        $updater->updateAuthors($project, $authors);

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Authors updated successfully');
    }
}
