<?php

namespace App\Jobs;

use App\Models\Project;
use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipStream;

class ArchiveStudy implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * The project instance.
     *
     * @var \App\Models\Project
     */
    public $project;

    /**
     * Create a new job instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->project->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $project = $this->project;
        if ($project) {
            foreach ($project->studies as $study) {
                $study->internal_status = 'processing';
                $study->save();
                $archiveDownloadURL = $study->download_url;
                if (! $archiveDownloadURL) {
                    $fsObject = $study->fsObject;
                    if ($fsObject) {
                        $path = $fsObject->path;
                        $s3Client = $this->storageClient();
                        $bucket = config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.bucket');
                        $s3keys = [];
                        $environment = env('APP_ENV', 'local');
                        if ($fsObject->type == 'file') {
                            if (Storage::has($path)) {
                                array_push($s3keys, substr($fsObject->path, 1));
                            }
                        } else {

                            $command = [
                                'Bucket' => $bucket,
                            ];
                            if ($path[0] == '/') {
                                $command['Prefix'] = ltrim($path, $path[0]).'/';
                            } else {
                                $command['Prefix'] = $path.'/';
                            }
                            $results = $s3Client->getPaginator('ListObjects', $command);
                            foreach ($results as $result) {
                                $contents = $result->get('Contents');
                                if ($contents) {
                                    foreach ($contents as $file) {
                                        array_push($s3keys, $file['Key']);
                                    }
                                }
                            }
                        }

                        $s3Client->registerStreamWrapper();

                        $zipFilePath = $environment.'/archive/'.$study->uuid.'/'.$fsObject->name.'.zip';

                        $archiveDestination = fopen('s3://'.$bucket.'/'.$zipFilePath, 'w');

                        $zip = new ZipStream\ZipStream(
                            outputStream: $archiveDestination,
                            defaultEnableZeroHeader: true,
                            sendHttpHeaders: false,
                        );

                        foreach ($s3keys as $key) {
                            $s3path = 's3://'.$bucket.'/'.$key;
                            if ($streamRead = fopen($s3path, 'r')) {
                                $sPath = explode($fsObject->relative_url, $key)[1];
                                if ($sPath != '') {
                                    $sPath = $fsObject->key.'/'.explode($fsObject->relative_url, $key)[1];
                                } else {
                                    $sPath = $fsObject->key;
                                }
                                $zip->addFileFromStream($sPath, $streamRead);
                            } else {
                                exit('Could not open stream for reading');
                            }
                        }
                        $zip->finish();
                        fclose($archiveDestination);

                        Storage::setVisibility($zipFilePath, 'public');
                        $url = Storage::url($zipFilePath);
                        $study->download_url = $url;

                        // $nmrium = $study->nmrium;
                        // if (!$nmrium) {
                        //     dd($url);
                        //     $nmrium_ = $this->processSpectra($url);
                        //     dd($nmrium_);
                        //     $parsedSpectra = $nmrium_['data']['data'];
                        //     foreach ($parsedSpectra['spectra'] as $spectra) {
                        //         unset($spectra["data"]);
                        //         unset($spectra["meta"]);
                        //         unset($spectra["originalData"]);
                        //         unset($spectra["originalInfo"]);
                        //     }

                        //     $version = $parsedSpectra['version'];
                        //     unset($parsedSpectra["version"]);

                        //     // associate
                        //     $nmriumJSON = array(
                        //         "data" => $parsedSpectra,
                        //         "version" => $version,
                        //     );

                        //     $nmrium = NMRium::create([
                        //         'nmrium_info' => json_encode($nmriumJSON),
                        //     ]);
                        //     $study->nmrium()->save($nmrium);
                        //     $study->has_nmrium = true;
                        // }

                        // $sample = $study->sample;
                        // if (! $sample) {
                        //     $sample = Sample::create([
                        //         'name' => $study->name.'_sample',
                        //         'slug' => Str::slug($study->name.'_sample', '-'),
                        //         'study_id' => $study->id,
                        //         'project_id' => $study->project->id,
                        //     ]);
                        //     $study->sample()->save($sample);
                        // }

                        // $molecules = $parsedSpectra['molecules'];
                        // if (count($molecules) > 0) {
                        //     foreach ($molecules as $mol) {
                        //         $standardizedMolecule = $this->standardizeMolecule($mol['molfile']);
                        //         // associate
                        //         $inchi = $standardizedMolecule['InChI'];
                        //         $molecule = $sample->molecules->where('standard_inchi', $inchi)->first();
                        //         if (is_null($molecule)) {
                        //             $molecule = Molecule::firstOrCreate([
                        //                 'standard_inchi' => $inchi,
                        //             ], [
                        //                 'molecular_formula' => $standardizedMolecule['formula'] ? $standardizedMolecule['formula']  : '',
                        //                 'inchi_key' => $standardizedMolecule['InChIKey']  ? $standardizedMolecule['InChIKey']  : '',
                        //                 'sdf' => $standardizedMolecule['mol']  ? $standardizedMolecule['mol']  : '',
                        //                 'canonical_smiles' => $standardizedMolecule['canonical_smiles']  ? $standardizedMolecule['canonical_smiles']  : '',
                        //             ]);
                        //             $sample->molecules()->syncWithPivotValues([$molecule->id], ['percentage_composition' => 0], false);
                        //         }
                        //     }
                        // }
                        // // add molecules
                        $study->internal_status = 'complete';
                        $study->save();
                    }
                } else {
                    $study->internal_status = 'complete';
                    $study->save();
                }
            }
        }
    }

    // protected function standardizeMolecule($mol)
    // {
    //     $response = Http::post('https://dev.api.naturalproducts.net/latest/chem/standardize', $mol);
    //     return $response->json();
    // }

    // protected function processSpectra($url)
    // {
    //     $response = Http::post('https://nodejsdev.nmrxiv.org/spectra-parser', '{
    //         "urls": [
    //           '. $url .'
    //         ],
    //         "snapshot": false
    //       }');

    //     return $response->json();
    // }

    /**
     * Get the S3 storage client instance.
     *
     * @return \Aws\S3\S3Client
     */
    protected function storageClient()
    {
        $config = [
            'region' => config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.region'),
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            'url' => config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.endpoint'),
            'endpoint' => config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.endpoint'),
            'credentials' => [
                'key' => config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.key'),
                'secret' => config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.secret'),
            ],
        ];

        return S3Client::factory($config);
    }
}
