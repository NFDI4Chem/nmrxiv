<?php

namespace App\Services;

use App\Models\Draft;
use App\Models\Project;

/**
 * Generate file and directory paths for different contexts.
 *
 * This service handles path generation for both draft and project uploads,
 * ensuring consistent path structures across the application.
 */
class PathGeneratorService
{
    /**
     * Generate file path for draft uploads.
     */
    public function generateDraftFilePath(Draft $draft, string $relativeFilePath): string
    {
        return $this->normalizeSlashes('/' . $draft->path . '/' . $relativeFilePath);
    }

    /**
     * Generate file path for project uploads.
     */
    public function generateProjectFilePath(Project $project, string $relativeFilePath): string
    {
        $environment = config('app.env', 'local');
        return $this->normalizeSlashes($environment . '/' . $project->uuid . '/' . $relativeFilePath);
    }

    /**
     * Generate relative file path from destination and file information.
     */
    public function generateRelativeFilePath(array $file, string $destination): string
    {
        $filename = '/' . $file['upload']['filename'];
        $path = $file['fullPath'] ?? null;
        
        $relativeFilePath = $path ? $path : $filename;
        return $destination . '/' . $relativeFilePath;
    }

    /**
     * Parse directory structure from a given path.
     */
    public function parseDirectories(string $path, string $filename): array
    {
        return array_values(
            array_filter(
                explode('/', str_replace($filename, '', $path))
            )
        );
    }

    /**
     * Check if a path contains directories beyond the destination.
     */
    public function hasDirectories(?string $path, string $destination): bool
    {
        return $path || $destination != '/';
    }

    /**
     * Normalize multiple consecutive slashes in a path.
     */
    private function normalizeSlashes(string $path): string
    {
        return preg_replace('~//+~', '/', $path);
    }
}
