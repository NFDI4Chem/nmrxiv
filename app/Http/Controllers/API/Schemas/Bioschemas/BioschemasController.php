<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Sample;
use App\Models\Schemas\Bioschemas;
use App\Models\Study;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\SchemaOrg\Schema;

/**
 * Implement Bioschemas types on nmrXiv project, study, and dataset, including the
 * samples and molecules details.
 */
class BioschemasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/schemas/bioschemas/{username}/{project}",
     *     operationId="getBioschemasMetadataByName",
     *     tags={"Scientific Metadata Schemas"},
     *     summary="Retrieve Bioschemas.org metadata by username and project slug",
     *     description="Generates structured scientific metadata compliant with Bioschemas.org standards for a specific project, study, or dataset. Bioschemas extends Schema.org with life sciences and chemistry-specific properties, enabling enhanced discoverability in scientific search engines and knowledge graphs. Returns comprehensive molecular entity representations, experimental methodologies, and research context.",
     *
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="NMRXIV username of the data owner/principal investigator",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[a-zA-Z0-9_-]+$",
     *             example="sarah_johnson_chem"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="Project slug identifier (URL-friendly project name)",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[a-z0-9-]+$",
     *             example="marine-alkaloids-nmr-study-2024"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Bioschemas.org structured metadata retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="@context", type="string", description="JSON-LD context", example="https://schema.org"),
     *             @OA\Property(property="@type", type="string", description="Bioschemas type", example="Study"),
     *             @OA\Property(property="@id", type="string", description="Unique identifier (DOI or URL)", example="https://doi.org/10.1000/nmrxiv.123456"),
     *             @OA\Property(
     *                 property="dct:conformsTo",
     *                 type="array",
     *                 description="Bioschemas profile compliance",
     *
     *                 @OA\Items(type="string"),
     *                 example={"https://bioschemas.org/types/Study/0.3-DRAFT", "https://isa-specs.readthedocs.io/en/latest/isamodel.html#investigation"}
     *             ),
     *
     *             @OA\Property(property="name", type="string", description="Scientific investigation title", example="Comprehensive NMR Analysis of Marine-Derived Alkaloids"),
     *             @OA\Property(property="description", type="string", description="Detailed research description", example="Systematic structural elucidation of bioactive alkaloids isolated from marine sponges using multidimensional NMR spectroscopy"),
     *             @OA\Property(
     *                 property="keywords",
     *                 type="array",
     *                 description="Research keywords and tags",
     *
     *                 @OA\Items(type="string"),
     *                 example={"marine natural products", "alkaloids", "NMR spectroscopy", "structure elucidation", "bioactivity"}
     *             ),
     *
     *             @OA\Property(property="license", type="string", format="uri", description="Data usage license", example="https://creativecommons.org/licenses/by/4.0/"),
     *             @OA\Property(property="url", type="string", format="uri", description="Public access URL", example="https://nmrxiv.org/P123"),
     *             @OA\Property(property="dateCreated", type="string", format="date-time", description="Creation timestamp"),
     *             @OA\Property(property="dateModified", type="string", format="date-time", description="Last modification timestamp"),
     *             @OA\Property(property="datePublished", type="string", format="date-time", description="Publication timestamp"),
     *             @OA\Property(
     *                 property="author",
     *                 type="array",
     *                 description="Research authors and contributors",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="@type", type="string", example="Person"),
     *                     @OA\Property(property="name", type="string", example="Dr. Sarah Johnson"),
     *                     @OA\Property(property="identifier", type="string", description="ORCID ID", example="https://orcid.org/0000-0002-1825-0097"),
     *                     @OA\Property(property="affiliation", type="string", example="Marine Chemistry Institute")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="publisher",
     *                 type="object",
     *                 description="Publishing organization",
     *                 @OA\Property(property="@type", type="string", example="Organization"),
     *                 @OA\Property(property="name", type="string", example="NMRXIV - NMR Data Repository"),
     *                 @OA\Property(property="url", type="string", example="https://nmrxiv.org")
     *             ),
     *             @OA\Property(
     *                 property="studyDomain",
     *                 type="string",
     *                 description="Scientific domain classification",
     *                 example="Chemistry"
     *             ),
     *             @OA\Property(
     *                 property="studySubject",
     *                 type="string",
     *                 description="Research subject matter",
     *                 example="Small molecules"
     *             ),
     *             @OA\Property(
     *                 property="hasPart",
     *                 type="array",
     *                 description="Component studies and datasets",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="@type", type="string", example="Study"),
     *                     @OA\Property(property="name", type="string", example="Compound 1 - Structural Analysis"),
     *                     @OA\Property(property="about", type="object", description="Chemical substance being studied"),
     *                     @OA\Property(
     *                         property="hasPart",
     *                         type="array",
     *                         description="NMR datasets and experiments",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="@type", type="string", example="Dataset"),
     *                             @OA\Property(property="name", type="string", example="1H NMR Spectrum - CDCl3"),
     *                             @OA\Property(
     *                                 property="measurementTechnique",
     *                                 type="object",
     *                                 description="NMR experiment type with CHMO ontology reference",
     *                                 @OA\Property(property="@type", type="string", example="DefinedTerm"),
     *                                 @OA\Property(property="name", type="string", example="1H nuclear magnetic resonance spectroscopy"),
     *                                 @OA\Property(property="termCode", type="string", example="CHMO:0000593"),
     *                                 @OA\Property(property="url", type="string", example="http://purl.obolibrary.org/obo/CHMO_0000593")
     *                             ),
     *                             @OA\Property(
     *                                 property="variableMeasured",
     *                                 type="array",
     *                                 description="Experimental parameters and conditions",
     *
     *                                 @OA\Items(
     *                                     type="object",
     *
     *                                     @OA\Property(property="@type", type="string", example="PropertyValue"),
     *                                     @OA\Property(property="name", type="string", example="NMR solvent"),
     *                                     @OA\Property(property="value", type="string", example="CDCl3"),
     *                                     @OA\Property(property="propertyID", type="string", example="NMR:1000330")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid username or project slug format"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="username", type="array", @OA\Items(type="string"), example={"Username contains invalid characters"}),
     *                 @OA\Property(property="project", type="array", @OA\Items(type="string"), example={"Project slug must be lowercase with hyphens only"})
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Private data or insufficient permissions",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="This project is not publicly available"),
     *             @OA\Property(property="access_info", type="string", example="Contact the project owner for access permissions")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Not found - User or project does not exist",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Project not found for the specified user"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Verify the username and project slug", "Check if the project has been archived", "Use the search endpoint to find similar projects"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Error generating Bioschemas metadata"),
     *             @OA\Property(property="error_code", type="string", example="SCHEMA_GENERATION_ERROR")
     *         )
     *     )
     * )
     *
     * Retrieve Bioschemas.org metadata by username and project slug
     *
     * **Bioschemas.org Integration:**
     * - Extends Schema.org with life sciences properties
     * - Enables enhanced search engine visibility
     * - Supports scientific knowledge graph integration
     * - Facilitates automated data discovery and harvesting
     *
     * **Generated Metadata Includes:**
     * - Study/Investigation metadata with research context
     * - ChemicalSubstance representations of samples
     * - MolecularEntity details with chemical identifiers
     * - Dataset schemas with NMR experimental parameters
     * - Measurement techniques with CHMO ontology mapping
     *
     * @param  string  $username  NMRXIV username
     * @param  string  $projectName  Project slug identifier
     * @return \Illuminate\Http\JsonResponse
     */
    // public function modelSchemaByName(Request $request, $username, $projectName, $studyName = null, $datasetName = null)

    /**
     * @OA\Get(
     *     path="/api/v1/schemas/bioschemas/{id}",
     *     operationId="getBioschemasMetadataById",
     *     tags={"Scientific Metadata Schemas"},
     *     summary="Retrieve Bioschemas.org metadata by NMRXIV identifier",
     *     description="Generates comprehensive Bioschemas.org compliant metadata for a specific scientific data entry identified by its public NMRXIV identifier. Returns structured metadata that enhances discoverability in scientific search engines, enables integration with knowledge graphs, and supports FAIR data principles. Includes detailed molecular representations, experimental methodologies, and semantic annotations using established scientific ontologies.",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NMRXIV public identifier for scientific data entry",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[PSD][0-9]+$",
     *             example="P123"
     *         ),
     *
     *         @OA\Examples(example="project", value="P123", summary="Project identifier"),
     *         @OA\Examples(example="study", value="S456", summary="Study/Sample identifier"),
     *         @OA\Examples(example="dataset", value="D789", summary="Dataset identifier")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Bioschemas.org structured metadata generated successfully",
     *
     *         @OA\JsonContent(
     *             oneOf={
     *
     *                 @OA\Schema(
     *                     description="Project-level Bioschemas Study schema",
     *
     *                     @OA\Property(property="@context", type="string", example="https://schema.org"),
     *                     @OA\Property(property="@type", type="string", example="Study"),
     *                     @OA\Property(property="@id", type="string", example="https://doi.org/10.1000/nmrxiv.123456"),
     *                     @OA\Property(
     *                         property="dct:conformsTo",
     *                         type="array",
     *
     *                         @OA\Items(type="string"),
     *                         example={"https://bioschemas.org/types/Study/0.3-DRAFT", "https://isa-specs.readthedocs.io/en/latest/isamodel.html#investigation"}
     *                     ),
     *
     *                     @OA\Property(property="name", type="string", example="Marine Natural Products Chemical Space Exploration"),
     *                     @OA\Property(property="studyDomain", type="string", example="Chemistry"),
     *                     @OA\Property(property="studySubject", type="string", example="Small molecules"),
     *                     @OA\Property(
     *                         property="hasPart",
     *                         type="array",
     *                         description="Component studies with their datasets",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="@type", type="string", example="Study"),
     *                             @OA\Property(property="name", type="string", example="Alkaloid Compound Library Analysis"),
     *                             @OA\Property(
     *                                 property="about",
     *                                 type="object",
     *                                 description="ChemicalSubstance being studied",
     *                                 @OA\Property(property="@type", type="string", example="ChemicalSubstance"),
     *                                 @OA\Property(property="name", type="string", example="Marine Alkaloid Extract MA-2024-001"),
     *                                 @OA\Property(
     *                                     property="hasBioChemEntityPart",
     *                                     type="array",
     *                                     description="Individual molecular entities",
     *
     *                                     @OA\Items(
     *                                         type="object",
     *
     *                                         @OA\Property(property="@type", type="string", example="MolecularEntity"),
     *                                         @OA\Property(property="@id", type="string", example="BSYNRYMUTXBXSQ-UHFFFAOYSA-N"),
     *                                         @OA\Property(property="name", type="string", example="Aspirin"),
     *                                         @OA\Property(property="inChI", type="string", example="InChI=1S/C9H8O4/c1-6(10)13-8-5-3-2-4-7(8)9(11)12/h2-5H,1H3,(H,11,12)"),
     *                                         @OA\Property(property="inChIKey", type="string", example="BSYNRYMUTXBXSQ-UHFFFAOYSA-N"),
     *                                         @OA\Property(property="molecularFormula", type="string", example="C9H8O4"),
     *                                         @OA\Property(property="molecularWeight", type="number", example=180.157),
     *                                         @OA\Property(property="smiles", type="array", @OA\Items(type="string"), example={"CC(=O)OC1=CC=CC=C1C(=O)O"})
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Schema(
     *                     description="Study-level Bioschemas Study schema",
     *
     *                     @OA\Property(property="@type", type="string", example="Study"),
     *                     @OA\Property(
     *                         property="about",
     *                         type="object",
     *                         description="Chemical substance being studied",
     *                         @OA\Property(property="@type", type="string", example="ChemicalSubstance"),
     *                         @OA\Property(property="name", type="string", example="Compound Library Sample CLS-456")
     *                     ),
     *                     @OA\Property(
     *                         property="hasPart",
     *                         type="array",
     *                         description="NMR datasets and experiments",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="@type", type="string", example="Dataset"),
     *                             @OA\Property(property="name", type="string", example="1H NMR - DMSO-d6 - 600 MHz")
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Schema(
     *                     description="Dataset-level Schema.org Dataset schema",
     *
     *                     @OA\Property(property="@type", type="string", example="Dataset"),
     *                     @OA\Property(
     *                         property="dct:conformsTo",
     *                         type="array",
     *
     *                         @OA\Items(type="string"),
     *                         example={"https://schema.org/Dataset", "https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay"}
     *                     ),
     *
     *                     @OA\Property(
     *                         property="measurementTechnique",
     *                         type="object",
     *                         description="NMR experiment with CHMO ontology mapping",
     *                         @OA\Property(property="@type", type="string", example="DefinedTerm"),
     *                         @OA\Property(property="name", type="string", example="1H nuclear magnetic resonance spectroscopy"),
     *                         @OA\Property(property="alternateName", type="array", @OA\Items(type="string"), example={"1H-NMR", "proton NMR", "1H NMR spectroscopy"}),
     *                         @OA\Property(property="termCode", type="string", example="CHMO:0000593"),
     *                         @OA\Property(property="url", type="string", example="http://purl.obolibrary.org/obo/CHMO_0000593"),
     *                         @OA\Property(
     *                             property="inDefinedTermSet",
     *                             type="object",
     *                             @OA\Property(property="@type", type="string", example="DefinedTermSet"),
     *                             @OA\Property(property="name", type="string", example="Chemical Methods Ontology"),
     *                             @OA\Property(property="url", type="string", example="http://purl.obolibrary.org/obo/chmo.owl")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="variableMeasured",
     *                         type="array",
     *                         description="Experimental parameters and NMR conditions",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="@type", type="string", example="PropertyValue"),
     *                             @OA\Property(property="name", type="string", example="magnetic field strength"),
     *                             @OA\Property(property="value", type="number", example=14.1),
     *                             @OA\Property(property="unitText", type="string", example="Tesla"),
     *                             @OA\Property(property="propertyID", type="string", example="MR:1400253")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="distribution",
     *                         type="object",
     *                         description="Data download information",
     *                         @OA\Property(property="@type", type="string", example="DataDownload"),
     *                         @OA\Property(property="contentUrl", type="string", example="https://nmrxiv.org/api/v1/datasets/D789/download"),
     *                         @OA\Property(property="encodingFormat", type="string", example="application/zip")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid identifier format",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid NMRXIV identifier format"),
     *             @OA\Property(property="expected_format", type="string", example="[P|S|D] followed by numbers (e.g., P123, S456, D789)"),
     *             @OA\Property(property="provided", type="string", example="XYZ123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Data not publicly accessible",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="This data entry is not publicly available"),
     *             @OA\Property(property="identifier", type="string", example="P123"),
     *             @OA\Property(property="contact_info", type="string", example="Contact the data owner for access permissions")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Not found - Data entry does not exist",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No data found for identifier: P999"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Verify the identifier is correct", "Check if the data has been archived", "Use the search endpoint to find similar data"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Schema generation failed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Failed to generate Bioschemas metadata"),
     *             @OA\Property(property="error_code", type="string", example="BIOSCHEMAS_GENERATION_ERROR"),
     *             @OA\Property(property="details", type="string", example="Error processing molecular entity data")
     *         )
     *     )
     * )
     *
     * Retrieve Bioschemas.org metadata by NMRXIV identifier
     *
     * **Bioschemas.org Compliance:**
     * This endpoint generates metadata following Bioschemas.org specifications which extend
     * Schema.org with life sciences and chemistry-specific properties:
     *
     * - **Study Profile**: Research investigations and experimental designs
     * - **ChemicalSubstance Profile**: Sample and specimen representations
     * - **MolecularEntity Profile**: Individual chemical compounds with identifiers
     * - **Dataset Profile**: Experimental data with measurement techniques
     *
     * **Scientific Ontology Integration:**
     * - **CHMO**: Chemical Methods Ontology for NMR techniques
     * - **InChI/InChIKey**: Standard chemical structure identifiers
     * - **ORCID**: Author identification and attribution
     * - **DOI**: Persistent citation and linking
     *
     * **Use Cases:**
     * - Enhanced search engine visibility for scientific data
     * - Knowledge graph integration and semantic linking
     * - Automated metadata harvesting by repositories
     * - FAIR data principles compliance
     * - Scientific workflow interoperability
     *
     * @param  string  $identifier  NMRXIV public identifier (P123, S456, D789)
     * @return \Illuminate\Http\JsonResponse
     */
    public function modelSchemaByID(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];

        if ($model->is_public) {
            if ($namespace == 'Project') {
                $projectSchema = $this->project($model);

                return $projectSchema;
            } elseif ($namespace == 'Study') {
                $studySchema = $this->study($model);

                return $studySchema;

            } elseif ($namespace == 'Dataset') {
                $datasetSchema = $this->dataset($model);

                return $datasetSchema;
            }
        } else {
            throw new AuthorizationException;
        }
    }

    /**
     * Use Bioschemas MolecularEntity type to represent molecules found in a sample.
     *
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Sample  $sample
     * @return array $moleculesSchemas
     */
    public function prepareMoleculesSchemas($sample)
    {
        $moleculesSchemas = [];

        foreach ($sample->molecules as &$molecule) {
            $inchiKey = $molecule->inchi_key;
            $pubchemLink = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/inchikey/'.$inchiKey.'/property/IUPACName/JSON';
            $pubchemDataJSON = json_decode(Http::get($pubchemLink)->body(), true);
            $cid = '';
            $iupacName = '';
            if (array_key_exists('PropertyTable', $pubchemDataJSON)) {
                $cid = $pubchemDataJSON['PropertyTable']['Properties'][0]['CID'];
                $iupacName = $pubchemDataJSON['PropertyTable']['Properties'][0]['IUPACName'];
            }

            $moleculeSchema = Schema::MolecularEntity();
            $moleculeSchema['@id'] = $inchiKey;
            $moleculeSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02']);
            $moleculeSchema['identifier'] = $inchiKey;
            $moleculeSchema->name($molecule->cas);
            $moleculeSchema->url('https://pubchem.ncbi.nlm.nih.gov/compound/'.$cid);
            $moleculeSchema->inChI($molecule->standard_inchi);
            $moleculeSchema->inChIKey($inchiKey);
            $moleculeSchema->iupacName($iupacName);
            $moleculeSchema->molecularFormula($molecule->molecular_formula);
            $moleculeSchema->molecularWeight($molecule->molecular_weight);
            $moleculeSchema->smiles([$molecule->SMILES, $molecule->absolute_smiles, $molecule->canonical_smiles]);
            $moleculeSchema->hasRepresentation($molecule->MOL);
            $moleculeSchema->description('Percentage composition: '.$molecule->pivot->percentage_composition.'%');
            array_push($moleculesSchemas, $moleculeSchema);
        }

        return $moleculesSchemas;
    }

    /**
     * Use Bioschemas ChemicalSubstance type to represent samples found in studies.
     *
     * @link https://bioschemas.org/types/ChemicalSubstance/0.3-RELEASE-2019_09_02
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $sampleSchema
     */
    public function getSample($study)
    {
        $sample = $study->sample;
        $molecules = $this->prepareMoleculesSchemas($sample);

        $sampleSchema = Schema::ChemicalSubstance();
        $sampleSchema['@id'] = $study->doi;
        $sampleSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/ChemicalSubstance/0.3-RELEASE-2019_09_02']);
        $sampleSchema->name($sample->name);
        $sampleSchema->description($sample->description);
        $sampleSchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $sampleSchema->hasBioChemEntityPart($this->prepareMoleculesSchemas($sample));

        return $sampleSchema;
    }

    /**
     * Represent the NMR experiment as a DefinedTerm
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $array
     */
    public function prepareExperiment($dataset)
    {
        $info = BioschemasHelper::getNMRiumInfo($dataset);
        $experimentSchema = null;
        if ($info) {
            if (property_exists($info, 'nucleus') && property_exists($info, 'experiment')) {
                $chmo = BioschemasHelper::prepareDefinedTermSet('Chemical Methods Ontology', 'http://purl.obolibrary.org/obo/chmo.owl');

                $nucleus = $info->nucleus;
                $experiment = $info->experiment;
                $experimentSchema = $experiment;

                if ($experiment == '1d') {
                    if ($nucleus == '1H') {
                        $experiment = 'proton';
                    } elseif ($nucleus == '13C') {
                        $experiment = 'c13';
                    }
                }
                if ($experiment == 'proton') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('1H nuclear magnetic resonance spectroscopy', ['1H-NMR spectrometry', 'proton nuclear magnetic resonance spectroscopy', '1H-NMR spectroscopy', '1H-NMR', '1H NMR', '1H NMR spectroscopy', '1H nuclear magnetic resonance spectrometry', 'proton NMR'], 'CHMO:0000593', 'http://purl.obolibrary.org/obo/CHMO_0000593', $chmo);
                } elseif ($experiment == 'c13') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('13C nuclear magnetic resonance spectroscopy', ['13C-NMR spectrometry', '13C nuclear magnetic resonance spectrometry', '13C-NMR spectroscopy', 'carbon NMR', '13C NMR spectroscopy', '13C NMR', 'C-NMR'], 'CHMO:0000595', 'http://purl.obolibrary.org/obo/CHMO_0000595', $chmo);
                } elseif ($experiment == 'cosy') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('correlation spectroscopy', ['correlation spectrometry', 'correlated spectroscopy', 'correlated spectrometry', 'COSY'], 'CHMO:0000599', 'http://purl.obolibrary.org/obo/CHMO_0000599', $chmo);
                } elseif ($experiment == 'hmbc') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('heteronuclear multiple bond coherence', ['HMBC', 'HMBC NMR'], 'CHMO:0000601', 'http://purl.obolibrary.org/obo/CHMO_0000601', $chmo);
                } elseif ($experiment == 'hmqc') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('heteronuclear multiple quantum coherence', ['HMQC', 'HMQC NMR'], 'CHMO:0000603', 'http://purl.obolibrary.org/obo/CHMO_0000603', $chmo);
                } elseif ($experiment == 'hsqc') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('heteronuclear single quantum coherence', ['HSQC'], 'CHMO:0000604', 'http://purl.obolibrary.org/obo/CHMO_0000604', $chmo);
                } elseif ($experiment == 'tocsy') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('total correlation spectroscopy', ['homonuclear Hartmann-Hahn spectroscopy', 'homonuclear Hartmann Hahn spectroscopy', 'total correlation spectrometry', 'HOHAHA spectroscopy', 'TOCSY', 'total correlated spectroscopy', 'homonuclear Hartmann,Hahn spectroscopy', 'HOHAHA spectrometry'], 'CHMO:0000605', 'http://purl.obolibrary.org/obo/CHMO_0000605', $chmo);
                } elseif ($experiment == 'roesy') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('rotating frame Overhauser effect spectroscopy', ['rotating frame Overhauser enhancement spectroscopy', 'rotating frame Overhauser enhancement spectrometry', 'rOesy', 'cross-relaxation appropriate for minimolecules eμlated by locked spins', 'ROESY', 'CAMELPSIN', 'rotational Overhauser effect spectroscopy', 'rotating frame Overhauser effect spectrometry', 'ROESY NMR'], 'CHMO:0000610', 'http://purl.obolibrary.org/obo/CHMO_0000610', $chmo);
                } elseif ($experiment == 'dept') {
                    $experimentSchema = BioschemasHelper::prepareDefinedTerm('distortionless enhancement with polarization transfer', ['distortionless enhancement with polarisation transfer', 'distortionless enhancement by polarisation transfer', 'distortionless enhancement by polarization transfer', 'DEPT NMR', 'DEPT', 'distortionless enhancement with polarization transfer'], 'CHMO:0000596', 'http://purl.obolibrary.org/obo/CHMO_0000596', $chmo);
                }
            }
        }

        return $experimentSchema;
    }

    /**
     * Represent NMRium info as PropertyValue schemas.
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $array
     */
    public function prepareNMRiumInfo($dataset)
    {
        $info = BioschemasHelper::getNMRiumInfo($dataset);
        if ($info) {
            $solvent = null;
            $nucleus = null;
            $dimension = null;
            $probeName = null;
            $experiment = null;
            $temperature = null;
            $baseFrequency = null;
            $fieldStrength = null;
            $numberOfScans = null;
            $pulseSequence = null;
            $spectralWidth = null;
            $numberOfPoints = null;
            $relaxationTime = null;

            if (property_exists($info, 'solvent')) {
                $solvent = $info->solvent;
            }
            if (property_exists($info, 'nucleus')) {
                $nucleus = $info->nucleus;
            }
            if (is_string($nucleus)) {
                $nucleus = [$nucleus];
            }
            if (property_exists($info, 'dimension')) {
                $dimension = $info->dimension;
            }
            if (property_exists($info, 'probeName')) {
                $probeName = $info->probeName;
            }
            if (property_exists($info, 'experiment')) {
                $experiment = $info->experiment;
            }
            if (property_exists($info, 'temperature')) {
                $temperature = $info->temperature;
            }
            if (property_exists($info, 'baseFrequency')) {
                $baseFrequency = $info->baseFrequency;
            }
            if (property_exists($info, 'fieldStrength')) {
                $fieldStrength = $info->fieldStrength;
            }
            if (property_exists($info, 'numberOfScans')) {
                $numberOfScans = $info->numberOfScans;
            }
            if (property_exists($info, 'pulseSequence')) {
                $pulseSequence = $info->pulseSequence;
            }
            if (property_exists($info, 'spectralWidth')) {
                $spectralWidth = $info->spectralWidth;
            }
            if (property_exists($info, 'numberOfPoints')) {
                $numberOfPoints = $info->numberOfPoints;
            }
            if (property_exists($info, 'relaxationTime')) {
                $relaxationTime = $info->relaxationTime;
            }

            $solventProperty = BioschemasHelper::preparePropertyValue('NMR solvent', 'NMR:1000330', $solvent, null);
            $nucleusProperty = BioschemasHelper::preparePropertyValue('acquisition nucleus', 'NMR:1400083', $nucleus, null);
            $dimensionProperty = BioschemasHelper::preparePropertyValue('NMR spectrum by dimensionality', 'NMR:1000117', $dimension, null);
            $probeNameProperty = BioschemasHelper::preparePropertyValue('NMR probe', 'OBI:0000516', $probeName, null);
            // $experimentProperty = BioschemasHelper::preparePropertyValue('pulsed nuclear magnetic resonance spectroscopy', 'CHMO:0000613', $experiment, null);
            $temperatureProperty = BioschemasHelper::preparePropertyValue('Temperature', 'NCIT:C25206', $temperature, 'http://purl.obolibrary.org/obo/UO_0000012');
            $baseFrequencyProperty = BioschemasHelper::preparePropertyValue('irradiation frequency', 'NMR:1400026', $baseFrequency, 'http://purl.obolibrary.org/obo/UO_0000325');
            $fieldStrengthProperty = BioschemasHelper::preparePropertyValue('magnetic field strength', 'MR:1400253', $fieldStrength, 'http://purl.obolibrary.org/obo/UO_0000228');
            $numberOfScansProperty = BioschemasHelper::preparePropertyValue('number of scans', 'NMR:1400087', $numberOfScans, 'scans');
            $pulseSequenceProperty = BioschemasHelper::preparePropertyValue('nuclear magnetic resonance pulse sequence', 'CHMO:0001841', $pulseSequence, null);
            $spectralWidthProperty = BioschemasHelper::preparePropertyValue('Spectral Width', 'NCIT:C156496', $spectralWidth, 'http://purl.obolibrary.org/obo/UO_0000169');
            $numberOfPointsProperty = BioschemasHelper::preparePropertyValue('number of data points', 'NMR:1000176', $numberOfPoints, 'points');
            $relaxationTimeProperty = BioschemasHelper::preparePropertyValue('relaxation time measurement', 'FIX:0000202', $relaxationTime, 'http://purl.obolibrary.org/obo/UO_0000010');

            $keywords = [$solvent, $dimension.'D'];
            if ($nucleus !== null) {
                foreach ($nucleus as $keyword) {
                    array_push($keywords, $keyword);
                }
            }

            $variables = [$solventProperty, $nucleusProperty,  $dimensionProperty, $probeNameProperty,
                $temperatureProperty, $baseFrequencyProperty, $fieldStrengthProperty, $numberOfScansProperty, $pulseSequenceProperty, $spectralWidthProperty, $numberOfPointsProperty, $relaxationTimeProperty, ];

            $array = [$keywords, $variables, $experiment];

            return $array;
        }
    }

    /**
     *  Use Bioschemas Study type to represent studies found in a project with their datasets.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return array $studiesSchemas
     */
    public function prepareStudies($project)
    {
        $studiesSchemas = [];
        foreach ($project->studies as $study) {
            $studySchema = $this->studyLite($study);
            $studySchema->hasPart($this->prepareDatasets($study));
            array_push($studiesSchemas, $studySchema);
        }

        return $studiesSchemas;
    }

    /**
     * Use Schema.org Dataset type to represent datasets found in a study.
     *
     * @link https://schema.org/Dataset
     *
     * @param  App\Models\Study  $study
     * @return array $datasetsSchemas
     */
    public function prepareDatasets($study)
    {
        $datasetsSchemas = [];
        foreach ($study->datasets as $dataset) {
            $datasetSchema = $this->datasetLite($dataset);
            array_push($datasetsSchemas, $datasetSchema);
        }

        return $datasetsSchemas;
    }

    /**
     * Use Schema.org Dataset type to represent an nmrXiv dataset without its relations.
     *
     * @link https://schema.org/Dataset
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $datasetSchema
     */
    public function datasetLite($dataset)
    {
        $nmriumInfo = $this->prepareNMRiumInfo($dataset);
        if ($nmriumInfo) {
            $study = $dataset->study;
            $prefix = $dataset->study->name;

            $datasetSchema = Schema::Dataset();
            $datasetSchema['@id'] = $dataset->doi;
            $datasetSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://schema.org/Dataset', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#assay']);
            $datasetSchema->name($prefix.'['.$dataset->name.']');
            $datasetSchema->description($dataset->description);
            $datasetSchema->keywords($nmriumInfo[0]);
            $datasetSchema->license($dataset->study->license->url);
            $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
            $datasetSchema->dateCreated($dataset->created_at ? $dataset->created_at->toISOString() : null);
            $datasetSchema->dateModified($dataset->updated_at ? $dataset->updated_at->toISOString() : null);
            $datasetSchema->datePublished($dataset->release_date ? Carbon::parse($dataset->release_date)->toISOString() : null);
            $datasetSchema->distribution(BioschemasHelper::prepareDataDownload($dataset));
            $datasetSchema->includedInDataCatalog(BioschemasHelper::prepareDataCatalogLite());
            $datasetSchema->measurementTechnique($this->prepareExperiment($dataset));
            $datasetSchema->variableMeasured($nmriumInfo[1]);
            $datasetSchema->isAccessibleForFree(true);

            return $datasetSchema;
        }
    }

    /**
     * Use Schema.org Dataset type to represent an nmrXiv dataset with its relations.
     *
     * @link https://schema.org/Dataset
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $datasetSchema
     */
    public function dataset($dataset)
    {
        $datasetSchema = $this->datasetLite($dataset);
        $studySchema = $this->studyLite($dataset->study);

        if ($dataset->project) {
            $projectSchema = $this->projectLite($dataset->project);
            $studySchema->isPartOf($projectSchema);
        }
        $datasetSchema->isPartOf($studySchema);

        return $datasetSchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv study without its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function studyLite($study)
    {

        $studySchema = Bioschemas::Study();
        $studySchema['@id'] = $study->doi;
        $studySchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#study']);
        $studySchema->name($study->name);
        $studySchema->description($study->description);
        $studySchema->keywords(BioschemasHelper::getTags($study));
        $studySchema->license($study->license->url);
        $studySchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $studySchema->dateCreated($study->created_at ? $study->created_at->toISOString() : null);
        $studySchema->dateModified($study->updated_at ? $study->updated_at->toISOString() : null);
        $studySchema->datePublished($study->release_date ? Carbon::parse($study->release_date)->toISOString() : null);
        $studySchema->about($this->getSample($study));
        $studySchema->studyDomain('Chemistry');
        $studySchema->studySubject('Small molecules');

        return $studySchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv study with its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function study($study)
    {
        $studySchema = $this->studyLite($study);
        if (property_exists($study, 'project')) {
            $studySchema->isPartOf($this->projectLite($study->project));
        }
        $studySchema->hasPart($this->prepareDatasets($study));

        return $studySchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv project without its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function projectLite($project)
    {
        $projectSchema = Bioschemas::Study();
        $projectSchema['@id'] = $project->doi;
        $projectSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://bioschemas.org/types/Study/0.3-DRAFT', 'https://isa-specs.readthedocs.io/en/latest/isamodel.html#investigation']);
        $projectSchema->name($project->name);
        $projectSchema->description($project->description);
        $projectSchema->keywords(BioschemasHelper::getTags($project));
        $projectSchema->license($project->license->url);
        $projectSchema->publisher(BioschemasHelper::preparePublisher());
        $projectSchema->url(env('APP_URL').'/'.explode(':', $project->identifier ? $project->identifier : ':')[1]);
        $projectSchema->dateCreated($project->created_at ? $project->created_at->toISOString() : null);
        $projectSchema->dateModified($project->updated_at ? $project->updated_at->toISOString() : null);
        $projectSchema->datePublished($project->release_date ? Carbon::parse($project->release_date)->toISOString() : null);
        $projectSchema->author(BioschemasHelper::prepareAuthors($project));
        $projectSchema->citation(BioschemasHelper::prepareCitations($project));

        return $projectSchema;
    }

    /**
     * Use Bioschemas Study type to represent an nmrXiv project with its relations.
     *
     * @link https://bioschemas.org/types/Study/0.3-DRAFT
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function project($project)
    {
        $projectSchema = $this->projectLite($project);
        $projectSchema->hasPart($this->prepareStudies($project));

        return $projectSchema;
    }
}
