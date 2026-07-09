<?php

namespace App\Jobs;

use App\Models\FileSystemObject;
use App\Models\Project;
use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipStream;

class ArchiveProject implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * Only ever run an archive job once. Failures must surface to Horizon
     * rather than be retried, otherwise large zips re-run from scratch and
     * compound the load on Ceph.
     */
    public $tries = 1;

    /**
     * The project instance.
     *
     * @var Project
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
     * How long (seconds) the unique lock should be held. Without this,
     * a crashed/killed worker can leave the lock orphaned in redis and
     * silently swallow every subsequent dispatch for this project.
     */
    public function uniqueFor(): int
    {
        return 14400;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('embargo_publish_trace', [
            'stage' => 'archive_project_job_started',
            'project_id' => $this->project->id,
        ]);

        $project = $this->project;
        if ($project) {
            $archiveDownloadURL = $project->download_url;
            if ($project->internal_status != 'processing' || $project->internal_status != 'completed') {
                $project->internal_status = 'processing';
                $project->save();

                if ($archiveDownloadURL == null) {
                    $fsObject = $project->fsObject;

                    if (! $fsObject) {
                        $fsObject = new FileSystemObject;
                        $fsObject->type = 'directory';
                        $fsObject->name = $project->slug;
                        $environment = config('app.env', 'local');
                        $fsObject->path = $environment.'/'.$project->uuid;
                        $fsObject->key = $project->uuid;
                        $fsObject->status = 'present';
                        $fsObject->relative_url = '/'.$project->uuid;
                    }

                    if ($fsObject && $fsObject->status != 'missing') {
                        $path = $fsObject->path;
                        $s3Client = $this->storageClient();
                        $bucket = config('filesystems.disks.'.config('filesystems.default').'.bucket');
                        $s3keys = [];
                        $environment = config('app.env', 'local');
                        if ($fsObject->type == 'file') {
                            if (Storage::disk(config('filesystems.default'))->exists($path)) {
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

                        $zipFilePath = $environment.'/archive/'.$project->uuid.'/'.$fsObject->name.'.zip';

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

                                // Get file size to handle empty files properly
                                try {
                                    $fileSize = $s3Client->headObject([
                                        'Bucket' => $bucket,
                                        'Key' => $key,
                                    ])->get('ContentLength');

                                    Log::info("Project {$project->id}: Processing file {$key}, size: {$fileSize} bytes");

                                    // Handle empty files specially to avoid corruption
                                    if ($fileSize == 0) {
                                        Log::info("Project {$project->id}: Adding empty file: {$key}");
                                        fclose($streamRead);

                                        // Add empty file using addFile method instead of stream
                                        try {
                                            $zip->addFile($sPath, '');
                                            Log::info("Project {$project->id}: Successfully added empty file: {$key}");
                                        } catch (\Exception $e) {
                                            Log::error("Project {$project->id}: Failed to add empty file {$key}: ".$e->getMessage());
                                        }
                                    } else {
                                        // Add non-empty file using stream
                                        $zip->addFileFromStream($sPath, $streamRead);
                                        fclose($streamRead);
                                    }
                                } catch (\Exception $e) {
                                    Log::error("Project {$project->id}: Error processing file {$key}: ".$e->getMessage());
                                    fclose($streamRead);

                                    continue;
                                }
                            } else {
                                Log::error("Project {$project->id}: Could not open stream for reading: {$s3path}");

                                continue;
                            }
                        }
                        $zip->finish();
                        fclose($archiveDestination);

                        Storage::disk(config('filesystems.default'))->setVisibility($zipFilePath, 'public');
                        $url = Storage::disk(config('filesystems.default'))->url($zipFilePath);
                        $project->download_url = $url;
                        $project->internal_status = 'complete';
                        $project->save();

                        Log::info('embargo_publish_trace', [
                            'stage' => 'archive_project_zip_built',
                            'project_id' => $project->id,
                            'download_url_set' => true,
                        ]);
                    } else {
                        Log::info('embargo_publish_trace', [
                            'stage' => 'archive_project_skip_zip',
                            'project_id' => $project->id,
                            'reason' => ! $fsObject ? 'no_fs_object' : 'fs_status_missing',
                        ]);
                    }
                } else {
                    Log::info('embargo_publish_trace', [
                        'stage' => 'archive_project_skip_existing_download_url',
                        'project_id' => $project->id,
                    ]);

                    $project->internal_status = 'complete';
                    $project->save();
                }
            }

        }

        Log::info('embargo_publish_trace', [
            'stage' => 'archive_project_job_finished',
            'project_id' => $this->project->id,
        ]);
    }

    /**
     * Get the S3 storage client instance.
     *
     * @return S3Client
     */
    protected function storageClient()
    {
        $diskName = config('filesystems.default');
        $config = [
            'region' => config('filesystems.disks.'.$diskName.'.region'),
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            'url' => config('filesystems.disks.'.$diskName.'.endpoint'),
            'endpoint' => config('filesystems.disks.'.$diskName.'.endpoint'),
            'credentials' => [
                'key' => config('filesystems.disks.'.$diskName.'.key'),
                'secret' => config('filesystems.disks.'.$diskName.'.secret'),
            ],
        ];

        return S3Client::factory($config);
    }
}
