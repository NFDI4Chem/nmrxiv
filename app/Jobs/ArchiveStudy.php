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
        // Debug logging - immediate write to log file
        Log::info('=== ArchiveStudy job STARTED for project: '.$this->project->id.' ===');
        Log::info('Project ID: '.$this->project->id);
        Log::info('Current timestamp: '.now()->toString());

        Log::info('Archiving study for projects '.$this->project->id);
        $project = $this->project;
        if ($project) {
            Log::info("Project {$project->id} studies count: ".$project->studies->count());
            foreach ($project->studies as $study) {
                Log::info("Processing study {$study->id}");
                $study->internal_status = 'processing';
                $study->save();
                $archiveDownloadURL = $study->download_url;
                Log::info("Study {$study->id} download URL: ".($archiveDownloadURL ?? 'null'));
                if (! $archiveDownloadURL) {
                    $fsObject = $study->fsObject;
                    Log::info("Study {$study->id} fsObject: ".($fsObject ? 'EXISTS' : 'NULL'));
                    if ($fsObject) {
                        Log::info("Study {$study->id}: Starting archive creation");
                        Log::info("Study {$study->id}: fsObject path: {$fsObject->path}");
                        Log::info("Study {$study->id}: fsObject type: {$fsObject->type}");
                        Log::info("Study {$study->id}: fsObject relative_url: {$fsObject->relative_url}");
                        Log::info("Study {$study->id}: fsObject key: {$fsObject->key}");

                        // Fix path duplication issue
                        $path = $this->validateAndCorrectPath($fsObject, $study->id);
                        Log::info("Study {$study->id}: Corrected path: {$path}");

                        $s3Client = $this->storageClient();
                        $filesystemDriver = config('filesystems.default');
                        $bucket = config("filesystems.disks.{$filesystemDriver}.bucket");
                        Log::info("Study {$study->id}: Using filesystem driver: {$filesystemDriver}, bucket: {$bucket}");

                        $s3keys = [];
                        $environment = env('APP_ENV', 'local');
                        $relative_URL = $fsObject->relative_url;
                        if ($fsObject->type == 'file') {
                            Log::info("Study {$study->id}: Processing single file");
                            if (Storage::disk($filesystemDriver)->exists($path)) {
                                array_push($s3keys, substr($fsObject->path, 1));
                                Log::info("Study {$study->id}: File exists, added to s3keys: ".substr($fsObject->path, 1));
                            } else {
                                Log::warning("Study {$study->id}: File does not exist at path: {$path}");
                            }
                        } else {
                            Log::info("Study {$study->id}: Processing directory");
                            $relative_URL = $relative_URL.'/';
                            $command = [
                                'Bucket' => $bucket,
                            ];
                            if ($path[0] == '/') {
                                $command['Prefix'] = ltrim($path, $path[0]).'/';
                            } else {
                                $command['Prefix'] = $path.'/';
                            }
                            Log::info("Study {$study->id}: S3 ListObjects command: ".json_encode($command));

                            try {
                                $results = $s3Client->getPaginator('ListObjects', $command);
                                $fileCount = 0;
                                foreach ($results as $result) {
                                    $contents = $result->get('Contents');
                                    if ($contents) {
                                        foreach ($contents as $file) {
                                            array_push($s3keys, $file['Key']);
                                            $fileCount++;
                                        }
                                    }
                                }
                                Log::info("Study {$study->id}: Found {$fileCount} files in directory");
                            } catch (\Exception $e) {
                                Log::error("Study {$study->id}: Error listing S3 objects: ".$e->getMessage());
                                throw $e;
                            }
                        }

                        if (empty($s3keys)) {
                            Log::warning("Study {$study->id}: No files found to archive");
                            $study->internal_status = 'complete';
                            $study->save();

                            continue;
                        }

                        Log::info("Study {$study->id}: Total files to archive: ".count($s3keys));
                        $s3Client->registerStreamWrapper();

                        $zipFilePath = $environment.'/archive/'.$study->uuid.'/'.$fsObject->name.'.zip';
                        Log::info("Study {$study->id}: Creating archive at: {$zipFilePath}");

                        try {
                            $archiveDestination = fopen('s3://'.$bucket.'/'.$zipFilePath, 'w');
                            if (! $archiveDestination) {
                                throw new \Exception("Could not open archive destination: s3://{$bucket}/{$zipFilePath}");
                            }

                            $zip = new ZipStream\ZipStream(
                                outputStream: $archiveDestination,
                                defaultEnableZeroHeader: true,
                                sendHttpHeaders: false,
                            );

                            $addedFiles = 0;
                            foreach ($s3keys as $key) {
                                $s3path = 's3://'.$bucket.'/'.$key;
                                Log::info("Study {$study->id}: Processing file: {$key}");

                                if ($streamRead = fopen($s3path, 'r')) {
                                    $sPath = explode($relative_URL, $key)[1];
                                    if ($sPath != '') {
                                        $sPath = $fsObject->key.'/'.explode($relative_URL, $key)[1];
                                    } else {
                                        $sPath = $fsObject->key;
                                    }
                                    $sPath = preg_replace('#/+#', '/', $sPath);

                                    Log::info("Study {$study->id}: Adding file to zip as: {$sPath}");

                                    // Get file size
                                    try {
                                        $fileSize = $s3Client->headObject([
                                            'Bucket' => $bucket,
                                            'Key' => $key,
                                        ])->get('ContentLength');

                                        Log::info("Study {$study->id}: File size: {$fileSize} bytes");

                                        // If file is larger than 100MB, process in chunks
                                        if ($fileSize > 100 * 1024 * 1024) {
                                            $chunkSize = 10 * 1024 * 1024; // 10MB chunks
                                            $zip->addFileFromStream($sPath, $streamRead, $fileSize);
                                        } else {
                                            $zip->addFileFromStream($sPath, $streamRead);
                                        }
                                        $addedFiles++;
                                        fclose($streamRead);
                                    } catch (\Exception $e) {
                                        Log::error("Study {$study->id}: Error getting file size for {$key}: ".$e->getMessage());
                                        fclose($streamRead);

                                        continue;
                                    }
                                } else {
                                    Log::error("Study {$study->id}: Could not open stream for reading: {$s3path}");
                                    throw new \Exception("Could not open stream for reading: {$s3path}");
                                }
                            }

                            Log::info("Study {$study->id}: Added {$addedFiles} files to archive");

                            try {
                                $zip->finish();
                                fclose($archiveDestination);

                                Storage::disk($filesystemDriver)->setVisibility($zipFilePath, 'public');

                                // Generate proper S3 URL for download
                                $s3Url = $this->generateS3Url($zipFilePath, $filesystemDriver, $bucket);
                                $study->download_url = $s3Url;
                                Log::info("Study {$study->id}: Archive created successfully at: {$s3Url}");
                            } catch (\Exception $e) {
                                // Clean up the partial file if it exists
                                if (Storage::disk($filesystemDriver)->exists($zipFilePath)) {
                                    Storage::disk($filesystemDriver)->delete($zipFilePath);
                                }
                                Log::error("Study {$study->id}: Error finalizing archive: ".$e->getMessage());
                                throw $e;
                            }
                        } catch (\Exception $e) {
                            Log::error("Study {$study->id}: Error during archive creation: ".$e->getMessage());
                            throw $e;
                        }

                        $study->internal_status = 'complete';
                        $study->save();
                    } else {
                        Log::info("Study {$study->id}: No fsObject found, marking complete");
                        $study->internal_status = 'complete';
                        $study->save();
                    }
                } else {
                    Log::info("Study {$study->id}: Already has download URL, marking complete");
                    $study->internal_status = 'complete';
                    $study->save();
                }
            }
        } else {
            Log::info('No project found');
        }

        Log::info('=== ArchiveStudy job COMPLETED for project: '.$this->project->id.' ===');
    }

    /**
     * Validate and correct filesystem object path to fix duplication issues.
     */
    private function validateAndCorrectPath(FileSystemObject $fsObject, int $studyId): string
    {
        $originalPath = $fsObject->path;
        $relativeUrl = $fsObject->relative_url;

        // Check if the relative URL appears twice in the path (duplication issue)
        if ($relativeUrl && substr_count($originalPath, $relativeUrl) > 1) {
            Log::warning("Study {$studyId}: Detected path duplication, correcting...");

            // Find the first occurrence and keep everything up to and including the first occurrence
            $firstOccurrence = strpos($originalPath, $relativeUrl);

            if ($firstOccurrence !== false) {
                $correctedPath = substr($originalPath, 0, $firstOccurrence + strlen($relativeUrl));
                Log::info("Study {$studyId}: Path corrected from {$originalPath} to {$correctedPath}");

                return $correctedPath;
            }
        }

        return $originalPath;
    }

    /**
     * Generate proper S3 URL for file download.
     */
    private function generateS3Url(string $filePath, string $filesystemDriver, string $bucket): string
    {
        $endpoint = config("filesystems.disks.{$filesystemDriver}.endpoint");
        $key = ltrim($filePath, '/');

        // For path-style S3 endpoints (like Ceph), format is: {endpoint}/{bucket}/{key}
        return rtrim($endpoint, '/').'/'.$bucket.'/'.$key;
    }

    // protected function standardizeMolecule($mol)
    // {
    //     $response = Http::post('https://api.cheminf.studio/latest/chem/standardize', $mol);
    //     return $response->json();
    // }

    // protected function processSpectra($url)
    // {
    //     $url = urlencode($url);
    //     $response = Http::post('https://nodejs.nmrxiv.org/spectra-parser', '{
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
