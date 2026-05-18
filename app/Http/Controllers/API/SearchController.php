<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Molecule;
use App\Support\Public\PublicMoleculeAggregates;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/search",
     *     operationId="searchMolecules",
     *     tags={"Chemical Search"},
     *     summary="Search chemical compounds by structure and properties",
     *     description="Advanced chemical search supporting multiple query types including SMILES, InChI, InChiKey, substructure matching, similarity search, and text-based name searches. Supports filtering by molecular properties and chemical classifications. Returns paginated results with comprehensive molecular data including calculated properties, classifications, and database references.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Chemical search query with type specification and optional filters",
     *
     *         @OA\JsonContent(
     *             required={"query"},
     *
     *             @OA\Property(
     *                 property="query",
     *                 type="string",
     *                 description="Search query - format depends on search type",
     *                 example="AAAAWQOPBUPWEV-UHFFFAOYSA-N"
     *             ),
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 description="Search query type - auto-detected if not specified",
     *                 enum={"text", "smiles", "inchi", "inchikey", "substructure", "exact", "similarity", "tags", "filters"},
     *                 example="inchikey"
     *             )
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results per page (default: 24, max: 100)",
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=24)
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination (default: 1)",
     *
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order for results",
     *
     *         @OA\Schema(type="string", enum={"recent", "relevance"}, default="relevance")
     *     ),
     *
     *     @OA\Parameter(
     *         name="tagType",
     *         in="query",
     *         description="Tag type for tag-based searches",
     *
     *         @OA\Schema(type="string", example="chemical_class")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Chemical search results with pagination",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of molecular structures and properties",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", description="Molecule database ID", example=12345),
     *                     @OA\Property(property="identifier", type="string", description="NMRXIV molecule identifier", example="M001234"),
     *                     @OA\Property(property="name", type="string", description="Chemical name", example="Aspirin"),
     *                     @OA\Property(property="synonyms", type="array", @OA\Items(type="string"), description="Alternative names", example={"Acetylsalicylic acid", "2-Acetoxybenzoic acid"}),
     *                     @OA\Property(property="molecular_formula", type="string", description="Molecular formula", example="C9H8O4"),
     *                     @OA\Property(property="molecular_weight", type="number", format="float", description="Molecular weight in g/mol", example=180.157),
     *                     @OA\Property(property="standard_inchi", type="string", description="International Chemical Identifier", example="InChI=1S/C9H8O4/c1-6(10)13-8-5-3-2-4-7(8)9(11)12/h2-5H,1H3,(H,11,12)"),
     *                     @OA\Property(property="standard_inchi_key", type="string", description="InChI Key", example="BSYNRYMUTXBXSQ-UHFFFAOYSA-N"),
     *                     @OA\Property(property="canonical_smiles", type="string", description="Canonical SMILES notation", example="CC(=O)OC1=CC=CC=C1C(=O)O"),
     *                     @OA\Property(
     *                         property="properties",
     *                         type="object",
     *                         description="Calculated molecular properties",
     *                         @OA\Property(property="heavy_atom_count", type="integer", description="Number of heavy atoms", example=13),
     *                         @OA\Property(property="total_atom_count", type="integer", description="Total atom count", example=21),
     *                         @OA\Property(property="aromatic_ring_count", type="integer", description="Number of aromatic rings", example=1),
     *                         @OA\Property(property="rotatable_bond_count", type="integer", description="Number of rotatable bonds", example=3),
     *                         @OA\Property(property="h_bond_acceptor_count", type="integer", description="Hydrogen bond acceptors", example=4),
     *                         @OA\Property(property="h_bond_donor_count", type="integer", description="Hydrogen bond donors", example=1),
     *                         @OA\Property(property="alogp", type="number", format="float", description="Lipophilicity (ALogP)", example=1.19),
     *                         @OA\Property(property="topo_psa", type="number", format="float", description="Topological polar surface area", example=63.6),
     *                         @OA\Property(property="np_likeness_score", type="number", format="float", description="Natural product likeness score", example=0.78)
     *                     ),
     *                     @OA\Property(
     *                         property="classifications",
     *                         type="object",
     *                         description="Chemical taxonomy classifications",
     *                         @OA\Property(property="chemical_super_class", type="string", example="Organic compounds"),
     *                         @OA\Property(property="chemical_class", type="string", example="Benzoic acids and derivatives"),
     *                         @OA\Property(property="chemical_sub_class", type="string", example="Salicylic acids"),
     *                         @OA\Property(property="direct_parent_classification", type="string", example="Acetate esters")
     *                     ),
     *                     @OA\Property(
     *                         property="database_links",
     *                         type="array",
     *                         description="External database identifiers",
     *
     *                         @OA\Items(type="string"),
     *                         example={"ChEBI:15365", "PubChem:2244", "DrugBank:DB00945"}
     *                     ),
     *
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Entry creation timestamp"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", description="Current page number", example=1),
     *             @OA\Property(property="per_page", type="integer", description="Results per page", example=24),
     *             @OA\Property(property="total", type="integer", description="Total number of matching results", example=150),
     *             @OA\Property(property="last_page", type="integer", description="Last page number", example=7),
     *             @OA\Property(property="from", type="integer", description="Starting result number", example=1),
     *             @OA\Property(property="to", type="integer", description="Ending result number", example=24),
     *             @OA\Property(
     *                 property="search_metadata",
     *                 type="object",
     *                 description="Search execution metadata",
     *                 @OA\Property(property="query_type", type="string", description="Detected or specified query type", example="inchikey"),
     *                 @OA\Property(property="execution_time", type="number", format="float", description="Search execution time in seconds", example=0.125),
     *                 @OA\Property(property="database_hits", type="integer", description="Raw database matches before filtering", example=152)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid query parameters",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid search query format"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="query", type="array", @OA\Items(type="string"), example={"The query field is required when type is specified."}),
     *                 @OA\Property(property="type", type="array", @OA\Items(type="string"), example={"The selected type is invalid."})
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="No results found for the given query",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No chemical compounds found matching the search criteria"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Check spelling of chemical names", "Try using SMILES notation", "Use broader search terms"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity - Invalid chemical structure",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid SMILES notation provided"),
     *             @OA\Property(property="error_code", type="string", example="INVALID_CHEMICAL_STRUCTURE"),
     *             @OA\Property(property="details", type="string", example="Unable to parse SMILES string: invalid character 'X' at position 5")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit exceeded",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Too many search requests. Please try again later."),
     *             @OA\Property(property="retry_after", type="integer", description="Seconds until next request allowed", example=60)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Database or search engine failure",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Chemical database search engine is temporarily unavailable"),
     *             @OA\Property(property="error_code", type="string", example="SEARCH_ENGINE_ERROR"),
     *             @OA\Property(property="support_reference", type="string", example="REF-2024-001234")
     *         )
     *     )
     * )
     *
     * Search chemical compounds by structure and properties
     *
     * Supports multiple search types:
     * - **Text**: Chemical names and synonyms
     * - **SMILES**: Simplified molecular-input line-entry system
     * - **InChI/InChIKey**: International Chemical Identifier
     * - **Substructure**: Molecular substructure matching
     * - **Similarity**: Molecular fingerprint similarity
     * - **Exact**: Exact structure matching
     * - **Tags**: Classification-based search
     * - **Filters**: Property-based filtering
     *
     * @return LengthAwarePaginator|JsonResponse
     */
    public function search(Request $request)
    {
        try {
            set_time_limit(300);

            // Validate and sanitize input parameters
            $validator = Validator::make($request->all(), [
                'query' => 'nullable|string|max:1000',
                'type' => ['nullable', 'string', Rule::in(['text', 'smiles', 'inchi', 'inchikey', 'substructure', 'exact', 'similarity', 'tags', 'filters'])],
                'limit' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'sort' => ['nullable', 'string', Rule::in(['recent', 'relevance'])],
                'tagType' => 'nullable|string|max:100|regex:/^[a-zA-Z_]+$/',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid input parameters',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $queryType = 'text';
            $results = [];

            $limit = (int) ($request->query('limit') ?? 24);
            $sort = $request->query('sort');
            $page = (int) ($request->query('page') ?? 1);
            $tagType = $request->get('tagType');

            $offset = ($page - 1) * $limit;

            $query = $this->sanitizeQuery($request->get('query'));

            if (($query === null || $query === '') && $sort === null) {
                $sort = 'recent';
            }

            $type = $request->query('type')
                ? $request->query('type')
                : $request->get('type');

            if ($type) {
                $queryType = $type;
            } else {
                // inchi
                $re =
                    '/^((InChI=)?[^J][0-9BCOHNSOPrIFla+\-\(\)\\\\\/,pqbtmsih]{6,})$/i';
                preg_match_all($re, $query, $imatches, PREG_SET_ORDER, 0);

                if (count($imatches) > 0 && substr($query, 0, 6) == 'InChI=') {
                    $queryType = 'inchi';
                }

                // inchikey
                $re = '/^([0-9A-Z\-]+)$/i';
                preg_match_all($re, $query, $ikmatches, PREG_SET_ORDER, 0);
                if (
                    count($ikmatches) > 0 &&
                    substr($query, 14, 1) == '-' &&
                    strlen($query) == 27
                ) {
                    $queryType = 'inchikey';
                }

                // smiles
                $re = '/^([^J][0-9BCOHNSOPrIFla@+\-\[\]\(\)\\\\\/%=#$]{6,})$/i';
                preg_match_all($re, $query, $matches, PREG_SET_ORDER, 0);

                if (count($matches) > 0 && substr($query, 14, 1) != '-') {
                    $queryType = 'smiles';
                }
            }

            $filterMap = [
                'mf' => 'molecular_formula',

                'mw' => 'molecular_weight',
                'hac' => 'heavy_atom_count',
                'tac' => 'total_atom_count',

                'arc' => 'aromatic_ring_count',
                'rbc' => 'rotatable_bond_count',
                'mrc' => 'minimal_number_of_rings',
                'fc' => 'formal_charge',
                'cs' => 'contains_sugar',
                'crs' => 'contains_ring_sugars',
                'cls' => 'contains_linear_sugars',

                'npl' => 'np_likeness_score',
                'alogp' => 'alogp',
                'topopsa' => 'topo_psa',
                'fsp3' => 'fsp3',
                'hba' => 'h_bond_acceptor_count',
                'hbd' => 'h_bond_donor_count',
                'ro5v' => 'rule_of_5_violations',
                'lhba' => 'lipinski_h_bond_acceptor_count',
                'lhbd' => 'lipinski_h_bond_donor_count',
                'lro5v' => 'lipinski_rule_of_5_violations',
                'ds' => 'found_in_databases',

                'class' => 'chemical_class',
                'subclass' => 'chemical_sub_class',
                'superclass' => 'chemical_super_class',
                'parent' => 'direct_parent_classification',

            ];

            $queryType = strtolower($queryType);

            $publicSpectraFilter = ' AND '.PublicMoleculeAggregates::hasPublicSpectraExistsSql('molecules.id');

            $statement = null;

            if ($queryType == 'smiles' || $queryType == 'substructure') {
                try {
                    $hits = DB::select(
                        "SELECT mols.id, COUNT(*) OVER () as count FROM mols
                        INNER JOIN molecules ON molecules.id = mols.id
                        WHERE m@>? AND molecules.identifier IS NOT NULL{$publicSpectraFilter}
                        LIMIT ? OFFSET ?",
                        [$query, $limit, $offset]
                    );
                } catch (\Exception $e) {
                    // Log the error and return empty results for invalid SMILES
                    \Log::warning('SMILES query error: '.$e->getMessage(), ['query' => $query]);
                    $hits = [];
                }
            } elseif ($queryType == 'inchi') {
                $hits = DB::select(
                    "SELECT id, COUNT(*) OVER () as count FROM molecules WHERE identifier IS NOT NULL AND standard_inchi LIKE ?{$publicSpectraFilter} LIMIT ? OFFSET ?",
                    ['%'.$query.'%', $limit, $offset]
                );
            } elseif ($queryType == 'inchikey') {
                $hits = DB::select(
                    "SELECT id, COUNT(*) OVER () as count FROM molecules WHERE identifier IS NOT NULL AND standard_inchi_key LIKE ?{$publicSpectraFilter} LIMIT ? OFFSET ?",
                    ['%'.$query.'%', $limit, $offset]
                );
            } elseif ($queryType == 'exact') {
                try {
                    $hits = DB::select(
                        "SELECT mols.id, COUNT(*) OVER () as count FROM mols
                        INNER JOIN molecules ON molecules.id = mols.id
                        WHERE m@=? AND molecules.identifier IS NOT NULL{$publicSpectraFilter}
                        LIMIT ? OFFSET ?",
                        [$query, $limit, $offset]
                    );
                } catch (\Exception $e) {
                    \Log::warning('Exact match query error: '.$e->getMessage(), ['query' => $query]);
                    $hits = [];
                }
            } elseif ($queryType == 'similarity') {
                try {
                    $hits = DB::select(
                        "SELECT fps.id, COUNT(*) OVER () as count FROM fps
                        INNER JOIN molecules ON molecules.id = fps.id
                        WHERE mfp2%morganbv_fp(?) AND molecules.identifier IS NOT NULL{$publicSpectraFilter}
                        LIMIT ? OFFSET ?",
                        [$query, $limit, $offset]
                    );
                } catch (\Exception $e) {
                    \Log::warning('Similarity query error: '.$e->getMessage(), ['query' => $query]);
                    $hits = [];
                }
            } elseif ($queryType == 'tags') {
                $tagQuery = PublicMoleculeAggregates::scopePublicCatalog(
                    Molecule::withAnyTags([$query], $tagType)
                );
                if ($sort === 'recent') {
                    $tagQuery->orderByDesc('created_at');
                }
                $results = PublicMoleculeAggregates::enrich(
                    $tagQuery->paginate($limit)->items()
                );
                $count = PublicMoleculeAggregates::scopePublicCatalog(
                    Molecule::withAnyTags([$query], $tagType)
                )->count();
            } elseif ($queryType == 'filters') {
                $result = $this->buildSecureFilterQuery($query, $filterMap, $limit, $offset);
                $hits = $result['hits'];
                $count = $result['count'];
            } else {
                if ($query) {
                    $hits = DB::select(
                        "SELECT id, COUNT(*) OVER () as count FROM molecules WHERE identifier IS NOT NULL AND (name::TEXT ILIKE ? OR synonyms::TEXT ILIKE ? OR identifier::TEXT ILIKE ?){$publicSpectraFilter} LIMIT ? OFFSET ?",
                        ['%'.$query.'%', '%'.$query.'%', '%'.$query.'%', $limit, $offset]
                    );
                } else {
                    $orderBy = $sort === 'recent' ? 'ORDER BY created_at DESC' : '';
                    $hits = DB::select(
                        "SELECT id, COUNT(*) OVER () as count FROM molecules WHERE identifier IS NOT NULL{$publicSpectraFilter} {$orderBy} LIMIT ? OFFSET ?",
                        [$limit, $offset]
                    );
                }
            }

            // Process results for non-tag queries
            if ($queryType !== 'tags' && $queryType !== 'filters') {
                $count = count($hits) > 0 ? $hits[0]->count : 0;

                $ids = collect($hits)->pluck('id')->toArray();

                if (! empty($ids)) {
                    $placeholders = str_repeat('?,', count($ids) - 1).'?';
                    $orderBy = $sort === 'recent' ? 'ORDER BY created_at DESC' : '';

                    $results = DB::select(
                        "SELECT * FROM molecules WHERE identifier IS NOT NULL AND id IN ({$placeholders}) {$orderBy}",
                        $ids
                    );
                } else {
                    $results = [];
                    $count = 0;
                }
            } elseif ($queryType === 'filters') {
                // Results already processed in buildSecureFilterQuery
                $ids = collect($hits)->pluck('id')->toArray();

                if (! empty($ids)) {
                    $placeholders = str_repeat('?,', count($ids) - 1).'?';
                    $orderBy = $sort === 'recent' ? 'ORDER BY created_at DESC' : '';

                    $results = DB::select(
                        "SELECT * FROM molecules WHERE identifier IS NOT NULL AND id IN ({$placeholders}) {$orderBy}",
                        $ids
                    );
                } else {
                    $results = [];
                }
            }

            if ($results !== []) {
                $results = PublicMoleculeAggregates::enrich($results);
            }

            $pagination = new LengthAwarePaginator(
                $results,
                $count,
                $limit,
                $page,
                ['path' => url('/compounds')]
            );

            $pagination->appends(array_filter([
                'query' => $query !== null && $query !== '' ? $query : null,
                'sort' => $sort,
                'limit' => $limit !== 24 ? $limit : null,
                'tagType' => $tagType,
                'type' => $queryType !== 'text' ? $queryType : null,
            ], fn ($value) => $value !== null && $value !== ''));

            return $pagination;
        } catch (QueryException $exception) {
            return response()->json(
                [
                    'message' => $exception->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Sanitize query input to prevent injection attacks
     */
    private function sanitizeQuery(?string $query): ?string
    {
        if (empty($query)) {
            return null;
        }

        // Remove null bytes and control characters
        $query = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $query);

        // Trim whitespace
        $query = trim($query);

        // Limit length
        $query = substr($query, 0, 1000);

        return $query;
    }

    /**
     * Build secure filter query with parameter binding
     */
    private function buildSecureFilterQuery(string $query, array $filterMap, int $limit, int $offset): array
    {
        try {
            $orConditions = explode('OR', $query);
            $whereConditions = [];
            $parameters = [];

            foreach ($orConditions as $orCondition) {
                $andConditions = explode(' ', trim($orCondition));
                $andClauses = [];

                foreach ($andConditions as $andCondition) {
                    // Skip empty conditions
                    if (empty(trim($andCondition))) {
                        continue;
                    }

                    $filter = explode(':', $andCondition, 2);

                    if (count($filter) !== 2) {
                        continue; // Skip invalid filters
                    }

                    $field = trim($filter[0]);
                    $value = trim($filter[1]);

                    // Skip if field or value is empty
                    if (empty($field) || empty($value)) {
                        continue;
                    }

                    // Validate field exists in filterMap
                    if (! isset($filterMap[$field])) {
                        continue; // Skip unknown fields
                    }

                    $dbField = $filterMap[$field];

                    // Additional validation: ensure dbField is safe (alphanumeric + underscore)
                    if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $dbField)) {
                        continue;
                    }

                    if (str_contains($value, '..')) {
                        // Range query
                        $range = explode('..', $value, 2);
                        if (count($range) === 2 && is_numeric($range[0]) && is_numeric($range[1])) {
                            $andClauses[] = "({$dbField} BETWEEN ? AND ?)";
                            $parameters[] = (float) $range[0];
                            $parameters[] = (float) $range[1];
                        }
                    } elseif ($value === 'true' || $value === 'false') {
                        // Boolean query
                        $andClauses[] = "({$dbField} = ?)";
                        $parameters[] = $value === 'true';
                    } elseif (str_contains($value, '|')) {
                        // Array contains query
                        $dbFilters = explode('|', $value, 2);
                        $dbs = explode('+', $dbFilters[0]);

                        // Validate database names (alphanumeric + underscore only)
                        $validDbs = array_filter($dbs, function ($db) {
                            return preg_match('/^[a-zA-Z0-9_]+$/', trim($db));
                        });

                        if (! empty($validDbs)) {
                            $jsonArray = json_encode($validDbs);
                            $andClauses[] = "({$dbField} @> ?)";
                            $parameters[] = $jsonArray;
                        }
                    } else {
                        // Text search - be more aggressive with sanitization
                        if (str_contains($value, '+')) {
                            $value = str_replace('+', ' ', $value);
                        }

                        // Remove any potentially dangerous characters, keep only safe ones
                        $value = preg_replace('/[^\w\s\-\.]/', '', $value);
                        $value = trim($value);

                        if (! empty($value) && strlen($value) > 0) {
                            $andClauses[] = "({$dbField}::TEXT ILIKE ?)";
                            $parameters[] = '%'.$value.'%';
                        }
                    }
                }

                if (! empty($andClauses)) {
                    $whereConditions[] = '('.implode(' AND ', $andClauses).')';
                }
            }

            if (empty($whereConditions)) {
                return ['hits' => [], 'count' => 0];
            }

            $whereClause = implode(' OR ', $whereConditions);
            $publicSpectraFilter = PublicMoleculeAggregates::hasPublicSpectraExistsSql('molecules.id');
            $sql = "SELECT properties.molecule_id as id, COUNT(*) OVER () as count
                FROM properties
                INNER JOIN molecules ON molecules.id = properties.molecule_id
                WHERE ({$whereClause})
                  AND molecules.identifier IS NOT NULL
                  AND {$publicSpectraFilter}
                LIMIT ? OFFSET ?";

            $parameters[] = $limit;
            $parameters[] = $offset;

            $hits = DB::select($sql, $parameters);
            $count = count($hits) > 0 ? $hits[0]->count : 0;

            return ['hits' => $hits, 'count' => $count];

        } catch (\Exception $e) {
            // Log the error but don't expose it to prevent information leakage
            \Log::warning('Filter query error: '.$e->getMessage(), ['query' => $query]);

            // Return empty results for any error to prevent SQL injection
            return ['hits' => [], 'count' => 0];
        }
    }
}
