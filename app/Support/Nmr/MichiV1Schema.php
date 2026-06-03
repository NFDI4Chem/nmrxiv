<?php

namespace App\Support\Nmr;

/**
 * Declarative dictionary for the NFDI4Chem MIChI v1 NMR tabular spec.
 *
 * @see https://nfdi4chem.github.io/workshops/docs/michi/tabular/nmr/v1/table
 *
 * Each row mirrors one MIChI property and carries the metadata needed to
 * emit a DataCite Schema 4.4 `subjects[]` entry (or a Methods description
 * line for numeric/unit values):
 *
 *   - `id`            — NFDI4Chem property ID (e.g. `nfdi.nmr.acquisition.nucleus`)
 *   - `label`         — human-readable label
 *   - `level`         — 1 = required (L1), 2 = recommended (L2)
 *   - `cardinality`   — MIChI cardinality string ("1", "1-n", "0-1", "0-d", ...)
 *   - `ontologyIri`   — IRI of the ontology term that defines the property
 *   - `subjectScheme` — DataCite `subjectScheme` (CHEBI / NMRCV / CHMO / UO / SIO / IUPAC / TEXT)
 *   - `unitIri`       — IRI of the unit (UO/AFR) for numeric+unit fields, null otherwise
 *   - `unitDisplay`   — human-readable unit suffix used in the Methods description
 *   - `extractor`     — name of the method on `MetadataEnricher` that returns the value
 *   - `kind`          — 'subject' (controlled vocab term), 'numeric' (value+unit), 'boolean'
 *
 * The MetadataEnricher iterates this list. Adding a new MIChI field later
 * is a one-row append (plus an extractor method) without touching the
 * DataCite plumbing.
 */
class MichiV1Schema
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function rows(): array
    {
        return [
            // 1.1 NMR Sample
            [
                'id' => 'nfdi.nmr.sample.compound',
                'label' => 'Characterized Compound',
                'level' => 1,
                'cardinality' => '1-n',
                'ontologyIri' => 'http://purl.obolibrary.org/obo/CHEBI_23367',
                'subjectScheme' => 'CHEBI',
                'unitIri' => null,
                'unitDisplay' => null,
                'extractor' => 'compoundsFromSample',
                'kind' => 'subject',
            ],
            [
                'id' => 'nfdi.nmr.sample.solvent',
                'label' => 'NMR Solvent',
                'level' => 1,
                'cardinality' => '1-n',
                'ontologyIri' => 'http://purl.obolibrary.org/obo/CHEBI_197449',
                'subjectScheme' => 'CHEBI',
                'unitIri' => null,
                'unitDisplay' => null,
                'extractor' => 'solventFromNmriumInfo',
                'kind' => 'subject',
            ],

            // 1.2 NMR Acquisition Parameters
            [
                'id' => 'nfdi.nmr.acquisition.nucleus',
                'label' => 'Acquisition Nucleus',
                'level' => 1,
                'cardinality' => '1-d',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400083',
                'subjectScheme' => 'NMRCV',
                'unitIri' => null,
                'unitDisplay' => null,
                'extractor' => 'nucleusFromNmriumInfo',
                'kind' => 'subject',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.proton_frequency',
                'label' => 'Nominal Proton Frequency',
                'level' => 1,
                'cardinality' => '1-d',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400026',
                'subjectScheme' => 'NMRCV',
                'unitIri' => 'http://purl.obolibrary.org/obo/UO_0000325',
                'unitDisplay' => 'MHz',
                'extractor' => 'baseFrequencyFromNmriumInfo',
                'kind' => 'numeric',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.method',
                'label' => 'NMR Method',
                'level' => 1,
                'cardinality' => '1',
                'ontologyIri' => 'http://purl.obolibrary.org/obo/CHMO_0000613',
                'subjectScheme' => 'CHMO',
                'unitIri' => null,
                'unitDisplay' => null,
                'extractor' => 'methodFromNmriumInfo',
                'kind' => 'subject',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.pulse',
                'label' => 'Pulse Sequence Name',
                'level' => 1,
                'cardinality' => '1',
                'ontologyIri' => null,
                'subjectScheme' => 'TEXT',
                'unitIri' => null,
                'unitDisplay' => null,
                'extractor' => 'pulseSequenceFromNmriumInfo',
                'kind' => 'subject',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.relaxation_delay',
                'label' => 'Relaxation Delay',
                'level' => 2,
                'cardinality' => '0-1',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400090',
                'subjectScheme' => 'NMRCV',
                'unitIri' => 'http://purl.obolibrary.org/obo/UO_0000010',
                'unitDisplay' => 's',
                'extractor' => 'relaxationDelayFromNmriumInfo',
                'kind' => 'numeric',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.number_of_acquisition_data_points',
                'label' => 'Number of Acquisition Data Points',
                'level' => 2,
                'cardinality' => '0-d',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400017',
                'subjectScheme' => 'NMRCV',
                'unitIri' => 'http://purl.allotrope.org/ontologies/result#AFR_0000186',
                'unitDisplay' => 'data points',
                'extractor' => 'numberOfPointsFromNmriumInfo',
                'kind' => 'numeric',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.temperature',
                'label' => 'Sample Temperature Information',
                'level' => 2,
                'cardinality' => '0-1',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400262',
                'subjectScheme' => 'NMRCV',
                'unitIri' => 'http://purl.obolibrary.org/obo/UO_0000012',
                'unitDisplay' => 'K',
                'extractor' => 'temperatureFromNmriumInfo',
                'kind' => 'numeric',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.number_of_scans',
                'label' => 'Number of Scans',
                'level' => 2,
                'cardinality' => '0-1',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400087',
                'subjectScheme' => 'NMRCV',
                'unitIri' => null,
                'unitDisplay' => 'scans',
                'extractor' => 'numberOfScansFromNmriumInfo',
                'kind' => 'numeric',
            ],
            [
                'id' => 'nfdi.nmr.acquisition.spectral_width',
                'label' => 'Spectral Width',
                'level' => 2,
                'cardinality' => '0-d',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1000175',
                'subjectScheme' => 'NMRCV',
                'unitIri' => 'http://purl.obolibrary.org/obo/UO_0000106',
                'unitDisplay' => 'Hz',
                'extractor' => 'spectralWidthFromNmriumInfo',
                'kind' => 'numeric',
            ],

            // 1.3 NMR Instrument
            [
                'id' => 'nfdi.nmr.instrument.probe',
                'label' => 'NMR Probe',
                'level' => 2,
                'cardinality' => '0-1',
                'ontologyIri' => 'http://nmrML.org/nmrCV#NMR_1400014',
                'subjectScheme' => 'NMRCV',
                'unitIri' => null,
                'unitDisplay' => null,
                'extractor' => 'probeFromNmriumInfo',
                'kind' => 'subject',
            ],
        ];
    }
}
