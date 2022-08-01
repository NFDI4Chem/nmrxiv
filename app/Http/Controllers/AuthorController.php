<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Author;
use App\Models\Project;
use App\Actions\Project\UpdateProject;

class AuthorController extends Controller
{
    public function getDetailsbyDOI(Request $request){

        $doi = $request->get('doi');
        $getDetailsbyDOIApi = config('app.get_details_by_DOI_api');
        $url = $getDetailsbyDOIApi . "/" . $doi;
        $response = Http::get($url, [
        ]);
        $authorsList =array();
        $author = null;
        $response = json_decode($response, true);
        if($response && $response['status'] == 'ok'){
            $authorRes = $response['message']['author'];
            foreach($authorRes as $item){
                $author['givenName'] = array_key_exists('given', $item) ? $item['given']:null;
                $author['familyName'] = array_key_exists('family', $item) ? $item['family']:null;
                $author['affiliation']  = array_key_exists('affiliation', $item) ? $item['affiliation'][0]['name']:null;
                $author['orcidId']   = array_key_exists('ORCID', $item) ? $item['ORCID']:null;
                array_push($authorsList, $author);
            }
            return $authorsList;
        } else {
            return null;
        }
    }

    public function addAuthor(Request $request, UpdateProject $updater, Project $project){
        //dd($request);
        $input = $request->selectedAuthorsList;
        //DB transaction

        foreach($input as $item){
                $author = Author::create([
                    'orcid_id' => array_key_exists('orcidId', $item) ? $item['orcidId'] : null,
                    'given_name' => array_key_exists('givenName', $item) ? $item['givenName'] : null,
                    'family_name' => array_key_exists('familyName', $item) ? $item['familyName'] : null,
                    'email_id' => array_key_exists('email', $item) ? $item['email'] : null,
                    'affiliation' => array_key_exists('affiliation', $item) ? $item['affiliation'] : null,
                ]);
        }
        $updater->updateAuthor($project, $input);

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Authors added successfully');
    }
}
