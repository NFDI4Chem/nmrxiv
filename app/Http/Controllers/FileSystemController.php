<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;

class FileSystemController extends Controller
{
     /**
     * Create a new signed URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signedStorageURL(Request $request)
    {
        $file = $request->get('file');

        $project = Project::find($request->get('project_id'));
        $study = Study::find($request->get('study_id'));

        $baseDirectory = $request->get('base');

        $path = null;

        if(array_key_exists('fullPath', $file)){
            $path = $file['fullPath'];
        }
        
        $hasDirectories = $path ? true : false;
        
        $filename = $file['upload']['filename'];

        $user = $request->user();

        $level = 0;

        $currentLevel = $level;

        $relativefilePath = $path ? $path : $filename;

        $environment = env('APP_ENV', 'local');

        $filePath = $environment . "/" . $project->uuid . "/" . $study->uuid . "/" . $relativefilePath;

        if($hasDirectories){
            $directories =  array_filter(explode('/', str_replace($filename, '' , $path)));
            if($baseDirectory){
                $level = 0;
            }
            if(($level + count($directories) - 1) > $level){
                for ($currentLevel; $currentLevel < ($level + count($directories) - 1); $currentLevel++) {
                    $parentFileSystemObject = FileSystemObject::firstOrCreate([
                        'name' => $directories[$currentLevel],
                        'slug' => Str::slug($directories[$currentLevel],'-'),
                        'description' => $directories[$currentLevel],
                        'relative_url' => str_replace( '//', '/', '/' . explode($directories[$currentLevel], $path)[0] . "/" . $directories[$currentLevel]),
                        'type' => 'directory',
                        'key' => $directories[$currentLevel],
                        'is_root' => $currentLevel == 0 ? 1 : 0,
                        'project_id' => $project->id,
                        'study_id' => $study->id,
                        'level' => $currentLevel,
                    ]);
                    $childFileSystemObject = FileSystemObject::firstOrCreate([
                        'name' => $directories[$currentLevel+1],
                        'slug' => Str::slug($directories[$currentLevel+1],'-'),
                        'description' => $directories[$currentLevel+1],
                        'relative_url' => str_replace( '//', '/', '/' . explode($directories[$currentLevel+1], $path)[0] . "/" . $directories[$currentLevel+1]),
                        'type' => 'directory',
                        'key' => $directories[$currentLevel+1],
                        'is_root' => $currentLevel + 1 == 0 ? 1 : 0,
                        'project_id' => $project->id,
                        'study_id' => $study->id,
                        'level' => $currentLevel+1,
                    ]);
                    if(!$childFileSystemObject->parent_id){
                        $childFileSystemObject->parent_id = $parentFileSystemObject->id;
                        $childFileSystemObject->save();
                        $parentFileSystemObject->has_children = 1;
                        $parentFileSystemObject->save();
                    }
                }
            }else{
                $childFileSystemObject = FileSystemObject::firstOrCreate([
                    'name' => $directories[$currentLevel],
                    'slug' => Str::slug($directories[$currentLevel],'-'),
                    'description' => $directories[$currentLevel],
                    'relative_url' => str_replace( '//', '/', '/' . explode($directories[$currentLevel], $path)[0] . "/" . $directories[$currentLevel]),
                    'type' => 'directory',
                    'key' => $directories[$currentLevel],
                    'is_root' => $currentLevel == 0 ? 1 : 0,
                    'project_id' => $request->get('project_id'),
                    'study_id' => $request->get('study_id'),
                    'level' => $currentLevel,
                ]);
            }
        }

        if($hasDirectories){
            $childFileSystemObject->has_children = 1;
            $childFileSystemObject->save();
        }

        $fileFileSystemObject = FileSystemObject::firstOrCreate([
            'name' => $filename,
            'slug' => Str::slug($filename,'-'),
            'description' => $filename,
            'relative_url' => $path,
            'type' => 'file',
            'key' => $filename,
            'is_root' => 0,
            'project_id' => $request->get('project_id'),
            'study_id' => $request->get('study_id'),
            'level' => $hasDirectories ? $currentLevel + 1 : $currentLevel,
            'parent_id' => $hasDirectories ? $childFileSystemObject->id : null,
        ]);

        $bucket = $request->input('bucket') ?: config('filesystems.disks.minio.bucket');

        $client = $this->storageClient();

        $uuid = (string) Str::uuid();
        
        $signedRequest = $client->createPresignedRequest(
            $this->createCommand($request, $client, $bucket, $key = $filePath),
            '+90 minutes'
        );

        return response()->json([
            'uuid' => $uuid,
            'bucket' => $bucket,
            'key' => $key,
            'url' => (string) $signedRequest->getUri(),
            'headers' => $this->headers($request, $signedRequest),
        ], 201);
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
    protected function createCommand(Request $request, S3Client $client, $bucket, $key)
    {
        return $client->getCommand('putObject', array_filter([
            'Bucket' => $bucket,
            'Key' => $key,
        ]));
    }

    /**
     * Get the headers that should be used when making the signed request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \GuzzleHttp\Psr7\Request
     * @return array
     */
    protected function headers(Request $request, $signedRequest)
    {
        return array_merge(
            $signedRequest->getHeaders(),
            [
                'Content-Type' => $request->input('content_type') ?: 'application/octet-stream',
            ]
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
}
