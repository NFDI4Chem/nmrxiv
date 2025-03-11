<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use ZipStream;

class DownloadController extends Controller
{
    public function downloadSet(Request $request, $username, $project, $study = null, $dataset = null)
    {
        if (str_contains($dataset, '.zip')) {
            $dataset = str_replace('.zip', '', $dataset);
        }
        if (str_contains($study, '.zip')) {
            $study = str_replace('.zip', '', $study);
        }

        $user = User::where('username', $username)->firstOrFail();
        if ($project) {
            $project = Project::where([['slug', $project], ['owner_id', $user->id]])->firstOrFail();

            $fsObject = null;

            if ($dataset) {
                $study = Study::where([['project_id', $project->id], ['slug', $study],  ['owner_id', $user->id]])->firstOrFail();
                $dataset = Dataset::where([['project_id', $project->id], ['study_id', $study->id], ['slug', $dataset], ['owner_id', $user->id]])->first();
                $fsObject = FileSystemObject::where([['id', $dataset->fs_id], ['type', 'directory']])->first();
                if (! $fsObject) {
                    $fsObject = FileSystemObject::where([['id', $dataset->fs_id], ['type', 'file']])->first();
                }
                $request->merge(['uuid' => $fsObject->uuid]);

                return $this->downloadFromProject($request, $username, $project, $fsObject->key);
            }

            if ($study) {
                $study = Study::where([['project_id', $project->id], ['slug', $study],  ['owner_id', $user->id]])->firstOrFail();

                if ($study->download_url) {
                    $fsObject = FileSystemObject::where([['id', $study->fs_id], ['type', 'directory']])->first();

                    return response()->stream(function () use ($study) {
                        readfile($study->download_url);
                    }, 200, [
                        'Access-Control-Allow-Origin' => '*',
                        'Content-Disposition' => 'attachment;filename="'.$fsObject->name.'.zip"',
                        'Content-Type' => 'application/octet-stream',
                    ]);
                } else {
                    $fsObject = FileSystemObject::where([['id', $study->fs_id], ['type', 'directory']])->first();
                    $request->merge(['uuid' => $fsObject->uuid]);

                    return $this->downloadFromProject($request, $username, $project, $fsObject->key);
                }
            }

            $fsObj = new FileSystemObject;
            $fsObj->type = 'directory';
            $fsObj->name = $project->slug;
            $environment = env('APP_ENV', 'local');
            $fsObj->path = $environment.'/'.$project->uuid;
            $fsObj->key = $project->uuid;
            $fsObj->relative_url = '/'.$project->uuid;
            $request->merge(['uuid' => $project->uuid]);

            return $this->downloadFromProject($request, $username, $project, $fsObj->key, $fsObj);
        }

        return Response::make(null, 404);
    }

    public function downloadFromProject(Request $request, $username, $project, $key = null, $fsObj = null)
    {
        $key = $key ?: $request->get('key');
        $uuid = $request->get('uuid');

        $fsObj = $fsObj ?: FileSystemObject::with('study')->where([
            ['uuid', $uuid],
        ])->first();

        if ($fsObj->key == $key) {
            $path = $fsObj->path;

            $s3Client = $this->storageClient();

            $bucket = $request->input('bucket') ?: config('filesystems.disks.'.env('FILESYSTEM_DRIVER').'.bucket');

            $s3keys = [];

            if ($fsObj->type == 'file') {
                $environment = env('APP_ENV', 'local');
                if (Storage::has($path)) {
                    array_push($s3keys, substr($fsObj->path, 1));
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

            return response()->stream(
                function () use ($s3keys, $bucket, $fsObj) {
                    $zip = new ZipStream\ZipStream(
                        outputName : $fsObj->name.'.zip',
                        contentType: 'application/octet-stream',
                        defaultEnableZeroHeader: true,
                        comment: $fsObj->name
                    );

                    foreach ($s3keys as $key) {
                        $s3path = 's3://'.$bucket.'/'.$key;
                        if ($streamRead = fopen($s3path, 'r')) {
                            $sPath = explode($fsObj->relative_url, $key)[1];
                            if ($sPath != '') {
                                $sPath = $fsObj->key.'/'.explode($fsObj->relative_url, $key)[1];
                            } else {
                                $sPath = $fsObj->key;
                            }
                            $zip->addFileFromStream($sPath, $streamRead);
                        } else {
                            exit('Could not open stream for reading');
                        }
                    }
                    $zip->finish();
                },
                200,
                [
                    'Access-Control-Allow-Origin' => '*',
                    'Content-Disposition' => 'attachment;filename="'.$fsObj->name.'.zip"',
                    'Content-Type' => 'application/octet-stream',
                ]
            );
        }

        return Response::make(null, 404);
    }

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
