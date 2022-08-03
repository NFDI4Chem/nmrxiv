<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DatasetController extends Controller
{
    //
    public function publicDatasetView(Request $request, $slug)
    {
        $dataset = Dataset::where('slug', $slug)->firstOrFail();

        if ($dataset->is_public) {
            return Inertia::render('Public/Dataset', [
                'dataset' => $dataset,
            ]);
        }
    }

    public function nmriumInfo(Request $request, Dataset $dataset)
    {
        if ($dataset) {
            $spectra = $request->get('spectra');
            $nmriumInfo = json_encode($spectra);
            $molecules = $request->get('molecules');
            $molecularInfo = json_encode($molecules);
            if ($dataset->nmriumInfo) {
                $nmriumData = $dataset->nmriumInfo;
            } else {
                $nmriumData = [];
            }
            if ($nmriumInfo) {
                $nmriumData['spectra'] = $nmriumInfo;
            }
            if ($molecularInfo) {
                $nmriumData['molecules'] = $molecularInfo;
            }
            $dataset->nmrium_info = $nmriumData;
            $dataset->type = $spectra['info']['nucleus'];
            $dataset->save();

            return $dataset->fresh();
        }
    }

    public function publicDatasetsView(Request $request)
    {
        $datasets = Dataset::where('is_public', true)->simplePaginate(15);

        return Inertia::render('Public/Datasets', [
            'datasets' => $datasets,
        ]);
    }
}
