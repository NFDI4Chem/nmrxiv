<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

/**
 * Ceph Storage Service Provider
 *
 * This service provider extends Laravel's Storage facade to support Ceph object storage
 * as an S3-compatible filesystem driver. Ceph is used as the primary storage backend
 * for NMRXIV, providing scalable and reliable object storage for NMR research data,
 * spectra files, and related scientific assets.
 *
 * The provider configures an S3-compatible client to communicate with Ceph clusters,
 * enabling seamless file operations through Laravel's unified Storage interface.
 *
 * @author NMRXIV Development Team
 *
 * @since 1.0.0
 */
class CephStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * Registers the 'ceph' storage driver with Laravel's Storage facade,
     * configuring it as an S3-compatible filesystem using the AWS SDK.
     * This allows the application to store and retrieve files from Ceph
     * object storage using standard Laravel Storage methods.
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
     *
     * This method is intentionally empty as the Ceph storage driver
     * is configured during the boot phase when all configuration
     * values are available.
     */
    public function register(): void
    {
        //
    }
}
