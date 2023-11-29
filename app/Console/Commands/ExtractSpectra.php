<?php

namespace App\Console\Commands;

use App\Models\NMRium;
use App\Models\Project;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ExtractSpectra extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:nmrium-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'extract nmrium json from spectra';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projects = Project::where([
            ['is_public', true],
        ])->get();

        foreach ($projects as $project) {
            echo "\r\n";
            echo $project->identifier;
            echo "\r\n";
            $studies = $project->studies;
            foreach ($studies as $study) {
                echo $study->identifier;
                echo "\r\n";
                try {
                    if (! $study->has_nmrium) {
                        DB::transaction(function () use ($study) {
                            $download_url = $study->download_url;
                            if ($download_url) {
                                $nmrium_ = $this->processSpectra($download_url);
                                $parsedSpectra = $nmrium_['data'];
                                foreach ($parsedSpectra['spectra'] as $spectra) {
                                    unset($spectra['data']);
                                    unset($spectra['meta']);
                                    unset($spectra['originalData']);
                                    unset($spectra['originalInfo']);
                                }

                                $version = $parsedSpectra['version'];
                                unset($parsedSpectra['version']);

                                $nmriumJSON = [
                                    'data' => $parsedSpectra,
                                    'version' => $version,
                                ];

                                $nmrium = $study->nmrium;

                                if ($nmrium) {
                                    $nmrium->nmrium_info = json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE);
                                    $nmrium->save();
                                } else {
                                    $nmrium = NMRium::create([
                                        'nmrium_info' => json_encode($nmriumJSON, JSON_UNESCAPED_UNICODE),
                                    ]);
                                    $study->nmrium()->save($nmrium);
                                    $study->has_nmrium = true;
                                    $study->save();
                                }
                            }
                        });
                    }
                    $study = $study->fresh();
                    if ($study->has_nmrium) {
                        $nmriumInfo = json_decode($study->nmrium['nmrium_info'], true);
                        if (count($nmriumInfo['data']['spectra']) == 0) {
                            echo '--MISSING SPECTRA INFO (NMRIUM JSON)--';
                            echo "\r\n";
                        } else {
                            foreach ($study->datasets as $dataset) {
                                echo $dataset->identifier;
                                echo "\r\n";
                                // echo $dataset->type;
                                // echo "\r\n";
                                if (! $dataset->has_nmrium) {
                                    $nmriumInfo = json_decode($study->nmrium['nmrium_info'], true);
                                    $_nmriumJSON = $nmriumInfo;
                                    $fsObject = $dataset->fsObject;

                                    $studyFSObject = $study->fsObject;
                                    $datasetFSObject = $dataset->fsObject;
                                    $path = '/'.$studyFSObject->name.'/'.$datasetFSObject->name;
                                    $fType = $studyFSObject->type;

                                    // echo($path);
                                    // echo "\r\n";
                                    $fsObject = $dataset->fsObject;
                                    $studyFSObject = $study->fsObject;
                                    $datasetFSObject = $dataset->fsObject;
                                    $path = '/'.$studyFSObject->name.'/'.$datasetFSObject->name;

                                    $pathsMatch = false;
                                    $spectrum = [];
                                    $type = [];
                                    foreach ($nmriumInfo['data']['spectra'] as $spectra) {
                                        unset($_nmriumJSON['data']['spectra']);
                                        $files = $spectra['sourceSelector']['files'];
                                        if ($files) {
                                            foreach ($files as $file) {
                                                if (str_contains($file, $fType == 'file' ? $path : $path.'/')) {
                                                    $pathsMatch = true;
                                                }
                                            }
                                        }
                                        if ($pathsMatch) {
                                            array_push($spectrum, $spectra);
                                            $experimentDetailsExists = array_key_exists('experiment', $spectra['info']);
                                            if ($experimentDetailsExists) {
                                                $experiment = $spectra['info']['experiment'];
                                                $nucleus = $spectra['info']['nucleus'];
                                                if (is_array($nucleus)) {
                                                    $nucleus = implode('-', $nucleus);
                                                }
                                                array_push($type, $experiment.' - '.$nucleus);
                                            }
                                            $pathsMatch = false;
                                        }
                                    }
                                    if (count($spectrum) > 0) {
                                        $_nmriumJSON['data']['spectra'] = $spectrum;
                                        $_nmrium = $dataset->nmrium;
                                        if ($_nmrium) {
                                            $_nmrium->nmrium_info = json_encode($_nmriumJSON, JSON_UNESCAPED_UNICODE);
                                            $dataset->has_nmrium = true;
                                            $_nmrium->save();
                                        } else {
                                            $_nmrium = NMRium::create([
                                                'nmrium_info' => json_encode($_nmriumJSON, JSON_UNESCAPED_UNICODE),
                                            ]);
                                            $dataset->nmrium()->save($_nmrium);
                                            $dataset->has_nmrium = true;
                                        }
                                    }
                                    $uType = array_unique($type);
                                    if (count($uType) == 1) {
                                        $dataset->type = $uType[0];
                                    }
                                    $dataset->save();
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }

            }
        }
    }

    public function processSpectra($url)
    {
        $response = Http::timeout(300)->post('https://nodejs.nmrxiv.org/spectra-parser', [
            'urls' => [$url],
            'snapshot' => false,
        ]);

        return $response->json();
    }
}
