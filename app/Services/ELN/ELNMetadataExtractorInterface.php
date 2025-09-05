<?php

namespace App\Services\ELN;

use App\Models\Draft;

/**
 * Interface for ELN-specific metadata extractors.
 * 
 * Each ELN integration should implement this interface to provide
 * standardized metadata extraction capabilities.
 */
interface ELNMetadataExtractorInterface
{
    /**
     * Extract molecule information.
     */
    public function extractMolecules(array $metadata): array;

    /**
     * Extract analyses information.
     */
    public function extractAnalyses(array $metadata): array;

    /**
     * Extract all metadata in a structured format.
     */
    public function extractAllMetadata(array $metadata): array;

    /**
     * Validate the metadata structure for this ELN.
     */
    public function validateMetadata(array $metadata): bool;

    /**
     * Get the ELN type this extractor handles.
     */
    public function getELNType(): string;
}
