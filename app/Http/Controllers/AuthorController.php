<?php

namespace App\Http\Controllers;

use App\Actions\Project\UpdateProject;
use App\Models\Author;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthorController extends Controller
{
    public function getAuthorsbyDOI(Request $request)
    {
        $doi = $request->get('doi');
        $getDetailsbyDOIApi = config('app.get_details_by_DOI_api');
        $url = $getDetailsbyDOIApi.'/'.$doi;
        $response = Http::get($url, [
        ]);
        $authorsList = [];
        $author = null;
        $response = json_decode($response, true);
        if ($response && $response['status'] == 'ok') {
            $authorRes = $response['message']['author'];
            foreach ($authorRes as $item) {
                $author['given_name'] = array_key_exists('given', $item) ? $item['given'] : null;
                $author['family_name'] = array_key_exists('family', $item) ? $item['family'] : null;
                $author['affiliation'] = array_key_exists('affiliation', $item) ? $item['affiliation'][0]['name'] : null;
                $author['orcid_id'] = array_key_exists('ORCID', $item) ? $item['ORCID'] : null;
                array_push($authorsList, $author);
            }

            return $authorsList;
        } else {
            return null;
        }
    }

    public function addAuthor(Request $request, UpdateProject $updater, Project $project)
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
        $updater->updateAuthor($project, $authors);

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Authors updated successfully');
    }
}
