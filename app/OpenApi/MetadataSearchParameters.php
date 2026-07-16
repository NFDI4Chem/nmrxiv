<?php

namespace App\OpenApi;

/**
 * Reusable OpenAPI query parameters for metadata search endpoints.
 *
 * @OA\Parameter(
 *     parameter="metadataSearchQ",
 *     name="q",
 *     in="query",
 *     required=false,
 *     description="Free-text search across denormalized NMRium metadata (`spectra_search_text`). Multi-word queries use AND semantics.",
 *
 *     @OA\Schema(type="string", maxLength=1000, example="caffeine hsqc")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchSolvent",
 *     name="solvent",
 *     in="query",
 *     required=false,
 *     description="NMR solvent (exact match on `spectra_solvent`).",
 *
 *     @OA\Schema(type="string", maxLength=255, example="CDCl3")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchTemperature",
 *     name="temperature",
 *     in="query",
 *     required=false,
 *     description="Sample temperature in kelvin (`spectra_temperature`). Integer values match ±0.5 K.",
 *
 *     @OA\Schema(type="number", format="float", example=294)
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchTubeDiameter",
 *     name="tube_diameter",
 *     in="query",
 *     required=false,
 *     description="Sample tube diameter in millimetres (`spectra_tube_diameter`).",
 *
 *     @OA\Schema(type="string", enum={"3", "5", "10"}, example="5")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchNucleus",
 *     name="nucleus",
 *     in="query",
 *     required=false,
 *     description="Acquisition nucleus (`spectra_nucleus`).",
 *
 *     @OA\Schema(type="string", example="1H")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchProtonFrequency",
 *     name="proton_frequency",
 *     in="query",
 *     required=false,
 *     description="Observed base frequency in MHz (`spectra_base_frequency`). Matches ±0.5 MHz.",
 *
 *     @OA\Schema(type="number", format="float", example=600)
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchNmrMethod",
 *     name="nmr_method",
 *     in="query",
 *     required=false,
 *     description="NMR experiment / method (`spectra_experiment`).",
 *
 *     @OA\Schema(type="string", maxLength=255, example="hsqc")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchPulseSequence",
 *     name="pulse_sequence",
 *     in="query",
 *     required=false,
 *     description="Pulse sequence name (`spectra_pulse_sequence`).",
 *
 *     @OA\Schema(type="string", maxLength=255, example="zg30")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchNumberOfScans",
 *     name="number_of_scans",
 *     in="query",
 *     required=false,
 *     description="Number of scans (`spectra_number_of_scans`).",
 *
 *     @OA\Schema(type="integer", minimum=1, example=16)
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchManufacturer",
 *     name="manufacturer",
 *     in="query",
 *     required=false,
 *     description="Instrument vendor from the sample folder type (`instrument_type`: Bruker, JEOL, Magritek, JCAMP) stored in `spectra_manufacturer`.",
 *
 *     @OA\Schema(type="string", maxLength=255, example="Bruker")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchInstrumentModel",
 *     name="instrument_model",
 *     in="query",
 *     required=false,
 *     description="Probe name (`spectra_probe_name`).",
 *
 *     @OA\Schema(type="string", maxLength=255, example="BBO")
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchPerPage",
 *     name="per_page",
 *     in="query",
 *     required=false,
 *     description="Results per entity group (default: 12, max: 24).",
 *
 *     @OA\Schema(type="integer", minimum=1, maximum=24, default=12)
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchStudiesPage",
 *     name="studies_page",
 *     in="query",
 *     required=false,
 *     description="Page number for sample (study) results.",
 *
 *     @OA\Schema(type="integer", minimum=1, default=1)
 * )
 *
 * @OA\Parameter(
 *     parameter="metadataSearchDatasetsPage",
 *     name="datasets_page",
 *     in="query",
 *     required=false,
 *     description="Page number for spectra (dataset) results.",
 *
 *     @OA\Schema(type="integer", minimum=1, default=1)
 * )
 */
class MetadataSearchParameters {}
