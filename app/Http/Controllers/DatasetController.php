<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\User;
use Auth;
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

    public function fetchNMRium(Request $request, Dataset $dataset)
    {
        if ($dataset) {
            $nmrium = $dataset->nmrium;
            if ($nmrium) {
                return json_decode($nmrium->nmrium_info);
            } else {
                return null;
            }
        }
    }

    public function nmriumInfo(Request $request, Dataset $dataset)
    {
        if ($dataset) {
            $user = Auth::user();
            $data = $request->get('data');
            $version = $request->get('version');
            $spectra = $request->get('spectra');
            $molecules = $request->get('molecules');

            $nmriumInfo = $spectra;
            $molecularInfo = $molecules;

            $nmrium = $dataset->nmrium;
            if ($nmrium) {
                $nmriumData = json_decode($nmrium['nmrium_info'], true);
            } else {
                $nmriumData = [];
            }
            if ($nmriumInfo && ! empty($nmriumInfo)) {
                $nmriumData['spectra'] = $nmriumInfo;
            }
            if ($molecularInfo && ! empty($molecularInfo)) {
                $nmriumData['molecules'] = $molecularInfo;
            }

            if ($version && ! empty($version)) {
                $nmriumData['version'] = $version;
            }

            if (! empty($nmriumData)) {
                if ($nmrium) {
                    $nmrium->nmrium_info = $nmriumData;
                    $dataset->has_nmrium = true;
                    $nmrium->save();
                } else {
                    $nmrium = NMRium::create([
                        'nmrium_info' => json_encode($nmriumData),
                    ]);
                    $dataset->nmrium()->save($nmrium);
                    $dataset->has_nmrium = true;
                }

                foreach ($spectra as $spectrum) {
                    $experimentDetailsExists = array_key_exists('experiment', $spectrum['info']);
                    if ($experimentDetailsExists) {
                        $experiment = $spectrum['info']['experiment'];
                        $nucleus = $spectrum['info']['nucleus'];
                        if (is_array($nucleus)) {
                            $nucleus = implode('-', $nucleus);
                        }
                        $dataset->type = $experiment.','.$nucleus;
                    }
                }

                $dataset->save();

                return $dataset->fresh();
            }
        }
    }

    public function nmriumVersions(Request $request, Dataset $dataset)
    {
        if ($dataset) {
            $nmrium = $dataset->nmrium;

            if ($nmrium) {
                return $nmrium->versions()->orderBy('created_at', 'DESC')->get()->map(function ($version) {
                    $user = User::find($version->user_id);

                    return [
                        'updated_at' => $version->updated_at,
                        'user' => [
                            'name' => $user->first_name.' '.$user->last_name,
                            'profile_photo_url' => $user->profile_photo_url,
                        ],
                    ];
                });
            }
        }
    }

    public function publicDatasetsView(Request $request)
    {
        // $datasets = Cache::rememberForever('datasets', function () {
        $datasets = DatasetResource::collection(Dataset::with('study')->where([['is_public', true], ['is_archived', false]])->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString());
        // });

        return Inertia::render('Public/Datasets', [
            'filters' => $request->all('search', 'sort', 'mode'),
            'datasets' => $datasets,
        ]);
    }

    public function preview(Request $request, Dataset $dataset)
    {
        $content = $request->get('img');
        $study = $dataset->study;
        if ($content) {
            if ($study->project) {
                $path = '/projects/'.$study->project->uuid.'/'.$study->uuid.'/'.$dataset->slug.'.svg';
                Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'))->put($path, $content, 'public');
                $dataset->dataset_photo_path = $path;
                $dataset->save();
            } else {
                $path = '/samples/'.$study->uuid.'/'.$dataset->slug.'.svg';
                Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'))->put($path, $content, 'public');
                $dataset->dataset_photo_path = $path;
                $dataset->save();
            }
        }
    }
}
