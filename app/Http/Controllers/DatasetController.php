<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\FileSystemObject;
use Inertia\Inertia;
use Aws\S3\S3Client;
use Illuminate\Support\Str;
use ZipStream;

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
        if($dataset){
            $spectra = $request->get('spectra');
            $nmriumInfo = json_encode($spectra);
            if($nmriumInfo){
                $dataset->nmrium_info = $nmriumInfo;
                $dataset->type = $spectra['info']['nucleus'];
                $dataset->save();
            }
        }
        return $dataset->fresh();
    }

    public function download(Request $request, $code, Dataset $dataset, $filename)
    {
        $fsObj = FileSystemObject::where([
            ['dataset_id', $dataset->id],
        ])->first();

        $path = $fsObj->path;

        $s3Client = $this->storageClient();

        $bucket =
            $request->input('bucket') ?:
            config('filesystems.disks.minio.bucket');

        $command = $s3Client->getCommand('ListObjects');
        $command['Bucket'] = $bucket;
        $command['Prefix'] = $path . '/';

        $result = $s3Client->execute($command);

        $s3keys = [];

        foreach ($result['Contents'] as $file) {
            array_push($s3keys, $file['Key']);
        }

        $s3Client->registerStreamWrapper();

        return response()->stream(
            function () use ($s3keys, $s3Client, $bucket, $fsObj, $dataset) {
                $options = new \ZipStream\Option\Archive();
                $options->setContentType('application/octet-stream');
                $options->setZeroHeader(true);
                $options->setComment('test zip file.');

                $zip = new ZipStream\ZipStream('test.zip', $options);

                foreach ($s3keys as $key) {
                    $fileName = basename($key);
                    $s3path = 's3://' . $bucket . '/' . $key;
                    if ($streamRead = fopen($s3path, 'r')) {
                        $zip->addFileFromStream($fsObj->key . '/' . explode( '/' . $dataset->study->name . '/' . $fsObj->key . '/', $key)[1], $streamRead);
                    } else {
                        die('Could not open stream for reading');
                    }
                }

                $zip->finish();
            },
            200,
            [
                'Access-Control-Allow-Origin' => '*',
                'Content-Disposition' => 'attachment;filename="'. $dataset->name .'.zip"',
                'Content-Type' => 'application/octet-stream',
            ]
        );
    }

    /**
     * Create a command for the PUT operation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Aws\S3\S3Client  $client
     * @param  string  $bucket
     * @param  string  $key
     * @return \Aws\Command
     */
    protected function createCommand(
        Request $request,
        S3Client $client,
        $bucket,
        $key
    ) {
        return $client->getCommand(
            'ListObjects',
            array_filter([
                'Bucket' => $bucket,
                'Key' => $key,
            ])
        );
    }

    /**
     * Get the S3 storage client instance.
     *
     * @return \Aws\S3\S3Client
     */
    protected function storageClient()
    {
        $config = [
            'region' => config('filesystems.disks.minio.region'),
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            'url' => config('filesystems.disks.minio.endpoint'),
            'endpoint' => config('filesystems.disks.minio.endpoint'),
            'credentials' => [
                'key' => config('filesystems.disks.minio.key'),
                'secret' => config('filesystems.disks.minio.secret'),
            ],
        ];

        return S3Client::factory($config);
    }

    public function publicDatasetsView(Request $request)
    {
        $datasets = Dataset::where('is_public', true)->simplePaginate(15);

        return Inertia::render('Public/Datasets', [
            'datasets' => $datasets,
        ]);
    }
}
