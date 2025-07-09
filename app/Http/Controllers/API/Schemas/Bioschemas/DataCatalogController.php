<?php

namespace App\Http\Controllers\API\Schemas\Bioschemas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\SchemaOrg\Schema;

/**
 * Use Schema.org DataCatalog type to represent nmrXiv as a repository.
 */
class DataCatalogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/schemas/bioschemas/",
     *     operationId="getDataCatalogSchema",
     *     tags={"Scientific Metadata Schemas"},
     *     summary="Retrieve Schema.org DataCatalog metadata for NMRXIV repository",
     *     description="Generates comprehensive Schema.org DataCatalog metadata representing NMRXIV as a scientific data repository. This endpoint provides structured metadata that enhances repository discoverability in search engines, enables integration with scientific data aggregators, and supports FAIR data principles. The DataCatalog schema includes detailed information about the repository's scope, contributors, measurement techniques, and scientific ontology mappings for NMR spectroscopy data.",
     *
     *     @OA\Response(
     *         response=200,
     *         description="DataCatalog schema generated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="@context", type="string", description="JSON-LD context", example="https://schema.org"),
     *             @OA\Property(property="@type", type="string", description="Schema.org type", example="DataCatalog"),
     *             @OA\Property(property="@id", type="string", description="Repository identifier", example="https://nmrxiv.org"),
     *             @OA\Property(
     *                 property="dct:conformsTo",
     *                 type="array",
     *                 description="Schema compliance references",
     *
     *                 @OA\Items(type="string"),
     *                 example={"https://schema.org/DataCatalog"}
     *             ),
     *
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Repository name",
     *                 example="NMRXIV - NMR Data Repository"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Comprehensive repository description",
     *                 example="NMRXIV is an open-access preprint repository for sharing and discovering nuclear magnetic resonance (NMR) spectroscopy data. The platform enables researchers to upload, validate, and share NMR datasets with comprehensive metadata, supporting reproducible research and data reuse in chemistry, biochemistry, and related fields."
     *             ),
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 format="uri",
     *                 description="Repository homepage URL",
     *                 example="https://nmrxiv.org"
     *             ),
     *             @OA\Property(
     *                 property="identifier",
     *                 type="string",
     *                 description="Persistent repository identifier",
     *                 example="https://nmrxiv.org"
     *             ),
     *             @OA\Property(
     *                 property="license",
     *                 type="string",
     *                 format="uri",
     *                 description="Repository software license",
     *                 example="https://mit-license.org/"
     *             ),
     *             @OA\Property(
     *                 property="isAccessibleForFree",
     *                 type="boolean",
     *                 description="Free access availability",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="provider",
     *                 type="object",
     *                 description="Repository provider organization",
     *                 @OA\Property(property="@type", type="string", example="Organization"),
     *                 @OA\Property(property="name", type="string", example="Friedrich Schiller University Jena"),
     *                 @OA\Property(property="url", type="string", example="https://cheminf.uni-jena.de")
     *             ),
     *             @OA\Property(
     *                 property="keywords",
     *                 type="array",
     *                 description="Scientific domain keywords with ontology mappings",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="@type", type="string", example="DefinedTerm"),
     *                     @OA\Property(property="name", type="string", example="nuclear magnetic resonance spectroscopy"),
     *                     @OA\Property(
     *                         property="alternateName",
     *                         type="array",
     *
     *                         @OA\Items(type="string"),
     *                         example={"NMR", "NMR spectroscopy", "nuclear magnetic resonance (NMR) spectroscopy"}
     *                     ),
     *
     *                     @OA\Property(property="termCode", type="string", example="CHMO:0000591"),
     *                     @OA\Property(property="url", type="string", example="http://purl.obolibrary.org/obo/CHMO_0000591"),
     *                     @OA\Property(
     *                         property="inDefinedTermSet",
     *                         type="object",
     *                         @OA\Property(property="@type", type="string", example="DefinedTermSet"),
     *                         @OA\Property(property="name", type="string", example="Chemical Methods Ontology"),
     *                         @OA\Property(property="url", type="string", example="http://purl.obolibrary.org/obo/chmo.owl")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="measurementTechnique",
     *                 type="array",
     *                 description="Supported analytical measurement techniques",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="@type", type="string", example="DefinedTerm"),
     *                     @OA\Property(property="name", type="string", example="pulsed nuclear magnetic resonance spectroscopy"),
     *                     @OA\Property(property="termCode", type="string", example="CHMO:0000613"),
     *                     @OA\Property(property="url", type="string", example="https://ontobee.org/ontology/CHMO?iri=http://purl.obolibrary.org/obo/CHMO_0000613")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="contributor",
     *                 type="array",
     *                 description="Repository contributors and development team",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="@type", type="string", example="Person"),
     *                     @OA\Property(property="givenName", type="string", example="Christoph"),
     *                     @OA\Property(property="familyName", type="string", example="Steinbeck"),
     *                     @OA\Property(property="email", type="string", example="christoph.steinbeck@uni-jena.de"),
     *                     @OA\Property(
     *                         property="identifier",
     *                         type="object",
     *                         @OA\Property(property="@type", type="string", example="PropertyValue"),
     *                         @OA\Property(property="name", type="string", example="orcid"),
     *                         @OA\Property(property="value", type="string", example="https://orcid.org/0000-0001-6966-0814")
     *                     ),
     *                     @OA\Property(
     *                         property="affiliation",
     *                         type="object",
     *                         @OA\Property(property="@type", type="string", example="Organization"),
     *                         @OA\Property(property="name", type="string", example="Friedrich-Schiller-Universität Jena")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="includedInDataCatalog",
     *                 type="array",
     *                 description="Parent data catalogs and aggregators",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="@type", type="string", example="DataCatalog"),
     *                     @OA\Property(property="name", type="string", example="re3data - Registry of Research Data Repositories"),
     *                     @OA\Property(property="url", type="string", example="https://www.re3data.org")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="dataset",
     *                 type="object",
     *                 description="Sample dataset entry reference",
     *                 @OA\Property(property="@type", type="string", example="Dataset"),
     *                 @OA\Property(property="name", type="string", example="Representative NMR datasets available in the repository"),
     *                 @OA\Property(property="description", type="string", example="This repository contains thousands of NMR datasets including 1D and 2D experiments")
     *             ),
     *             @OA\Property(
     *                 property="spatial",
     *                 type="object",
     *                 description="Geographic coverage",
     *                 @OA\Property(property="@type", type="string", example="Place"),
     *                 @OA\Property(property="name", type="string", example="Global coverage - datasets from research institutions worldwide")
     *             ),
     *             @OA\Property(
     *                 property="temporal",
     *                 type="string",
     *                 description="Temporal coverage",
     *                 example="2020/2024"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - DataCatalog schema generation failed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Failed to generate DataCatalog schema"),
     *             @OA\Property(property="error_code", type="string", example="DATACATALOG_GENERATION_ERROR"),
     *             @OA\Property(property="details", type="string", example="Error accessing repository configuration or contributor data")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Repository temporarily offline",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Repository metadata service temporarily unavailable"),
     *             @OA\Property(property="retry_after", type="integer", example=300, description="Seconds to wait before retrying"),
     *             @OA\Property(property="status_page", type="string", example="https://status.nmrxiv.org")
     *         )
     *     )
     * )
     *
     * Retrieve Schema.org DataCatalog metadata for NMRXIV repository
     *
     * **Schema.org DataCatalog Benefits:**
     * The DataCatalog schema provides standardized metadata that enables:
     *
     * - **Enhanced Repository Discovery**: Improves visibility in search engines and scientific indexing services
     * - **FAIR Data Compliance**: Supports Findable, Accessible, Interoperable, and Reusable data principles
     * - **Metadata Harvesting**: Enables automatic discovery by data aggregators and portal services
     * - **Scientific Integration**: Facilitates integration with research data ecosystems and workflows
     *
     * **Repository Features Documented:**
     * - **Scientific Scope**: NMR spectroscopy and related analytical chemistry methods
     * - **Ontology Integration**: CHMO (Chemical Methods Ontology) and NMR CV mappings
     * - **Open Access**: Free access to all repository content and metadata
     * - **International Collaboration**: Contributors from multiple research institutions globally
     * - **Standardized Formats**: Support for standard NMR data formats and metadata schemas
     *
     * **Technical Specifications:**
     * - **Measurement Techniques**: Comprehensive coverage of 1D and 2D NMR experiments
     * - **Data Formats**: Support for vendor-neutral and proprietary NMR data formats
     * - **Metadata Standards**: Integration with Bioschemas.org and DataCite schemas
     * - **API Access**: RESTful API for programmatic access to repository content
     *
     * **Use Cases:**
     * - Repository registration in scientific data catalogs
     * - Search engine optimization for scientific content discovery
     * - Integration with institutional research data management systems
     * - Compliance with funding agency data sharing requirements
     * - Support for systematic reviews and meta-analyses in chemistry
     *
     * @return \Illuminate\Http\JsonResponse DataCatalog schema representing NMRXIV repository
     */
    public function dataCatalogSchema(Request $request)
    {
        $keywords = $this->prepareKeywords();
        $contributors = $this->prepareContributors();

        $nmrXivProvider = Schema::Organization();
        $nmrXivProvider->name(Config::get('schemas.bioschema.provider'));
        $nmrXivProvider->url(Config::get('schemas.bioschema.provider_url'));

        $dataCatalogSchema = Schema::DataCatalog();
        $dataCatalogSchema['@id'] = url(Config::get('app.url'));
        $dataCatalogSchema['dct:conformsTo'] = BioschemasHelper::conformsTo(['https://schema.org/DataCatalog']);
        $dataCatalogSchema->description(env('APP_DESCRIPTION'));
        $dataCatalogSchema->keywords($keywords);
        $dataCatalogSchema->name(Config::get('app.name'));
        $dataCatalogSchema->provider($nmrXivProvider);
        $dataCatalogSchema->url(Config::get('app.url'));
        $dataCatalogSchema->identifier(Config::get('app.url'));
        $dataCatalogSchema->license('https://mit-license.org/');
        $dataCatalogSchema->contributor($contributors);
        $dataCatalogSchema->isAccessibleForFree(true);

        $dataCatalogSchema->measurementTechnique(Config::get('schemas.ontologies.measurement_technique'));

        return $dataCatalogSchema;
    }

    /**
     * Prepare keywords associated with nmrXiv.
     *
     * @return array $keywords
     */
    public function prepareKeywords()
    {
        // Prepare Defined Term Sets
        $chmo = BioschemasHelper::prepareDefinedTermSet('Chemical Methods Ontology', 'http://purl.obolibrary.org/obo/chmo.owl');
        $nmrcv = BioschemasHelper::prepareDefinedTermSet('nuclear magnetic resonance CV', 'http://nmrml.org/cv/');

        // Prepare Defined Terms
        $nmr = BioschemasHelper::prepareDefinedTerm('nuclear magnetic resonance spectroscopy', ['NMR', 'NMR spectroscopy', 'nuclear magnetic resonance (NMR) spectroscopy'], 'CHMO:0000591', 'http://purl.obolibrary.org/obo/CHMO_0000591', $chmo);
        $pulsedNMR = BioschemasHelper::prepareDefinedTerm('pulsed nuclear magnetic resonance spectroscopy', ['NMR', 'nuclear magnetic resonance spectroscopy', 'NMR spectroscopy'], 'CHMO:0000613', 'https://ontobee.org/ontology/CHMO?iri=http://purl.obolibrary.org/obo/CHMO_0000613', $chmo);
        $oneDNMR = BioschemasHelper::prepareDefinedTerm('one-dimensional nuclear magnetic resonance spectroscopy', ['1D NMR spectroscopy', '1-D NMR', 'one-dimensional nuclear magnetic resonance spectroscopy', '1D NMR', '1D nuclear magnetic resonance spectroscopy'], 'CHMO:0000592', 'http://purl.obolibrary.org/obo/CHMO_0000592', $chmo);
        $twoDNMR = BioschemasHelper::prepareDefinedTerm('two-dimensional nuclear magnetic resonance spectroscopy', ['2-D NMR', '2D NMR', 'two-dimensional nuclear magnetic resonance spectroscopy', '2D NMR spectroscopy', 'two-dimensional NMR', '2D nuclear magnetic resonance'], 'CHMO:0000598', 'http://purl.obolibrary.org/obo/CHMO_0000598', $chmo);
        $cosy = BioschemasHelper::prepareDefinedTerm('correlation spectroscopy spectrum', ['COSY spectra', 'COSY spectrum', 'COSY NMR spectrum', 'COSY NMR spectra'], 'CHMO:0002450', 'http://purl.obolibrary.org/obo/CHMO_0002450', $chmo);
        $hsqc = BioschemasHelper::prepareDefinedTerm('heteronuclear single quantum coherence', ['HSQC'], 'CHMO:0000604', 'http://purl.obolibrary.org/obo/CHMO_0000604', $chmo);
        $hmbc = BioschemasHelper::prepareDefinedTerm('heteronuclear multiple bond coherence', ['HMBC NMR', 'HMBC'], 'CHMO:0000601', 'http://purl.obolibrary.org/obo/CHMO_0000601', $chmo);
        $noesy = BioschemasHelper::prepareDefinedTerm('two-dimensional nuclear Overhauser enhancement spectrum', ['2D NOESY-NMR spectrum', '2D NOESY-NMR spectra', '2D NOESY spectra', '2D NOESY spectrum'], 'CHMO:0001171', 'http://purl.obolibrary.org/obo/CHMO_0001171', $chmo);
        $brukerNMR = BioschemasHelper::prepareDefinedTerm('Bruker', [], 'NMR:1400256', 'http://nmrML.org/nmrCV#NMR:1400256', $nmrcv);
        $joel = BioschemasHelper::prepareDefinedTerm('JEOL', [], 'NMR:1400258', 'http://nmrML.org/nmrCV#NMR:1400258', $nmrcv);
        $nmreData = BioschemasHelper::prepareDefinedTerm('NMReDATA', [], 'format:3906', 'http://edamontology.org/format_3906', $nmrcv);

        $keywords = [$nmr, $pulsedNMR, $oneDNMR, $twoDNMR, $cosy, $hsqc, $hmbc, $noesy, $brukerNMR, $joel, $nmreData];

        return $keywords;
    }

    /**
     * Prepare contributors to nmrXiv.
     *
     * @return array $contributors
     */
    public function prepareContributors()
    {

        $Annett = BioschemasHelper::preparePerson('0000-0002-2542-0867', 'Annett', 'Schröter', 'annett.schroeter@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Christian = BioschemasHelper::preparePerson(null, 'Christian', 'Popp', null, null);
        $Christoph = BioschemasHelper::preparePerson('0000-0001-6966-0814', 'Christoph', 'Steinbeck', 'christoph.steinbeck@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Darina = BioschemasHelper::preparePerson(null, 'Darina', 'Storozhuk', 'darina.storozhuk@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $David = BioschemasHelper::preparePerson('0000-0001-7499-1693', 'David', 'Rauh', null, null);
        $Guido = BioschemasHelper::preparePerson('0000-0003-1022-4326', 'Guido', 'Pauli', null, 'University of Illinois');
        $Hamed = BioschemasHelper::preparePerson(null, 'Hamed', 'Musallam', 'hamed.musallam@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Johannes = BioschemasHelper::preparePerson('0000-0003-2060-842X', 'Johannes', 'Liermann', 'liermann@uni-mainz.de', 'Johannes Gutenberg-Universität Mainz');
        $Julien = BioschemasHelper::preparePerson('0000-0002-3416-2572', 'Julien', 'Wist', null, 'Murdoch University: Murdoch, WA, AU');
        $Kohulan = BioschemasHelper::preparePerson('0000-0003-1066-7792', 'Kohulan', 'Rajan', 'kohulan.rajan@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Luc = BioschemasHelper::preparePerson('0000-0002-4943-2643', 'Luc', 'Patiny', null, 'École Polytechnique Fédérale de Lausanne: Lausanne, VD, CH');
        $Markus = BioschemasHelper::preparePerson(null, 'Markus', 'Lange', null, null);
        $Nazar = BioschemasHelper::preparePerson('0000-0002-5870-8496', 'Nazar', 'Stefaniuk', null, null);
        $Nils = BioschemasHelper::preparePerson('0000-0002-0990-9582', 'Nils', 'Schlörer', null, 'Friedrich-Schiller-Universität Jena');
        $Nisha = BioschemasHelper::preparePerson('0009-0006-4755-1039', 'Nisha', 'Sharma', 'nisha.sharma@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Noura = BioschemasHelper::preparePerson('0009-0001-5998-5030', 'Noura', 'Rayya', 'noura.rayya@uni-jena.de', 'Friedrich-Schiller-Universität Jena');
        $Pascal = BioschemasHelper::preparePerson(null, 'Pascal', 'Scherreiks', null, null);
        $Stefan = BioschemasHelper::preparePerson('0000-0002-5990-4157', 'Stefan', 'Kuhn', null, 'University of Tartu, Tartu');
        $Steffen = BioschemasHelper::preparePerson('0000-0002-7899-7192', 'Steffen', 'Neumann', null, 'Leibniz-Institut für Pflanzenbiochemie, Halle');
        $Tillmann = BioschemasHelper::preparePerson('0000-0003-4480-8661', 'Tillmann', 'Fischer', null, 'Leibniz-Institut für Pflanzenbiochemie, Halle');
        $Venkata = BioschemasHelper::preparePerson('0000-0002-2564-3243', 'Venkata Chandrasekhar', 'Nainala', 'chandu.nainala@uni-jena.de', 'Friedrich-Schiller-Universität Jena');

        $contributors = [$Annett, $Christian, $Christoph, $Darina, $Guido, $Hamed, $Johannes, $Julien, $Kohulan, $Luc, $Markus, $Nazar, $Nils, $Nisha, $Noura, $Pascal, $Stefan, $Steffen, $Tillmann, $Venkata];

        return $contributors;
    }
}
