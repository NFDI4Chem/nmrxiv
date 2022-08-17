<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Storage;

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
            $nmriumInfo = $spectra;
            $molecules = $request->get('molecules');
            $molecularInfo = $molecules;

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
            foreach ($spectra as $spectrum) {
                $nucleus = $spectrum['info']['nucleus'];
                if (is_array($nucleus)) {
                    $nucleus = implode('-', $nucleus);
                }
                $dataset->type = $nucleus.', '.$dataset->type;
            }

            $dataset->save();

            return $dataset->fresh();
        }
    }

    public function publicDatasetsView(Request $request)
    {
        $datasets = Cache::rememberForever('datasets', function () {
            return DatasetResource::collection(Dataset::with('project')->where('is_public', true)->orderByDesc('updated_at')->paginate(15));
        });

        return Inertia::render('Public/Datasets', [
            'datasets' => $datasets,
        ]);
    }

    public function preview(Request $request, Dataset $dataset)
    {
        $content = $request->get('img');
        $study = $dataset->study;
        if ($content) {
            $path = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/'.$dataset->slug.'.svg';
            Storage::disk('minio_public')->put($path, $content);
            $dataset->dataset_photo_path = $path;
            $dataset->save();
        }
    }
}
