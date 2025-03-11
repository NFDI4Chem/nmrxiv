<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Storage;

class CephStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        Storage::extend('ceph', function ($app, $config) {
            $config = [
                'region' => config('filesystems.disks.ceph.region'),
                'version' => 'latest',
                'use_path_style_endpoint' => true,
                'url' => config('filesystems.disks.ceph.endpoint'),
                'endpoint' => config('filesystems.disks.ceph.endpoint'),
                'credentials' => [
                    'key' => config('filesystems.disks.ceph.key'),
                    'secret' => config('filesystems.disks.ceph.secret'),
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
