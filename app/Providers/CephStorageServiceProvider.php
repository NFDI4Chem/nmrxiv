<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

/**
 * Register Ceph S3-compatible storage driver for Laravel Storage.
 *
 * This provider extends Laravel's Storage facade to support Ceph object storage
 * as an S3-compatible filesystem driver, enabling seamless file operations.
 */
class CephStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        Storage::extend('ceph', function ($app, $config) {
            $clientConfig = [
                'region' => config('filesystems.disks.ceph.region'),
                'version' => 'latest',
                'use_path_style_endpoint' => true,
                'endpoint' => config('filesystems.disks.ceph.endpoint'),
                'credentials' => [
                    'key' => config('filesystems.disks.ceph.key'),
                    'secret' => config('filesystems.disks.ceph.secret'),
                ],
            ];

            $client = new S3Client($clientConfig);
            $adapter = new AwsS3V3Adapter(
                $client,
                config('filesystems.disks.ceph.bucket'),
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
