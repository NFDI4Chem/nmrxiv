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
        $user = User::where('username', $username)->firstOrFail();

        if ($project) {
            $project = Project::where([['slug', $project], ['owner_id', $user->id]])->firstOrFail();

            $fsObject = null;

            if ($dataset) {
                $study = Study::where([['project_id', $project->id], ['slug', $study],  ['owner_id', $user->id]])->firstOrFail();
                $dataset = Dataset::where([['project_id', $project->id], ['study_id', $study->id], ['slug', $dataset], ['owner_id', $user->id]])->first();
                $fsObject = FileSystemObject::where([['id', $dataset->fs_id], ['type', 'directory']])->first();
                $request->merge(['uuid' => $fsObject->uuid]);

                return $this->downloadFromProject($request, $username, $project, $fsObject->key);
            }

            if ($study) {
                $study = Study::where([['project_id', $project->id], ['slug', $study],  ['owner_id', $user->id]])->firstOrFail();
                $fsObject = FileSystemObject::where([['id', $study->fs_id], ['type', 'directory']])->first();
                $request->merge(['uuid' => $fsObject->uuid]);

                return $this->downloadFromProject($request, $username, $project, $fsObject->key);
            }

            $fsObj = new FileSystemObject();
            $fsObj->type = 'directory';
            $fsObj->name = $project->slug;
            $environment = env('APP_ENV', 'local');
            $fsObj->path = $environment.'/'.$project->uuid;
            $fsObj->key = $project->uuid;

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

            if ($fsObj->type == 'file') {
                $environment = env('APP_ENV', 'local');
                if (Storage::has($path)) {
                    $data = Storage::get($path);
                    $newFileName = $fsObj->name;
                    $headers = [
                        'Access-Control-Allow-Origin' => '*',
                        'Content-Disposition' => sprintf(
                            'attachment; filename="%s"',
                            $newFileName
                        ),
                    ];

                    return Response::make($data, 200, $headers);
                }
            } else {
                $s3Client = $this->storageClient();

                $bucket = $request->input('bucket') ?: config('filesystems.disks.minio.bucket');

                $command = $s3Client->getCommand('ListObjects');
                $command['Bucket'] = $bucket;
                $command['Prefix'] = $path.'/';

                $result = $s3Client->execute($command);

                $s3keys = [];

                foreach ($result['Contents'] as $file) {
                    array_push($s3keys, $file['Key']);
                }

                $s3Client->registerStreamWrapper();

                return response()->stream(
                    function () use ($s3keys, $bucket, $fsObj) {
                        $options = new \ZipStream\Option\Archive();
                        $options->setContentType('application/octet-stream');
                        $options->setZeroHeader(true);
                        $options->setComment($fsObj->name);
                        $zip = new ZipStream\ZipStream($fsObj->name, $options);
                        foreach ($s3keys as $key) {
                            $s3path = 's3://'.$bucket.'/'.$key;
                            if ($streamRead = fopen($s3path, 'r')) {
                                $zip->addFileFromStream($fsObj->key.'/'.explode('/'.$fsObj->key.'/', $key)[1], $streamRead);
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
}
