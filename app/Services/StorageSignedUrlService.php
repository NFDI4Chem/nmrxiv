<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Generate signed URLs for S3-compatible storage operations.
 *
 * This service handles the creation of pre-signed URLs for secure file uploads
 * to S3-compatible storage systems (like Ceph) without exposing credentials.
 */
class StorageSignedUrlService
{
    private S3Client $client;
    private string $bucket;

    public function __construct()
    {
        $this->client = $this->createStorageClient();
        $this->bucket = $this->getDefaultBucket();
    }

    /**
     * Generate signed URL for secure file upload.
     */
    public function generateSignedUploadUrl(string $filePath, ?string $bucket = null): array
    {
        $bucket = $bucket ?: $this->bucket;
        $key = ltrim($filePath, '/');

        $command = $this->client->getCommand('putObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);

        $signedRequest = $this->client->createPresignedRequest($command, '+90 minutes');

        return [
            'uuid' => (string) Str::uuid(),
            'bucket' => $bucket,
            'key' => $key,
            'url' => (string) $signedRequest->getUri(),
            'headers' => $this->getHeaders($signedRequest),
        ];
    }

    /**
     * Generate multiple signed URLs for batch file uploads.
     */
    public function generateMultipleSignedUrls(array $filePaths, ?string $bucket = null): array
    {
        $urls = [];
        
        foreach ($filePaths as $path => $metadata) {
            $signedUrl = $this->generateSignedUploadUrl($path, $bucket);
            $signedUrl = array_merge($signedUrl, $metadata);
            $urls[] = $signedUrl;
        }

        return $urls;
    }

    /**
     * Create S3 storage client using application configuration.
     */
    private function createStorageClient(): S3Client
    {
        $driver = config('filesystems.default');
        
        $config = [
            'region' => config("filesystems.disks.{$driver}.region"),
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            'url' => config("filesystems.disks.{$driver}.endpoint"),
            'endpoint' => config("filesystems.disks.{$driver}.endpoint"),
            'credentials' => [
                'key' => config("filesystems.disks.{$driver}.key"),
                'secret' => config("filesystems.disks.{$driver}.secret"),
            ],
        ];

        return S3Client::factory($config);
    }

    /**
     * Get default bucket from filesystem configuration.
     */
    private function getDefaultBucket(): string
    {
        $driver = config('filesystems.default');
        return config("filesystems.disks.{$driver}.bucket");
    }

    /**
     * Get headers for signed request with content type.
     */
    private function getHeaders($signedRequest, string $contentType = 'application/octet-stream'): array
    {
        return array_merge($signedRequest->getHeaders(), [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * Get the current bucket name.
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * Get the S3 client instance.
     */
    public function getClient(): S3Client
    {
        return $this->client;
    }
}
