<?php

namespace App\Support\Hifsa;

/**
 * NMRium wrapper fileFilter for samples that include HiFSA / Cosmic Truth data.
 *
 * Cosmic Truth exports nest EXTRA/ (and often a hifsa/ analysis folder) inside
 * the study archive. NMRium recursively unpacks those paths and can treat them
 * as spectra/molecules. The wrapper load event accepts a fileFilter that is
 * applied for type "url" / "file" loads — see
 * https://github.com/NFDI4Chem/nmrium-react-wrapper/wiki/3.-Wrapper-Events
 *
 * Important: do not set include to []. In file-collection, an empty include
 * array is truthy and matches nothing, so every file is dropped.
 */
class HifsaNmriumFileFilter
{
    /**
     * Path substrings excluded from NMRium parsing when HiFSA data is present.
     * file-collection matches with path.includes(pattern) (case-sensitive).
     *
     * @var list<string>
     */
    public const EXCLUDE = [
        'EXTRA/',
        'hifsa/',
        'HiFSA/',
        'HIFSA/',
    ];

    /**
     * True when the study (or study-like array/object) has HiFSA payload or PDF.
     */
    public static function studyHasHifsa(mixed $study): bool
    {
        if ($study === null) {
            return false;
        }

        $hifsaData = self::value($study, 'hifsa_data');
        $hifsaPdfUrl = self::value($study, 'hifsa_pdf_url');

        if (is_array($hifsaData) && $hifsaData !== []) {
            return true;
        }

        return is_string($hifsaPdfUrl) && trim($hifsaPdfUrl) !== '';
    }

    /**
     * Wrapper fileFilter for HiFSA samples, or null when filtering is not needed.
     *
     * Only `exclude` is set — never pass an empty `include` array.
     *
     * @return array{exclude: list<string>}|null
     */
    public static function forStudy(mixed $study): ?array
    {
        if (! self::studyHasHifsa($study)) {
            return null;
        }

        return [
            'exclude' => self::EXCLUDE,
        ];
    }

    private static function value(mixed $study, string $key): mixed
    {
        if (is_array($study)) {
            return $study[$key] ?? null;
        }

        if (is_object($study)) {
            return $study->{$key} ?? null;
        }

        return null;
    }
}
