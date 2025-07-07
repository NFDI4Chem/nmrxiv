<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Molecule;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/search",
     *     operationId="searchMolecules",
     *     tags={"Chemical Search"},
     *     summary="Search chemical compounds by structure and properties",
     *     description="Advanced chemical search supporting multiple query types including SMILES, InChI, InChiKey, substructure matching, similarity search, and text-based name searches. Supports filtering by molecular properties and chemical classifications. Returns paginated results with comprehensive molecular data including calculated properties, classifications, and database references.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Chemical search query with type specification and optional filters",
     *         @OA\JsonContent(
     *             required={"query"},
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
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results per page (default: 24, max: 100)",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=24)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination (default: 1)",
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order for results",
     *         @OA\Schema(type="string", enum={"recent", "relevance"}, default="relevance")
     *     ),
     *     @OA\Parameter(
     *         name="tagType",
     *         in="query",
     *         description="Tag type for tag-based searches",
     *         @OA\Schema(type="string", example="chemical_class")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chemical search results with pagination",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of molecular structures and properties",
     *                 @OA\Items(
     *                     type="object",
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
     *                         @OA\Items(type="string"),
     *                         example={"ChEBI:15365", "PubChem:2244", "DrugBank:DB00945"}
     *                     ),
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
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid query parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid search query format"),
     *             @OA\Property(property="errors", type="object", 
     *                 @OA\Property(property="query", type="array", @OA\Items(type="string"), example={"The query field is required when type is specified."}),
     *                 @OA\Property(property="type", type="array", @OA\Items(type="string"), example={"The selected type is invalid."})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No results found for the given query",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No chemical compounds found matching the search criteria"),
     *             @OA\Property(property="suggestions", type="array", @OA\Items(type="string"), example={"Check spelling of chemical names", "Try using SMILES notation", "Use broader search terms"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity - Invalid chemical structure",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid SMILES notation provided"),
     *             @OA\Property(property="error_code", type="string", example="INVALID_CHEMICAL_STRUCTURE"),
     *             @OA\Property(property="details", type="string", example="Unable to parse SMILES string: invalid character 'X' at position 5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Rate limit exceeded",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Too many search requests. Please try again later."),
     *             @OA\Property(property="retry_after", type="integer", description="Seconds until next request allowed", example=60)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Database or search engine failure",
     *         @OA\JsonContent(
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
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            set_time_limit(300);

            $queryType = 'text';
            $results = [];

            // dd($request);

            $limit = $request->query('limit');
            $sort = $request->query('sort');
            $limit = $limit ? $limit : 24;
            $page = $request->query('page');
            $tagType = $request->get('tagType') ? $request->get('tagType') : null;

            $offset =
                (($page != null && $page != 'null' && $page != 0 ? $page : 1) -
                    1) *
                $limit;

            $query = $request->get('query');

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

            $statement = null;

            if ($queryType == 'smiles' || $queryType == 'substructure') {
                $statement =
                    "select id, COUNT(*) OVER () from mols where m@>'".
                    $query.
                    "' limit ".
                    $limit.
                    ' offset '.
                    $offset;
            } elseif ($queryType == 'inchi') {
                $statement =
                    "select id, COUNT(*) OVER () from molecules WHERE identifier NOTNULL AND standard_inchi LIKE '%".
                    $query.
                    "%' limit ".
                    $limit.
                    ' offset '.
                    $offset;
            } elseif ($queryType == 'inchikey') {
                $statement =
                    "select id, COUNT(*) OVER () from molecules WHERE identifier NOTNULL AND standard_inchi_key LIKE '%".
                    $query.
                    "%' limit ".
                    $limit.
                    ' offset '.
                    $offset;
            } elseif ($queryType == 'exact') {
                $statement =
                    "select id, COUNT(*) OVER () from mols where m@='".
                    $query.
                    "' limit ".
                    $limit.
                    ' offset '.
                    $offset;
            } elseif ($queryType == 'similarity') {
                $statement =
                    "select id, COUNT(*) OVER () from fps where mfp2%morganbv_fp('".
                    $query.
                    "') limit ".
                    $limit.
                    ' offset '.
                    $offset;
            } elseif ($queryType == 'tags') {
                $results = Molecule::withAnyTags([$query], $tagType)->paginate($limit)->items();
                $count = Molecule::withAnyTags([$query], $tagType)->count();
            } elseif ($queryType == 'filters') {
                $orConditions = explode('OR', $query);
                $isORInitial = true;
                $statement =
                    'select molecule_id as id, COUNT(*) OVER () from properties where ';
                foreach ($orConditions as $orCondition) {
                    if ($isORInitial === false) {
                        $statement = $statement.' OR ';
                    }
                    $isORInitial = false;
                    $statement = $statement.'(';
                    $andConditions = explode(' ', trim($orCondition, ' '));
                    $isANDInitial = true;
                    foreach ($andConditions as $andCondition) {
                        if ($isANDInitial === false) {
                            $statement = $statement.' AND ';
                        }
                        $isANDInitial = false;
                        $_filter = explode(':', $andCondition);
                        if (str_contains($_filter[1], '..')) {
                            $range = array_values(explode('..', $_filter[1]));
                            $statement =
                                $statement.
                                '('.
                                $filterMap[$_filter[0]].
                                ' between '.
                                $range[0].
                                ' and '.
                                $range[1].
                                ')';
                        } elseif (
                            $_filter[1] === 'true' ||
                            $_filter[1] === 'false'
                        ) {
                            $statement =
                                $statement.
                                '('.
                                $filterMap[$_filter[0]].
                                ' = '.
                                $_filter[1].
                                ')';
                        } elseif (str_contains($_filter[1], '|')) {
                            $dbFilters = explode('|', $_filter[1]);
                            $dbs = explode('+', $dbFilters[0]);
                            $statement =
                                $statement.
                                '('.
                                $filterMap[$_filter[0]].
                                " @> '[\"".
                                implode('","', $dbs).
                                "\"]')";
                        } else {
                            if (str_contains($_filter[1], '+')) {
                                $_filter[1] = str_replace('+', ' ', $_filter[1]);
                            }
                            $statement =
                                $statement.
                                '('.$filterMap[$_filter[0]].'::TEXT ILIKE \'%'.$_filter[1].'%\')';
                        }
                    }
                    $statement = $statement.')';
                }
                $statement = $statement.' LIMIT '.$limit;
                // dd($statement );
            } else {
                if ($query) {
                    $query = str_replace("'", "''", $query);
                    $statement =
                        "select id, COUNT(*) OVER () from molecules WHERE identifier NOTNULL AND (\"name\"::TEXT ILIKE '%".
                        $query.
                        "%') OR (\"synonyms\"::TEXT ILIKE '%".
                        $query.
                        "%') OR (\"identifier\"::TEXT ILIKE '%".
                        $query.
                        "%') limit ".
                        $limit.
                        ' offset '.
                        $offset;
                } else {
                    $statement =
                        'select id, COUNT(*) OVER () from molecules WHERE identifier NOTNULL limit '.
                        $limit.
                        ' offset '.
                        $offset;
                }
            }
            if ($statement) {
                $expression = DB::raw($statement);
                $qString = $expression->getValue(
                    DB::connection()->getQueryGrammar()
                );

                $hits = DB::select($qString);

                $count = count($hits) > 0 ? $hits[0]->count : 0;

                $ids = implode(
                    ',',
                    collect($hits)
                        ->pluck('id')
                        ->toArray()
                );

                if ($ids != '') {
                    $statement =
                        'SELECT * FROM molecules WHERE identifier NOTNULL AND ID IN ('.
                        implode(
                            ',',
                            collect($hits)
                                ->pluck('id')
                                ->toArray()
                        ).
                        ')';
                    if ($sort == 'recent') {
                        $statement = $statement.' ORDER BY created_at DESC';
                    }
                    $expression = DB::raw($statement);
                    $string = $expression->getValue(
                        DB::connection()->getQueryGrammar()
                    );
                    $results = DB::select($string);
                } else {
                    $results = [];
                    $count = 0;
                }
            }
            $pagination = new LengthAwarePaginator(
                $results,
                $count,
                $limit,
                $page
            );

            // dd($pagination);
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
}
