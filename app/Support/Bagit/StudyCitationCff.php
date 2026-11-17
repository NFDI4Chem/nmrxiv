<?php

namespace App\Support\Bagit;

use App\Models\Study;
use App\Support\Metadata\StudyAttribution;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Builds a Citation File Format 1.2.0 document that tells people how to cite
 * a single study (typed as a dataset), from the same attribution the
 * Bioschema API uses.
 *
 * @link https://github.com/citation-file-format/citation-file-format/blob/main/schema-guide.md
 */
class StudyCitationCff
{
    public const FILE_NAME = 'CITATION.cff';

    public static function build(Study $study, ?array $attribution = null): string
    {
        $attribution ??= StudyAttribution::resolve($study);
        $study->loadMissing('license');

        $doi = $study->doi ? preg_replace('#^https?://(dx\.)?doi\.org/#i', '', trim($study->doi)) : null;
        $studyUrl = rtrim(config('app.url'), '/').'/'.str_replace('NMRXIV:', '', (string) $study->identifier);

        $lines = [
            'cff-version: 1.2.0',
            'message: '.self::scalar('If you use this dataset, please cite it as below.'),
            'type: dataset',
            'title: '.self::scalar($study->name ?: $study->identifier),
        ];

        if ($study->description) {
            $lines[] = 'abstract: '.self::scalar(Str::limit(trim(preg_replace('/\s+/', ' ', $study->description)), 1000, '...'));
        }

        $lines[] = 'authors:';
        foreach (self::authors($attribution['authors']) as $authorLines) {
            array_push($lines, ...$authorLines);
        }

        $lines[] = 'identifiers:';
        if ($doi) {
            $lines[] = '  - type: doi';
            $lines[] = '    value: '.self::scalar($doi);
        }
        $lines[] = '  - type: url';
        $lines[] = '    value: '.self::scalar($studyUrl);

        if ($doi) {
            $lines[] = 'doi: '.self::scalar($doi);
        }

        $lines[] = 'repository: '.self::scalar(rtrim(config('app.url'), '/'));
        $lines[] = 'url: '.self::scalar($studyUrl);

        if ($study->license?->spdx_id) {
            $lines[] = 'license: '.self::scalar($study->license->spdx_id);
        }

        if ($study->release_date) {
            $lines[] = 'date-released: '.Carbon::parse($study->release_date)->toDateString();
        }

        $references = self::references($attribution['citations']);
        if ($references !== []) {
            $lines[] = 'references:';
            foreach ($references as $referenceLines) {
                array_push($lines, ...$referenceLines);
            }
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * authors is a required CFF key, so fall back to the repository itself
     * when nobody can be attributed.
     *
     * @return array<int, array<int, string>>
     */
    protected static function authors(array $authors): array
    {
        if ($authors === []) {
            return [['  - name: '.self::scalar(config('app.name'))]];
        }

        return array_map(function (array $author) {
            $lines = [];
            $keys = [];

            if ($author['family_name']) {
                $keys['family-names'] = $author['family_name'];
            }
            if ($author['given_name']) {
                $keys['given-names'] = $author['given_name'];
            }
            if ($author['orcid']) {
                $keys['orcid'] = $author['orcid'];
            }
            if ($author['affiliation']) {
                $keys['affiliation'] = $author['affiliation'];
            }

            foreach ($keys as $key => $value) {
                $lines[] = ($lines === [] ? '  - ' : '    ').$key.': '.self::scalar($value);
            }

            return $lines;
        }, $authors);
    }

    /**
     * CFF references need a title and authors. The stored citation authors are
     * one free-text string, so they are split on ";" only and kept as given
     * otherwise; entries missing either field are left out.
     *
     * @return array<int, array<int, string>>
     */
    protected static function references(array $citations): array
    {
        $references = [];

        foreach ($citations as $citation) {
            if (! $citation['title'] || ! $citation['authors']) {
                continue;
            }

            $lines = [
                '  - type: article',
                '    title: '.self::scalar($citation['title']),
                '    authors:',
            ];

            foreach (array_filter(array_map('trim', explode(';', $citation['authors']))) as $name) {
                $lines[] = '      - name: '.self::scalar($name);
            }

            if ($citation['doi']) {
                $lines[] = '    doi: '.self::scalar($citation['doi']);
            }

            $references[] = $lines;
        }

        return $references;
    }

    /**
     * A JSON string is a valid YAML double-quoted scalar and escapes every
     * character that would otherwise need care.
     */
    protected static function scalar(string $value): string
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
