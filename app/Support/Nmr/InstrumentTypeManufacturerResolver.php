<?php

namespace App\Support\Nmr;

use App\Models\Dataset;
use App\Models\FileSystemObject;

/**
 * Maps sample folder `instrument_type` values (set during upload processing)
 * to canonical manufacturer labels used in metadata search and statistics.
 */
class InstrumentTypeManufacturerResolver
{
    /**
     * @var array<string, string>
     */
    private const MANUFACTURERS = [
        'bruker' => 'Bruker',
        'joel' => 'JEOL',
        'magritek' => 'Magritek',
        'jcamp' => 'JCAMP',
        'varian' => 'Varian',
    ];

    public static function forDataset(Dataset $dataset): ?string
    {
        return self::toManufacturer(self::resolveInstrumentType($dataset));
    }

    public static function toManufacturer(?string $instrumentType): ?string
    {
        if ($instrumentType === null || $instrumentType === '') {
            return null;
        }

        return self::MANUFACTURERS[strtolower($instrumentType)] ?? null;
    }

    private static function resolveInstrumentType(Dataset $dataset): ?string
    {
        $dataset->loadMissing([
            'fsObject.children',
            'fsObject.parent',
        ]);

        if ($dataset->fsObject === null) {
            return null;
        }

        $instrumentType = self::instrumentTypeFromNode($dataset->fsObject);
        if ($instrumentType !== null) {
            return $instrumentType;
        }

        return self::instrumentTypeFromAncestors($dataset->fsObject);
    }

    private static function instrumentTypeFromNode(FileSystemObject $node): ?string
    {
        if (filled($node->instrument_type)) {
            return (string) $node->instrument_type;
        }

        foreach ($node->children as $child) {
            if (filled($child->instrument_type)) {
                return (string) $child->instrument_type;
            }
        }

        return null;
    }

    private static function instrumentTypeFromAncestors(FileSystemObject $node): ?string
    {
        $parent = $node->parent;

        while ($parent !== null) {
            if (filled($parent->instrument_type)) {
                return (string) $parent->instrument_type;
            }

            $parent->loadMissing('parent');
            $parent = $parent->parent;
        }

        return null;
    }
}
