<?php

namespace App\Http\Controllers\API\Schemas\DataCite;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;

class DataCiteController extends Controller
{
    /**
     * Implement DataCite metadata schema on nmrXiv project, study, and dataset to enable exporting
     * their metadata with a json endpoint, including the samples and molecules.
     */

    /**
     * @OA\Get(
     *     path="/api/v1/schemas/datacite/{username}/{project}",
     *     operationId="getDataCiteMetadataByName",
     *     tags={"Scientific Metadata Schemas"},
     *     summary="Retrieve DataCite metadata schema by username and project slug",
     *     description="Generates DataCite Metadata Schema compliant metadata for scientific data citation and DOI registration. DataCite is the leading global registry for research data DOIs, enabling persistent citation of scientific datasets. This endpoint produces metadata conforming to DataCite Metadata Schema 4.4, supporting automated DOI minting, academic citation workflows, and research data discoverability in scholarly databases.",
     *
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         required=true,
     *         description="NMRXIV username of the principal investigator or data owner",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[a-zA-Z0-9_-]+$",
     *             example="prof_chemistry_2024"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="URL-friendly project slug identifier for the research investigation",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[a-z0-9-]+$",
     *             example="comprehensive-metabolomics-study-2024"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="DataCite metadata schema generated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="schemaVersion",
     *                 type="string",
     *                 description="DataCite Metadata Schema version",
     *                 example="http://datacite.org/schema/kernel-4.4"
     *             ),
     *             @OA\Property(
     *                 property="identifier",
     *                 type="object",
     *                 description="Primary identifier (DOI) for the dataset",
     *                 @OA\Property(property="identifier", type="string", example="10.1000/nmrxiv.123456"),
     *                 @OA\Property(property="identifierType", type="string", example="DOI")
     *             ),
     *             @OA\Property(
     *                 property="creators",
     *                 type="array",
     *                 description="Authors and creators of the research data",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="creatorName", type="string", example="Johnson, Sarah M."),
     *                     @OA\Property(property="givenName", type="string", example="Sarah M."),
     *                     @OA\Property(property="familyName", type="string", example="Johnson"),
     *                     @OA\Property(
     *                         property="nameIdentifiers",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="nameIdentifier", type="string", example="0000-0002-1825-0097"),
     *                             @OA\Property(property="nameIdentifierScheme", type="string", example="ORCID"),
     *                             @OA\Property(property="schemeURI", type="string", example="https://orcid.org")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="affiliations",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="affiliation", type="string", example="Department of Chemistry, University of Excellence"),
     *                             @OA\Property(property="affiliationIdentifier", type="string", example="https://ror.org/012345678"),
     *                             @OA\Property(property="affiliationIdentifierScheme", type="string", example="ROR")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="titles",
     *                 type="array",
     *                 description="Dataset titles in multiple languages",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="title", type="string", example="Comprehensive NMR-Based Metabolomic Analysis of Marine Natural Products"),
     *                     @OA\Property(property="titleType", type="string", example="Main Title"),
     *                     @OA\Property(property="lang", type="string", example="en")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="publisher",
     *                 type="string",
     *                 description="Data repository name",
     *                 example="NMRXIV - NMR Data Repository"
     *             ),
     *             @OA\Property(
     *                 property="publicationYear",
     *                 type="integer",
     *                 description="Year of data publication",
     *                 example=2024
     *             ),
     *             @OA\Property(
     *                 property="resourceType",
     *                 type="object",
     *                 description="Type of research data resource",
     *                 @OA\Property(property="resourceType", type="string", example="Dataset/NMR Spectroscopy Data"),
     *                 @OA\Property(property="resourceTypeGeneral", type="string", example="Dataset")
     *             ),
     *             @OA\Property(
     *                 property="subjects",
     *                 type="array",
     *                 description="Research subject classifications and keywords",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="subject", type="string", example="Metabolomics"),
     *                     @OA\Property(property="subjectScheme", type="string", example="Fields of Science and Technology (FOS)"),
     *                     @OA\Property(property="schemeURI", type="string", example="http://www.oecd.org/science/inno/38235147.pdf"),
     *                     @OA\Property(property="valueURI", type="string", example="http://www.oecd.org/science/inno/38235147.pdf#1.4"),
     *                     @OA\Property(property="classificationCode", type="string", example="1.4")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="contributors",
     *                 type="array",
     *                 description="Research contributors and collaborators",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="contributorName", type="string", example="Research Data Support Team"),
     *                     @OA\Property(property="contributorType", type="string", example="DataCurator"),
     *                     @OA\Property(property="nameIdentifiers", type="array", @OA\Items(type="object"))
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="dates",
     *                 type="array",
     *                 description="Important dates in the data lifecycle",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="date", type="string", format="date", example="2024-01-15"),
     *                     @OA\Property(property="dateType", type="string", example="Created"),
     *                     @OA\Property(property="dateInformation", type="string", example="Data collection start date")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="language",
     *                 type="string",
     *                 description="Primary language of the dataset documentation",
     *                 example="en"
     *             ),
     *             @OA\Property(
     *                 property="alternateIdentifiers",
     *                 type="array",
     *                 description="Alternative identifiers for the dataset",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="alternateIdentifier", type="string", example="P123"),
     *                     @OA\Property(property="alternateIdentifierType", type="string", example="NMRXIV ID")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="relatedIdentifiers",
     *                 type="array",
     *                 description="Related publications and datasets",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="relatedIdentifier", type="string", example="10.1021/acs.jnatprod.2024.00123"),
     *                     @OA\Property(property="relatedIdentifierType", type="string", example="DOI"),
     *                     @OA\Property(property="relationType", type="string", example="IsReferencedBy"),
     *                     @OA\Property(property="resourceTypeGeneral", type="string", example="Text")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="sizes",
     *                 type="array",
     *                 description="Dataset size information",
     *
     *                 @OA\Items(type="string"),
     *                 example={"1.2 GB", "450 NMR spectra", "25 chemical compounds"}
     *             ),
     *
     *             @OA\Property(
     *                 property="formats",
     *                 type="array",
     *                 description="Data file formats included",
     *
     *                 @OA\Items(type="string"),
     *                 example={"application/zip", "chemical/x-nmr-fid", "chemical/x-nmr-jcamp", "application/json"}
     *             ),
     *
     *             @OA\Property(
     *                 property="rightsList",
     *                 type="array",
     *                 description="Usage rights and licensing information",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="rights", type="string", example="Creative Commons Attribution 4.0 International"),
     *                     @OA\Property(property="rightsURI", type="string", example="https://creativecommons.org/licenses/by/4.0/"),
     *                     @OA\Property(property="rightsIdentifier", type="string", example="CC-BY-4.0"),
     *                     @OA\Property(property="rightsIdentifierScheme", type="string", example="SPDX")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="descriptions",
     *                 type="array",
     *                 description="Detailed dataset descriptions",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="description", type="string", example="This dataset contains comprehensive NMR spectroscopic data for 25 marine-derived natural products..."),
     *                     @OA\Property(property="descriptionType", type="string", example="Abstract"),
     *                     @OA\Property(property="lang", type="string", example="en")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="geoLocations",
     *                 type="array",
     *                 description="Geographic locations relevant to data collection",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="geoLocationPlace", type="string", example="Great Barrier Reef, Australia"),
     *                     @OA\Property(
     *                         property="geoLocationPoint",
     *                         type="object",
     *                         @OA\Property(property="pointLatitude", type="number", example=-16.2839),
     *                         @OA\Property(property="pointLongitude", type="number", example=145.7781)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="fundingReferences",
     *                 type="array",
     *                 description="Research funding information",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="funderName", type="string", example="National Science Foundation"),
     *                     @OA\Property(property="funderIdentifier", type="string", example="https://doi.org/10.13039/100000001"),
     *                     @OA\Property(property="funderIdentifierType", type="string", example="Crossref Funder ID"),
     *                     @OA\Property(property="awardNumber", type="string", example="CHE-2024-123456"),
     *                     @OA\Property(property="awardTitle", type="string", example="Advancing NMR-Based Natural Product Discovery")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid username or project parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid username or project slug format"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="username", type="array", @OA\Items(type="string"), example={"Username contains invalid characters"}),
     *                 @OA\Property(property="project", type="array", @OA\Items(type="string"), example={"Project slug must be lowercase alphanumeric with hyphens"})
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions or private data",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="This project is not publicly available for DataCite metadata export"),
     *             @OA\Property(property="access_requirements", type="string", example="Contact the principal investigator for data access permissions")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Not found - User or project does not exist",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Project not found for the specified username"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Verify username and project slug are correct", "Check if project has been archived or deleted", "Use the search API to find similar projects"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - DataCite schema generation failed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Failed to generate DataCite metadata schema"),
     *             @OA\Property(property="error_code", type="string", example="DATACITE_GENERATION_ERROR"),
     *             @OA\Property(property="support_contact", type="string", example="Contact technical support for assistance")
     *         )
     *     )
     * )
     *
     * Retrieve DataCite metadata schema by username and project slug
     *
     * **DataCite Metadata Schema Compliance:**
     * This endpoint generates metadata compliant with DataCite Metadata Schema 4.4,
     * the international standard for research data citation and DOI registration.
     *
     * **Key Features:**
     * - **DOI-ready metadata**: Structured for automatic DOI minting
     * - **Academic citation support**: Enables proper research data citation
     * - **Funding transparency**: Links datasets to research grants and sponsors
     * - **Geographic context**: Supports location-based data discovery
     * - **Multilingual support**: Titles and descriptions in multiple languages
     * - **Relation mapping**: Connects datasets to publications and other resources
     *
     * **Integration Benefits:**
     * - Automatic DOI registration workflows
     * - Enhanced discoverability in DataCite Commons
     * - Integration with institutional repositories
     * - Support for research impact metrics
     * - Compliance with funding agency requirements
     *
     * @param  string  $username  NMRXIV username
     * @param  string  $projectName  Project slug identifier
     * @return \Illuminate\Http\JsonResponse
     */
    // public function modelSchemaByName(Request $request, $username, $projectName, $studyName = null, $datasetName = null)

    /**
     * @OA\Get(
     *     path="/api/v1/schemas/datacite/{id}",
     *     operationId="getDataCiteMetadataById",
     *     tags={"Scientific Metadata Schemas"},
     *     summary="Retrieve DataCite metadata schema by NMRXIV identifier",
     *     description="Generates DataCite Metadata Schema 4.4 compliant metadata for a specific scientific data entry using its public NMRXIV identifier. This metadata enables DOI registration, academic citation, and integration with scholarly databases. The schema includes comprehensive research context, authorship details, funding information, and technical specifications required for proper research data citation and discovery in academic environments.",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NMRXIV public identifier for the scientific data entry requiring DataCite metadata",
     *
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[PSD][0-9]+$",
     *             example="P1234"
     *         ),
     *
     *         @OA\Examples(example="project", value="P456", summary="Project-level DataCite metadata"),
     *         @OA\Examples(example="study", value="S789", summary="Study/Sample-level DataCite metadata"),
     *         @OA\Examples(example="dataset", value="D012", summary="Dataset-level DataCite metadata")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="DataCite metadata schema generated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="schemaVersion",
     *                 type="string",
     *                 description="DataCite Metadata Schema version compliance",
     *                 example="http://datacite.org/schema/kernel-4.4"
     *             ),
     *             @OA\Property(
     *                 property="identifier",
     *                 type="object",
     *                 description="Primary dataset identifier (DOI)",
     *                 @OA\Property(property="identifier", type="string", example="10.1000/nmrxiv.456789"),
     *                 @OA\Property(property="identifierType", type="string", example="DOI")
     *             ),
     *             @OA\Property(
     *                 property="creators",
     *                 type="array",
     *                 description="Principal investigators and data creators",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="creatorName", type="string", example="Rodriguez, Maria Elena"),
     *                     @OA\Property(property="givenName", type="string", example="Maria Elena"),
     *                     @OA\Property(property="familyName", type="string", example="Rodriguez"),
     *                     @OA\Property(
     *                         property="nameIdentifiers",
     *                         type="array",
     *                         description="Creator identification schemes",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="nameIdentifier", type="string", example="0000-0003-1234-5678"),
     *                             @OA\Property(property="nameIdentifierScheme", type="string", example="ORCID"),
     *                             @OA\Property(property="schemeURI", type="string", example="https://orcid.org")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="affiliations",
     *                         type="array",
     *                         description="Institutional affiliations",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="affiliation", type="string", example="Institute of Marine Chemistry, Barcelona"),
     *                             @OA\Property(property="affiliationIdentifier", type="string", example="https://ror.org/987654321"),
     *                             @OA\Property(property="affiliationIdentifierScheme", type="string", example="ROR")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="titles",
     *                 type="array",
     *                 description="Dataset titles with language specifications",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="title", type="string", example="High-Resolution NMR Characterization of Mediterranean Seaweed Metabolites"),
     *                     @OA\Property(property="titleType", type="string", example="Main Title"),
     *                     @OA\Property(property="lang", type="string", example="en")
     *                 )
     *             ),
     *             @OA\Property(property="publisher", type="string", example="NMRXIV - NMR Data Repository"),
     *             @OA\Property(property="publicationYear", type="integer", example=2024),
     *             @OA\Property(
     *                 property="resourceType",
     *                 type="object",
     *                 description="Scientific data resource classification",
     *                 @OA\Property(property="resourceType", type="string", example="NMR Spectroscopy Dataset"),
     *                 @OA\Property(property="resourceTypeGeneral", type="string", example="Dataset")
     *             ),
     *             @OA\Property(
     *                 property="descriptions",
     *                 type="array",
     *                 description="Comprehensive dataset descriptions",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="description", type="string", example="This dataset comprises 1D and 2D NMR spectra (1H, 13C, COSY, HSQC, HMBC) of 15 bioactive compounds isolated from Mediterranean marine algae. Spectra were acquired at 600 MHz using multiple solvents (CDCl3, DMSO-d6, CD3OD) at 298K. Chemical shifts are referenced to internal standards and molecular structures are confirmed through comprehensive spectroscopic analysis."),
     *                     @OA\Property(property="descriptionType", type="string", example="Abstract"),
     *                     @OA\Property(property="lang", type="string", example="en")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="subjects",
     *                 type="array",
     *                 description="Research domain classifications",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="subject", type="string", example="Marine Natural Products"),
     *                     @OA\Property(property="subjectScheme", type="string", example="Chemical Entities of Biological Interest (ChEBI)"),
     *                     @OA\Property(property="valueURI", type="string", example="http://purl.obolibrary.org/obo/CHEBI_25106")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="alternateIdentifiers",
     *                 type="array",
     *                 description="Alternative identification schemes",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="alternateIdentifier", type="string", example="P1234"),
     *                     @OA\Property(property="alternateIdentifierType", type="string", example="NMRXIV Project ID")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="rightsList",
     *                 type="array",
     *                 description="Data usage rights and licensing",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="rights", type="string", example="Creative Commons Attribution 4.0 International"),
     *                     @OA\Property(property="rightsURI", type="string", example="https://creativecommons.org/licenses/by/4.0/"),
     *                     @OA\Property(property="rightsIdentifier", type="string", example="CC-BY-4.0"),
     *                     @OA\Property(property="rightsIdentifierScheme", type="string", example="SPDX")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="version",
     *                 type="string",
     *                 description="Dataset version information",
     *                 example="1.0"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid identifier format",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid NMRXIV identifier format for DataCite metadata generation"),
     *             @OA\Property(property="expected_format", type="string", example="Valid format: [P|S|D] followed by numbers (e.g., P123, S456, D789)"),
     *             @OA\Property(property="provided_identifier", type="string", example="INVALID123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Data not accessible for public metadata export",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="This data entry is not available for public DataCite metadata export"),
     *             @OA\Property(property="identifier", type="string", example="P1234"),
     *             @OA\Property(property="access_policy", type="string", example="Only publicly available datasets can generate DataCite metadata for DOI registration")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Not found - Data entry does not exist",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No scientific data found for identifier: P9999"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Verify the NMRXIV identifier is correct", "Check if the data has been archived or moved", "Search for similar datasets using the search API"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity - Incomplete metadata for DataCite requirements",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Insufficient metadata to generate valid DataCite schema"),
     *             @OA\Property(property="missing_required_fields", type="array", @OA\Items(type="string"), example={"creator information", "publication year", "resource type"}),
     *             @OA\Property(property="recommendation", type="string", example="Contact the data owner to complete the required metadata fields")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - DataCite schema generation failed",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Internal error occurred during DataCite metadata generation"),
     *             @OA\Property(property="error_code", type="string", example="DATACITE_SCHEMA_ERROR"),
     *             @OA\Property(property="technical_details", type="string", example="Failed to process contributor affiliations data")
     *         )
     *     )
     * )
     *
     * Retrieve DataCite metadata schema by NMRXIV identifier
     *
     * **DataCite Integration Benefits:**
     * DataCite is the leading international DOI registration agency for research data,
     * providing persistent identifiers that enable proper citation of scientific datasets.
     *
     * **Metadata Schema Features:**
     * - **DOI Registration Ready**: Compliant with DataCite API requirements
     * - **Citation Standards**: Supports academic citation best practices
     * - **Research Impact**: Enables tracking of dataset usage and citations
     * - **Institutional Compliance**: Meets repository and funding requirements
     * - **Global Discovery**: Indexed in DataCite Commons and scholarly databases
     *
     * **Use Cases:**
     * - Automated DOI minting for published datasets
     * - Repository metadata export and synchronization
     * - Academic citation workflow integration
     * - Research data management compliance
     * - Institutional repository interoperability
     * - Grant reporting and impact assessment
     *
     * **Supported Data Types:**
     * - **Projects**: Research investigations with multiple datasets
     * - **Studies**: Individual experimental studies with samples
     * - **Datasets**: Specific NMR experiments and spectroscopic data
     *
     * @param  string  $identifier  NMRXIV public identifier (P123, S456, D789)
     * @return \Illuminate\Http\JsonResponse
     */
    public function modelSchemaByID(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $model = $resolvedModel['model'];

        $modelDatacite = $model->datacite_schema;

        return $modelDatacite;
    }
}
