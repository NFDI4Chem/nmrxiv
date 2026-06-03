<?php

namespace App\Support\Nmr;

/**
 * Common deuterated NMR solvent display name → ChEBI IRI lookup.
 *
 * The MIChI v1 spec (NFDI4Chem) requires `nfdi.nmr.sample.solvent` to be
 * encoded as a ChEBI ID for a molecular entity that has the role
 * "NMR solvent" (CHEBI:197449). Our `nmrium_info.solvent` payloads are
 * usually plain display strings ("CDCl3", "DMSO-d6"), so this map closes
 * the gap by emitting the corresponding ChEBI IRI as a DataCite
 * `subjects[].valueURI` whenever the name is recognised.
 *
 * Unknown names fall back to free-text subjects (no `valueURI`), which is
 * the correct behaviour per DataCite 4.4 — the value is still useful to
 * humans even without a controlled-vocabulary backing.
 */
class SolventChebiMap
{
    /**
     * Map of normalized solvent display name → ChEBI IRI.
     *
     * Keys are lower-cased, with all common separators stripped so the
     * lookup tolerates "CDCl3", "cdcl3", "CDCl₃", and "CDCl-3".
     *
     * @var array<string, string>
     */
    private const ENTRIES = [
        'cdcl3' => 'http://purl.obolibrary.org/obo/CHEBI_85365',          // chloroform-d
        'dmsod6' => 'http://purl.obolibrary.org/obo/CHEBI_193041',         // DMSO-d6
        'd2o' => 'http://purl.obolibrary.org/obo/CHEBI_41981',             // deuterium oxide
        'meod' => 'http://purl.obolibrary.org/obo/CHEBI_85369',            // methanol-d4
        'cd3od' => 'http://purl.obolibrary.org/obo/CHEBI_85369',           // methanol-d4
        'methanold4' => 'http://purl.obolibrary.org/obo/CHEBI_85369',
        'c6d6' => 'http://purl.obolibrary.org/obo/CHEBI_85364',            // benzene-d6
        'benzened6' => 'http://purl.obolibrary.org/obo/CHEBI_85364',
        'acetoned6' => 'http://purl.obolibrary.org/obo/CHEBI_193039',      // acetone-d6
        'cd3cocd3' => 'http://purl.obolibrary.org/obo/CHEBI_193039',
        'thfd8' => 'http://purl.obolibrary.org/obo/CHEBI_193044',          // THF-d8
        'tolueneD8' => 'http://purl.obolibrary.org/obo/CHEBI_193040',      // toluene-d8
        'toluened8' => 'http://purl.obolibrary.org/obo/CHEBI_193040',
        'cd3cn' => 'http://purl.obolibrary.org/obo/CHEBI_177275',          // acetonitrile-d3
        'acetonitriled3' => 'http://purl.obolibrary.org/obo/CHEBI_177275',
        'pyridined5' => 'http://purl.obolibrary.org/obo/CHEBI_193043',     // pyridine-d5
    ];

    /**
     * Resolve a solvent display name to its ChEBI IRI, or null when unknown.
     */
    public static function lookup(?string $solventName): ?string
    {
        if ($solventName === null) {
            return null;
        }

        $key = self::normalize($solventName);
        if ($key === '') {
            return null;
        }

        return self::ENTRIES[$key] ?? null;
    }

    /**
     * Normalize a free-text solvent name for lookup. Strips whitespace,
     * underscores, hyphens, dots, and the common subscript characters
     * (₀-₉) so "DMSO-d6", "DMSO d6", "DMSO_d6", and "dmsod6" all collapse
     * to the same key.
     */
    private static function normalize(string $name): string
    {
        $subscripts = ['₀', '₁', '₂', '₃', '₄', '₅', '₆', '₇', '₈', '₉'];
        $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $name = str_replace($subscripts, $digits, $name);

        $name = strtolower($name);
        $name = preg_replace('/[\s\-_\.]+/', '', $name) ?? $name;

        return $name;
    }
}
