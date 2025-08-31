<?php

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

function resolveIdentifier($identifier)
{
    if (str_contains($identifier, 'NMRXIV:')) {
        $identifier = str_replace('NMRXIV:', '', $identifier);
    }

    preg_match('/(P|S|D|M|p|s|d|m)[0-9]+/', $identifier, $matches);

    if (count($matches) > 0) {
        $mapping = [
            'P' => 'Project',
            'S' => 'Study',
            'D' => 'Dataset',
            'M' => 'Molecule',
        ];

        $namespace = $mapping[strtoupper((substr($identifier, 0, 1)))];

        $id = substr($identifier, 1);

        $model = null;

        try {
            if ($namespace == 'Project') {
                $model = Project::where([['identifier', $id]])->firstOrFail();
            } elseif ($namespace == 'Study') {
                $model = Study::where([['identifier', $id]])->firstOrFail();
            } elseif ($namespace == 'Dataset') {
                $model = Dataset::where([['identifier', $id]])->firstOrFail();
            }

            return [
                'namespace' => $namespace,
                'model' => $model,
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'namespace' => $namespace,
                'model' => null,
            ];
        }
    }

    return [
        'namespace' => null,
        'model' => null,
    ];
}

function NMRiumMockData($type = null)
{
    if ($type) {
        $nmriumDataTypeMapping = [
            'proton' => 'https://nmrxiv.org/datasets/1444/nmriumInfo',
            '13c' => 'https://nmrxiv.org/datasets/1447/nmriumInfo',
            'dept' => 'https://nmrxiv.org/datasets/1448/nmriumInfo',
            'hsqc' => 'https://nmrxiv.org/datasets/1446/nmriumInfo',
            'hmbc' => 'https://nmrxiv.org/datasets/1451/nmriumInfo',
        ];

        $response = Http::get($nmriumDataTypeMapping[$type]);

        if ($response) {
            return $response['nmrium_info'];
        }

        return json_encode('{}');
    }

    return json_encode('{}');
}

/**
 * Sanitize Unicode characters in NMRium data to prevent SQL_ASCII encoding issues
 */
function sanitizeUnicodeInNMRiumData(array $data): array
{
    // Recursively sanitize array data
    array_walk_recursive($data, function (&$value, $key) {
        if (is_string($value)) {
            // Handle file paths and other strings that might contain Unicode
            $value = sanitizeUnicodeString($value);
        }
    });

    return $data;
}

/**
 * Sanitize Unicode characters in array data to prevent SQL_ASCII encoding issues
 *
 * @param  array|null  $data
 * @return array|null
 */
function sanitizeUnicodeInArray($data)
{
    if ($data === null) {
        return null;
    }

    if (! is_array($data)) {
        return $data;
    }

    // Recursively sanitize array data
    array_walk_recursive($data, function (&$value, $key) {
        if (is_string($value)) {
            // Handle file paths and other strings that might contain Unicode
            $value = sanitizeUnicodeString($value);
        }
    });

    return $data;
}

/**
 * Sanitize Unicode characters in a string to prevent SQL_ASCII encoding issues
 */
function sanitizeUnicodeString(string $input): string
{
    // Convert Unicode escape sequences to their actual characters, then transliterate
    $decoded = json_decode('"'.str_replace('"', '\\"', $input).'"');

    if ($decoded === null) {
        // If JSON decode fails, work with the original string
        $decoded = $input;
    }

    // Common Unicode character mappings for file paths
    $unicodeMap = [
        // Full-width parentheses to regular parentheses
        '（' => '(',  // U+FF08
        '）' => ')',  // U+FF09
        // Full-width plus to regular plus
        '＋' => '+',  // U+FF0B
        // Other common full-width characters
        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
        '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
        '－' => '-',  // U+FF0D (full-width hyphen)
        '．' => '.',  // U+FF0E (full-width period)
    ];

    // Replace known problematic Unicode characters
    $sanitized = str_replace(array_keys($unicodeMap), array_values($unicodeMap), $decoded);

    // Use transliteration to convert remaining Unicode characters to ASCII
    if (function_exists('iconv')) {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $sanitized);
        if ($transliterated !== false) {
            $sanitized = $transliterated;
        }
    }

    // Remove any remaining non-ASCII characters as a fallback, but preserve newlines and tabs
    $sanitized = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $sanitized);

    return $sanitized;
}
