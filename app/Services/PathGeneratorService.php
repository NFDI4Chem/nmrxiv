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
        return $this->normalizeSlashes('/'.$draft->path.'/'.$relativeFilePath);
    }

    /**
     * Generate file path for project uploads.
     */
    public function generateProjectFilePath(Project $project, string $relativeFilePath): string
    {
        $environment = config('app.env', 'local');

        return $this->normalizeSlashes($environment.'/'.$project->uuid.'/'.$relativeFilePath);
    }

    /**
     * Generate relative file path from destination and file information.
     */
    public function generateRelativeFilePath(array $file, string $destination): string
    {
        $filename = $file['upload']['filename'];
        $path = $file['fullPath'] ?? null;

        // Use the full path if available, otherwise just the filename
        $relativeFilePath = $path ? $path : $filename;

        // Clean up the destination path
        $destination = trim($destination, '/');

        // If we have a destination and it's not empty or root
        if (! empty($destination) && $destination !== '/') {
            // Check if the relative path already contains the destination
            if (! str_starts_with($relativeFilePath, $destination)) {
                return $this->normalizeSlashes($destination.'/'.$relativeFilePath);
            }
        }

        // Ensure the path starts with a single slash and normalize
        return $this->normalizeSlashes('/'.$relativeFilePath);
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
    public function normalizeSlashes(string $path): string
    {
        return preg_replace('~//+~', '/', $path);
    }
}
