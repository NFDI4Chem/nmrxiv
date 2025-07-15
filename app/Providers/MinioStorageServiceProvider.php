<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

/**
 * MinIO Storage Service Provider
 *
 * This service provider extends Laravel's Storage facade to support MinIO object storage
 * as an S3-compatible filesystem driver. MinIO serves as an alternative storage backend
 * for NMRXIV, particularly useful for development, testing, and local deployments where
 * a lightweight, self-hosted object storage solution is preferred.
 *
 * The provider configures an S3-compatible client to communicate with MinIO instances,
 * enabling seamless file operations through Laravel's unified Storage interface.
 *
 * @package App\Providers
 * @author NMRXIV Development Team
 * @since 1.0.0
 */
class MinioStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * Registers the 'minio' storage driver with Laravel's Storage facade,
     * configuring it as an S3-compatible filesystem using the AWS SDK.
     * This allows the application to store and retrieve files from MinIO
     * object storage using standard Laravel Storage methods.
     *
     * @return void
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
     *
     * This method is intentionally empty as the MinIO storage driver
     * is configured during the boot phase when all configuration
     * values are available.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
