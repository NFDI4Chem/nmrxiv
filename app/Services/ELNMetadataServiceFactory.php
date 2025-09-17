<?php

namespace App\Services;

use App\Services\ELN\ChemotionMetadataService;
use App\Services\ELN\ELNMetadataExtractorInterface;
use InvalidArgumentException;

/**
 * Factory for creating ELN-specific metadata services.
 *
 * This factory handles the creation of appropriate metadata extractors
 * based on the ELN type, making it easy to add new ELN integrations.
 */
class ELNMetadataServiceFactory
{
    /**
     * Create a metadata extractor for the specified ELN type.
     */
    public static function create(string $elnType): ELNMetadataExtractorInterface
    {
        return match (strtolower($elnType)) {
            'chemotion' => new ChemotionMetadataService(app(FileIntegrityService::class)),
            default => throw new InvalidArgumentException("Unsupported ELN type: {$elnType}")
        };
    }

    /**
     * Get all supported ELN types.
     */
    public static function getSupportedELNTypes(): array
    {
        return [
            'chemotion',
            // Add more ELN types here as they are implemented
        ];
    }

    /**
     * Check if an ELN type is supported.
     */
    public static function isSupported(string $elnType): bool
    {
        return in_array(strtolower($elnType), self::getSupportedELNTypes());
    }
}
