<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatasetResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class DataController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/list/{model}",
     *     operationId="getPublicDataModels",
     *     tags={"Public Data Access"},
     *     summary="Retrieve public scientific data collections",
     *     description="Fetches paginated collections of publicly available scientific data models from the NMRXIV repository. Supports projects (research investigations), samples (chemical specimens), and datasets (NMR spectroscopy data). All returned data complies with FAIR data principles and Bioschemas.org standards for scientific data discovery.",
     *     @OA\Parameter(
     *         name="model",
     *         in="path",
     *         required=true,
     *         description="Type of scientific data model to retrieve",
     *         @OA\Schema(
     *             type="string",
     *             enum={"projects", "samples", "datasets"},
     *             example="projects"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page (default: 100, max: 500)",
     *         @OA\Schema(type="integer", minimum=1, maximum=500, default=100)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field with optional direction prefix (-created_at for descending)",
     *         @OA\Schema(type="string", enum={"created_at", "-created_at", "identifier", "-identifier", "owner.email", "-owner.email"}, default="-created_at")
     *     ),
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by name or title (case-insensitive partial match)",
     *         @OA\Schema(type="string", example="NMR analysis")
     *     ),
     *     @OA\Parameter(
     *         name="filter[identifier]",
     *         in="query",
     *         description="Filter by NMRXIV identifier (exact match)",
     *         @OA\Schema(type="string", example="P123")
     *     ),
     *     @OA\Parameter(
     *         name="filter[owner.email]",
     *         in="query",
     *         description="Filter by data owner email",
     *         @OA\Schema(type="string", format="email", example="researcher@university.edu")
     *     ),
     *     @OA\Parameter(
     *         name="filter[doi]",
     *         in="query",
     *         description="Filter by Digital Object Identifier",
     *         @OA\Schema(type="string", example="10.1000/xyz123")
     *     ),
     *     @OA\Parameter(
     *         name="filter[created_at]",
     *         in="query",
     *         description="Filter by creation date (ISO 8601 format or date range)",
     *         @OA\Schema(type="string", example="2024-01-01,2024-12-31")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Public scientific data collection retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of scientific data models",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", description="Internal database ID", example=123),
     *                     @OA\Property(property="identifier", type="string", description="Public NMRXIV identifier", example="P001234"),
     *                     @OA\Property(property="name", type="string", description="Title of the scientific investigation", example="Metabolomic Analysis of Plant Extracts"),
     *                     @OA\Property(property="description", type="string", description="Detailed description of the research", example="Comprehensive NMR-based metabolomic study of secondary metabolites in medicinal plants"),
     *                     @OA\Property(property="doi", type="string", description="Digital Object Identifier for citation", example="10.1000/nmrxiv.123456", nullable=true),
     *                     @OA\Property(property="is_public", type="boolean", description="Public availability status", example=true),
     *                     @OA\Property(
     *                         property="owner",
     *                         type="object",
     *                         description="Principal investigator or data owner",
     *                         @OA\Property(property="id", type="integer", example=789),
     *                         @OA\Property(property="name", type="string", example="Dr. Sarah Johnson"),
     *                         @OA\Property(property="email", type="string", format="email", example="sarah.johnson@university.edu"),
     *                         @OA\Property(property="orcid_id", type="string", description="ORCID identifier", example="0000-0002-1825-0097", nullable=true),
     *                         @OA\Property(property="affiliation", type="string", description="Institutional affiliation", example="University of Chemistry", nullable=true)
     *                     ),
     *                     @OA\Property(
     *                         property="license",
     *                         type="object",
     *                         description="Data usage license information",
     *                         @OA\Property(property="name", type="string", example="CC BY 4.0"),
     *                         @OA\Property(property="url", type="string", format="uri", example="https://creativecommons.org/licenses/by/4.0/"),
     *                         @OA\Property(property="description", type="string", example="Creative Commons Attribution 4.0 International")
     *                     ),
     *                     @OA\Property(
     *                         property="keywords",
     *                         type="array",
     *                         description="Research topic keywords",
     *                         @OA\Items(type="string"),
     *                         example={"NMR spectroscopy", "metabolomics", "natural products", "plant chemistry"}
     *                     ),
     *                     @OA\Property(
     *                         property="bioschemas",
     *                         type="object",
     *                         description="Bioschemas.org structured metadata",
     *                         @OA\Property(property="@type", type="string", example="Study"),
     *                         @OA\Property(property="@context", type="string", example="https://schema.org"),
     *                         @OA\Property(property="studyDomain", type="string", example="Chemistry"),
     *                         @OA\Property(property="studySubject", type="array", @OA\Items(type="string"), example={"chemical analysis", "molecular structure"})
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Last modification timestamp"),
     *                     @OA\Property(property="published_at", type="string", format="date-time", description="Publication timestamp", nullable=true),
     *                     @OA\Property(
     *                         property="statistics",
     *                         type="object",
     *                         description="Data collection statistics",
     *                         @OA\Property(property="samples_count", type="integer", description="Number of samples", example=25),
     *                         @OA\Property(property="datasets_count", type="integer", description="Number of datasets", example=150),
     *                         @OA\Property(property="spectra_count", type="integer", description="Number of NMR spectra", example=450),
     *                         @OA\Property(property="download_count", type="integer", description="Total downloads", example=1247)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 description="Pagination and response metadata",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=100),
     *                 @OA\Property(property="total", type="integer", description="Total available records", example=2547),
     *                 @OA\Property(property="last_page", type="integer", example=26),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=100)
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 description="Pagination navigation links",
     *                 @OA\Property(property="first", type="string", format="uri"),
     *                 @OA\Property(property="last", type="string", format="uri"),
     *                 @OA\Property(property="prev", type="string", format="uri", nullable=true),
     *                 @OA\Property(property="next", type="string", format="uri", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid model type or parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid model type specified"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="model", type="array", @OA\Items(type="string"), example={"The selected model is invalid. Must be one of: projects, samples, datasets"}),
     *                 @OA\Property(property="per_page", type="array", @OA\Items(type="string"), example={"The per_page must be between 1 and 500"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No public data found matching the criteria",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No public data available for the specified criteria"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Try broadening your search filters", "Check if data exists in other model types", "Contact repository administrators"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Too many API requests. Please try again later."),
     *             @OA\Property(property="retry_after", type="integer", example=60)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Database connection error"),
     *             @OA\Property(property="error_code", type="string", example="DATABASE_UNAVAILABLE")
     *         )
     *     )
     * )
     *
     * Retrieve public scientific data collections
     *
     * This endpoint provides access to publicly available scientific data from the NMRXIV repository:
     * 
     * - **Projects**: Research investigations with multiple samples and datasets
     * - **Samples**: Individual chemical specimens or biological materials  
     * - **Datasets**: Collections of NMR spectra and analytical data
     *
     * All data follows FAIR principles (Findable, Accessible, Interoperable, Reusable)
     * and includes Bioschemas.org metadata for enhanced discoverability.
     *
     * @param Request $request
     * @param string $model The data model type (projects|samples|datasets)
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all(Request $request, $model)
    {
        $per_page = \Request::get('per_page') ?: 100;

        $defaultSort = '-created_at';
        $allowedSorts = ['created_at', 'identifier', 'owner.email'];
        $allowedFilters = ['name', 'created_at', 'identifier', 'owner.email', 'doi'];
        if ($model === 'projects') {
            return ProjectResource::collection(
                QueryBuilder::for(Project::class)
                    ->where('is_public', true)
                    ->allowedSorts($allowedSorts)
                    ->allowedFilters($allowedFilters)
                    ->paginate($per_page)
                    ->appends(request()->query())
            );
        } elseif ($model === 'samples') {
            return StudyResource::collection(
                QueryBuilder::for(Study::class)
                    ->where('is_public', true)
                    ->allowedSorts($allowedSorts)
                    ->allowedFilters($allowedFilters)
                    ->paginate($per_page)
                    ->appends(request()->query())
            );
        } elseif ($model === 'datasets') {
            return DatasetResource::collection(
                QueryBuilder::for(Dataset::class)
                    ->where('is_public', true)
                    ->allowedSorts($allowedSorts)
                    ->allowedFilters($allowedFilters)
                    ->paginate($per_page)
                    ->appends(request()->query())
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/{id}",
     *     operationId="getPublicDataByIdentifier",
     *     tags={"Public Data Access"},
     *     summary="Retrieve specific public scientific data by identifier",
     *     description="Fetches detailed information for a specific publicly available scientific data entry using its NMRXIV identifier. Returns comprehensive metadata, associated files, measurement details, and structured data compliant with scientific data standards. Supports projects (P prefix), samples (S prefix), and datasets (D prefix).",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NMRXIV public identifier for the scientific data entry",
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[PSD][0-9]+$",
     *             example="P1234"
     *         ),
     *         @OA\Examples(example="project", value="P1234", summary="Project identifier"),
     *         @OA\Examples(example="sample", value="S5678", summary="Sample identifier"),
     *         @OA\Examples(example="dataset", value="D9012", summary="Dataset identifier")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Scientific data entry retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Internal database ID", example=1234),
     *             @OA\Property(property="identifier", type="string", description="Public NMRXIV identifier", example="P1234"),
     *             @OA\Property(property="name", type="string", description="Title of the scientific entry", example="Comprehensive NMR Analysis of Natural Product Library"),
     *             @OA\Property(property="description", type="string", description="Detailed scientific description", example="This project presents a systematic NMR-based characterization of 150 natural products isolated from marine organisms, including 1D and 2D NMR experiments for structure elucidation."),
     *             @OA\Property(property="doi", type="string", description="Digital Object Identifier", example="10.1000/nmrxiv.123456", nullable=true),
     *             @OA\Property(property="is_public", type="boolean", description="Public availability status", example=true),
     *             @OA\Property(
     *                 property="owner",
     *                 type="object",
     *                 description="Principal investigator or data owner",
     *                 @OA\Property(property="id", type="integer", example=789),
     *                 @OA\Property(property="name", type="string", example="Dr. Maria Rodriguez"),
     *                 @OA\Property(property="email", type="string", format="email", example="maria.rodriguez@marineinstitute.org"),
     *                 @OA\Property(property="orcid_id", type="string", example="0000-0003-1234-5678"),
     *                 @OA\Property(property="affiliation", type="string", example="Marine Chemistry Institute")
     *             ),
     *             @OA\Property(
     *                 property="team",
     *                 type="object",
     *                 description="Research team information",
     *                 @OA\Property(property="id", type="integer", example=456),
     *                 @OA\Property(property="name", type="string", example="Marine Natural Products Research Group"),
     *                 @OA\Property(
     *                     property="members",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                         @OA\Property(property="role", type="string", example="Co-investigator"),
     *                         @OA\Property(property="orcid_id", type="string", example="0000-0004-5678-9012")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="license",
     *                 type="object",
     *                 description="Data usage license",
     *                 @OA\Property(property="name", type="string", example="CC BY-SA 4.0"),
     *                 @OA\Property(property="url", type="string", format="uri", example="https://creativecommons.org/licenses/by-sa/4.0/"),
     *                 @OA\Property(property="description", type="string", example="Creative Commons Attribution-ShareAlike 4.0 International")
     *             ),
     *             @OA\Property(
     *                 property="experimental_details",
     *                 type="object",
     *                 description="Scientific methodology and experimental conditions",
     *                 @OA\Property(property="instruments", type="array", @OA\Items(type="string"), example={"Bruker Avance III 600 MHz", "Varian VNMRS 500 MHz"}),
     *                 @OA\Property(property="solvents", type="array", @OA\Items(type="string"), example={"CDCl3", "DMSO-d6", "CD3OD"}),
     *                 @OA\Property(property="temperature", type="string", example="298 K"),
     *                 @OA\Property(property="measurement_techniques", type="array", @OA\Items(type="string"), example={"1H NMR", "13C NMR", "2D COSY", "2D HSQC", "2D HMBC"})
     *             ),
     *             @OA\Property(
     *                 property="files",
     *                 type="array",
     *                 description="Associated data files and downloads",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=567),
     *                     @OA\Property(property="filename", type="string", example="compound_001_1H_NMR.fid"),
     *                     @OA\Property(property="size", type="integer", description="File size in bytes", example=1048576),
     *                     @OA\Property(property="type", type="string", example="NMR raw data"),
     *                     @OA\Property(property="format", type="string", example="Bruker FID"),
     *                     @OA\Property(property="download_url", type="string", format="uri", example="https://nmrxiv.org/api/v1/files/567/download"),
     *                     @OA\Property(property="checksum", type="string", description="SHA-256 hash for integrity", example="abc123...")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="related_data",
     *                 type="object",
     *                 description="Related scientific data entries",
     *                 @OA\Property(property="samples", type="array", @OA\Items(type="string"), example={"S001", "S002", "S003"}),
     *                 @OA\Property(property="datasets", type="array", @OA\Items(type="string"), example={"D001", "D002", "D003"}),
     *                 @OA\Property(property="publications", type="array", @OA\Items(type="string"), example={"10.1021/np2007234", "10.1016/j.phytochem.2023.113456"})
     *             ),
     *             @OA\Property(
     *                 property="bioschemas",
     *                 type="object",
     *                 description="Bioschemas.org structured metadata for enhanced discoverability",
     *                 @OA\Property(property="@type", type="string", example="Study"),
     *                 @OA\Property(property="@context", type="string", example="https://schema.org"),
     *                 @OA\Property(property="name", type="string", example="Marine Natural Products NMR Database"),
     *                 @OA\Property(property="description", type="string", example="Comprehensive NMR characterization of marine-derived natural products"),
     *                 @OA\Property(property="studyDomain", type="string", example="Marine Chemistry"),
     *                 @OA\Property(property="keywords", type="array", @OA\Items(type="string"), example={"marine natural products", "NMR spectroscopy", "structure elucidation"}),
     *                 @OA\Property(property="author", type="object", @OA\Property(property="@type", type="string", example="Person"), @OA\Property(property="name", type="string", example="Dr. Maria Rodriguez"))
     *             ),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-20T14:45:00Z"),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-18T09:00:00Z"),
     *             @OA\Property(
     *                 property="statistics",
     *                 type="object",
     *                 description="Usage and engagement statistics",
     *                 @OA\Property(property="view_count", type="integer", example=2847),
     *                 @OA\Property(property="download_count", type="integer", example=456),
     *                 @OA\Property(property="citation_count", type="integer", example=12),
     *                 @OA\Property(property="last_accessed", type="string", format="date-time", example="2024-01-25T16:20:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid identifier format",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid identifier format. Expected format: [P|S|D]followed by numbers"),
     *             @OA\Property(property="examples", type="array", @OA\Items(type="string"), example={"P1234", "S5678", "D9012"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Data is not publicly available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This data entry is not publicly available"),
     *             @OA\Property(property="contact", type="string", example="Contact the data owner for access requests"),
     *             @OA\Property(property="owner_email", type="string", format="email", example="researcher@university.edu")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found - Invalid identifier or data does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No data found for identifier: P9999"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Verify the identifier is correct", "Check if the data has been moved or deleted", "Use the search endpoint to find similar data"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error occurred while retrieving data"),
     *             @OA\Property(property="error_code", type="string", example="DATA_RETRIEVAL_ERROR")
     *         )
     *     )
     * )
     *
     * Retrieve specific public scientific data by identifier
     *
     * Returns comprehensive information for a single scientific data entry including:
     * - Complete metadata and experimental details
     * - Associated files and download links  
     * - Related data entries and publications
     * - Bioschemas.org structured metadata
     * - Usage statistics and engagement metrics
     *
     * @param Request $request
     * @param string $id NMRXIV identifier (P123, S456, D789)
     * @return \App\Http\Resources\ProjectResource|\App\Http\Resources\StudyResource|\App\Http\Resources\DatasetResource|\Illuminate\Http\JsonResponse
     */
    public function id(Request $request, $id)
    {
        try {
            $hidden = ['private_url', 'is_bookmarked', 'is_published'];
            $resolvedModel = resolveIdentifier($id);
            $namespace = $resolvedModel['namespace'];
            $model = $resolvedModel['model'];

            if ($model->is_public) {
                if ($namespace == 'Project') {
                    return (new ProjectResource(
                        $model
                    ))->lite(false);
                } elseif ($namespace == 'Study') {
                    return (new StudyResource(
                        $model
                    ))->lite(false);
                } elseif ($namespace == 'Dataset') {
                    return (new DatasetResource(
                        $model
                    ))->lite(false);
                }
            } else {
                throw new AuthorizationException;
            }

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Unprocessable Entity',
            ], 422);
        }
    }
}
