<?php

namespace App\Support;

use App\Models\Draft;
use RuntimeException;

class ProvisionalDoi
{
    /**
     * Build a placeholder DOI string for a draft (not registered with DataCite).
     */
    public static function forDraft(Draft $draft): string
    {
        $prefix = config('doi.datacite.prefix');
        if (! is_string($prefix) || trim($prefix) === '') {
            throw new RuntimeException('DOI prefix is not configured.');
        }

        $prefix = trim($prefix);
        // Env sometimes mistakenly includes a leading "datacite/"; DOI must be "10.xxx/…" only.
        $prefix = preg_replace('#^datacite/#i', '', $prefix) ?? $prefix;
        $prefix = ltrim($prefix, '/');

        $key = (string) $draft->getAttribute('key');
        $segment = explode('-', $key, 2)[0] ?? '';

        if ($segment === '') {
            throw new RuntimeException('Draft key is missing.');
        }

        return $prefix.'/nmrxiv.'.$segment;
    }
}
