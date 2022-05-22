<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;

class DatasetController extends Controller
{
    //
    public function publicDatasetView(Request $request, $slug)
    {
        $dataset = Project::where('slug', $slug)->firstOrFail();

        if($dataset->is_public){
            return Inertia::render('Public/Dataset', [
                'dataset' => $dataset
            ]);
        }
    }

    public function publicDatasetsView(Request $request)
    {
        $Datasets = Project::where('is_public', TRUE)->simplePaginate(15);

        return Inertia::render('Public/Datasets', [
            'datasets' => $Datasets
        ]);
    }
}
