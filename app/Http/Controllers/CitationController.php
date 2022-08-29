<?php

namespace App\Http\Controllers;

use App\Actions\Project\UpdateProject;
use App\Models\Citation;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitationController extends Controller
{
    public function updateCitation(Request $request, UpdateProject $updater, Project $project)
    {
        $input = $request->citationList;
        $user = $request->user();
        //dd($request->citationList);
        //DB transaction
        $citations = [];

        foreach ($input as $item) {
            $citation = Citation::create([
                'doi' => array_key_exists('doi', $item) ? $item['doi'] : null,
                'title' => array_key_exists('title', $item) ? $item['title'] : null,
                'authors' => array_key_exists('authors', $item) ? $item['authors'] : null,
                'abstract' => array_key_exists('abstract', $item) ? $item['abstract'] : null,
                'citation_text' => array_key_exists('citation_text', $item) ? $item['citation_text'] : null,
            ]);
            array_push($citations, $citation->id);
        }
        $updater->updateCitation($project, $citations, $user);

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Citation updated successfully');
    }
}
