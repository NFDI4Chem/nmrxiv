<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

/**
 * Register MinIO S3-compatible storage driver for Laravel Storage.
 *
 * This provider extends Laravel's Storage facade to support MinIO object storage
 * as an S3-compatible filesystem driver, enabling seamless file operations.
 */
class MinioStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        Storage::extend('minio', function ($app, $config) {
            $clientConfig = [
                'region' => config('filesystems.disks.minio.region'),
                'version' => 'latest',
                'use_path_style_endpoint' => true,
                'endpoint' => config('filesystems.disks.minio.endpoint'),
                'credentials' => [
                    'key' => config('filesystems.disks.minio.key'),
                    'secret' => config('filesystems.disks.minio.secret'),
                ],
            ];

            $client = new S3Client($clientConfig);
            $adapter = new AwsS3V3Adapter(
                $client,
                config('filesystems.disks.minio.bucket'),
                '',
                null,
                null,
                ['override_visibility_on_copy' => true]
            );

            return new Filesystem($adapter);
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
