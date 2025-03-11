<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Storage;

class MinioStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        Storage::extend('minio', function ($app, $config) {
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

            $client = new S3Client($config);
            $options = [
                'override_visibility_on_copy' => true,
            ];

            return new Filesystem(
                new AwsS3Adapter($client, $config['bucket'], '', $options)
            );
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void {}
}
