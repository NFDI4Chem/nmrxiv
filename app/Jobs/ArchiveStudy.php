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
use Illuminate\Support\Facades\Storage;
use ZipStream;

class ArchiveStudy implements ShouldBeUnique, ShouldQueue
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
     * Execute the job.
     */
    public function handle(): void
    {
        $project = $this->project;
        if ($project) {
            foreach ($project->studies as $study) {
                $archiveDownloadURL = $study->download_url;
                if (! $archiveDownloadURL) {
                    $fsObject = $study->fsObject;
                    if ($fsObject && $fsObject->status != 'missing') {
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
                            $command = $s3Client->getCommand('ListObjects');
                            $command['Bucket'] = $bucket;
                            if ($path[0] == '/') {
                                $command['Prefix'] = ltrim($path, $path[0]).'/';
                            } else {
                                $command['Prefix'] = $path.'/';
                            }
                            $result = $s3Client->execute($command);
                            if ($result['Contents']) {
                                foreach ($result['Contents'] as $file) {
                                    array_push($s3keys, $file['Key']);
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
                        $study->save();
                    }
                }
            }
        }

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
