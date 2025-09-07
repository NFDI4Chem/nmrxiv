<?php

namespace App\Http\Controllers\API;

use App\Actions\Draft\CreateDraft;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudyResource;
use App\Jobs\ProcessDraftELNSubmission;
use App\Models\Draft;
use App\Models\Study;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ELNController extends Controller
{
    /**
     * Supported ELN systems
     */
    const SUPPORTED_ELNS = [
        'chemotion',
    ];

    /**
     * @OA\Post(
     *     path="/api/v1/{eln}/upload",
     *     operationId="uploadELNData",
     *     tags={"ELN Submission"},
     *     summary="Upload and process data from Electronic Lab Notebook (ELN) systems",
     *     description="Creates or updates a draft with data from external ELN systems. Currently supports Chemotion. Processes ZIP files containing experimental data and extracts them to organized folder structure.",
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="eln",
     *         in="path",
     *         required=true,
     *         description="ELN system identifier",
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"chemotion"},
     *             example="chemotion"
     *         )
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="ELN upload data",
     *
     *         @OA\JsonContent(
     *             required={"external_id", "callback_url", "zip_url"},
     *
     *             @OA\Property(
     *                 property="external_id",
     *                 type="string",
     *                 description="Unique identifier from the external ELN system",
     *                 example="CHEM-EXP-2024-001"
     *             ),
     *             @OA\Property(
     *                 property="callback_url",
     *                 type="string",
     *                 format="url",
     *                 description="URL to callback after processing is complete",
     *                 example="https://chemotion.example.com/api/callback"
     *             ),
     *             @OA\Property(
     *                 property="zip_url",
     *                 type="string",
     *                 format="url",
     *                 description="URL to download the ZIP file containing experimental data",
     *                 example="https://chemotion.example.com/exports/experiment-data.zip"
     *             ),
     *             @OA\Property(
     *                 property="release_date",
     *                 type="string",
     *                 format="date",
     *                 description="Optional future release date for the data (ISO 8601 format)",
     *                 example="2026-12-31"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="ELN data successfully queued for processing",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="ELN upload endpoint ready"
     *             ),
     *             @OA\Property(
     *                 property="eln_system",
     *                 type="string",
     *                 example="chemotion"
     *             ),
     *             @OA\Property(
     *                 property="draft_id",
     *                 type="integer",
     *                 example=123
     *             ),
     *             @OA\Property(
     *                 property="draft_key",
     *                 type="string",
     *                 example="550e8400-e29b-41d4-a716-446655440000"
     *             ),
     *             @OA\Property(
     *                 property="external_id",
     *                 type="string",
     *                 example="CHEM-EXP-2024-001"
     *             ),
     *             @OA\Property(
     *                 property="callback_url",
     *                 type="string",
     *                 example="https://chemotion.example.com/api/callback"
     *             ),
     *             @OA\Property(
     *                 property="zip_url",
     *                 type="string",
     *                 example="https://chemotion.example.com/exports/experiment-data.zip"
     *             ),
     *             @OA\Property(
     *                 property="release_date",
     *                 type="string",
     *                 format="date",
     *                 nullable=true,
     *                 example="2026-12-31"
     *             ),
     *             @OA\Property(
     *                 property="created_new",
     *                 type="boolean",
     *                 description="Whether a new draft was created or existing one was updated",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 example=456
     *             ),
     *             @OA\Property(
     *                 property="team_id",
     *                 type="integer",
     *                 example=789
     *             ),
     *             @OA\Property(
     *                 property="processing_status",
     *                 type="string",
     *                 example="job_dispatched"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation errors",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="External ID is required"
     *             ),
     *             @OA\Property(
     *                 property="supported_elns",
     *                 type="array",
     *
     *                 @OA\Items(type="string"),
     *                 example={"chemotion"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - authentication required",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Authentication required"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Not found - unsupported ELN system",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The route api/v1/invalid-eln/upload could not be found."
     *             )
     *         )
     *     )
     * )
     *
     * Handle file upload for a specific ELN entry
     *
     * @param  string  $eln
     * @return \Illuminate\Http\Response
     */
    public function upload($eln, Request $request)
    {
        // Validate that the ELN is supported
        if (! in_array(strtolower($eln), self::SUPPORTED_ELNS)) {
            return response()->json([
                'error' => 'Unsupported ELN system',
                'supported_elns' => self::SUPPORTED_ELNS,
            ], 400);
        }

        // Check authentication
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'error' => 'Authentication required',
            ], 401);
        }

        // Get user and team info using same logic as DraftController
        $team = $user->currentTeam;
        if ($team->personal_team) {
            $user_id = $user->id;
            $team_id = $team->id;
        } else {
            $team_id = $user->current_team_id;
            $user_id = $team->user_id;
        }

        // Validate the request to get the external id and throw an error if it's not present
        $externalId = $request->input('external_id');
        if (! $externalId) {
            return response()->json([
                'error' => 'External ID is required',
                'external_id' => $externalId,
            ], 400);
        }

        // Validate callback_url
        $callbackUrl = $request->input('callback_url');
        if (! $callbackUrl || ! filter_var($callbackUrl, FILTER_VALIDATE_URL)) {
            return response()->json([
                'error' => 'Callback URL is required and must be a valid URL',
                'callback_url' => $callbackUrl,
            ], 400);
        }

        // Validate zip_url
        $zipUrl = $request->input('zip_url');
        if (! $zipUrl || ! filter_var($zipUrl, FILTER_VALIDATE_URL)) {
            return response()->json([
                'error' => 'ZIP URL is required and must be a valid URL',
                'zip_url' => $zipUrl,
            ], 400);
        }

        $createDraft = new CreateDraft;

        // check if study already exists
        $study = Study::where('external_id', $externalId)->first();
        if ($study && $study->is_public && $study->draft == null) {
            return response()->json([
                'error' => 'A submission with this '.$eln.' external ID already exists and is published',
                'external_id' => $externalId,
                'external_url' => $study->external_url,
                'nmrxiv_id' => $study->identifier,
            ], 400);
        }

        $draft = $createDraft->findByExternalId($externalId, $user_id, $team_id);

        $isNewDraft = false;

        if (! $draft) {
            $isNewDraft = true;

            // Prepare ELN options for draft creation
            $elnOptions = [
                'eln' => $eln,
                'external_id' => $externalId,
                'callback_url' => $callbackUrl,
                'eln_status' => 'RECEIVED',
                'zip_url' => $zipUrl,
            ];

            // Add release date if provided and valid
            $releaseDate = $request->input('release_date');
            if ($releaseDate && Carbon::parse($releaseDate)->isFuture()) {
                $elnOptions['release_date'] = Carbon::parse($releaseDate)->toDateString();
            }

            // Create new draft using the action
            $draft = $createDraft->execute($user, $elnOptions);
        } else {
            // Update existing draft with new URLs using the action
            $updateData = [
                'callback_url' => $callbackUrl,
                'zip_url' => $zipUrl,
            ];

            // Update release date if provided and valid
            $releaseDate = $request->input('release_date');
            if ($releaseDate && Carbon::parse($releaseDate)->isFuture()) {
                $updateData['release_date'] = Carbon::parse($releaseDate)->toDateString();
            }

            $draft = $createDraft->update($draft, $updateData);
        }

        // Dispatch job to process the zip file
        ProcessDraftELNSubmission::dispatch($draft->id);

        return response()->json([
            'draft_id' => $draft->id,
            'draft_key' => $draft->key,
            'external_id' => $draft->external_id,
            'callback_url' => $draft->callback_url,
            'zip_url' => $draft->zip_url,
            'release_date' => $draft->release_date,
            'created_new' => $isNewDraft,
            'user_id' => $user_id,
            'team_id' => $team_id,
            'eln_status' => $draft->eln_status,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/{eln}/status/{external_id}",
     *     operationId="getELNStatus",
     *     tags={"ELN Submission"},
     *     summary="Get processing status of ELN submission",
     *     description="Retrieves the current processing status and details of a draft submission identified by external_id from the specified ELN system.",
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="eln",
     *         in="path",
     *         required=true,
     *         description="ELN system identifier",
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"chemotion"},
     *             example="chemotion"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="external_id",
     *         in="path",
     *         required=true,
     *         description="External ID from the ELN system",
     *
     *         @OA\Schema(
     *             type="string",
     *             example="CHEM-EXP-2024-001"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Draft status retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="draft_id",
     *                     type="integer",
     *                     example=123
     *                 ),
     *                 @OA\Property(
     *                     property="draft_key",
     *                     type="string",
     *                     example="550e8400-e29b-41d4-a716-446655440000"
     *                 ),
     *                 @OA\Property(
     *                     property="external_id",
     *                     type="string",
     *                     example="CHEM-EXP-2024-001"
     *                 ),
     *                 @OA\Property(
     *                     property="eln_system",
     *                     type="string",
     *                     example="chemotion"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="ELN Import (CHEMOTION: 550e8400)"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Draft created from ELN system: chemotion"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     nullable=true,
     *                     description="Processing status of the submission",
     *                     example="zip_processed"
     *                 ),
     *                 @OA\Property(
     *                     property="current_step",
     *                     type="string",
     *                     nullable=true,
     *                     description="Current processing step",
     *                     example="1"
     *                 ),
     *                 @OA\Property(
     *                     property="callback_url",
     *                     type="string",
     *                     nullable=true,
     *                     example="https://chemotion.example.com/api/callback"
     *                 ),
     *                 @OA\Property(
     *                     property="zip_url",
     *                     type="string",
     *                     nullable=true,
     *                     example="https://chemotion.example.com/exports/experiment-data.zip"
     *                 ),
     *                 @OA\Property(
     *                     property="release_date",
     *                     type="string",
     *                     format="date",
     *                     nullable=true,
     *                     example="2026-12-31"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-07-07T09:30:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-07-07T09:35:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="owner_id",
     *                     type="integer",
     *                     example=456
     *                 ),
     *                 @OA\Property(
     *                     property="team_id",
     *                     type="integer",
     *                     example=789
     *                 ),
     *                 @OA\Property(
     *                     property="files_count",
     *                     type="integer",
     *                     description="Number of files associated with this draft",
     *                     example=5
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - unsupported ELN system",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unsupported ELN system"
     *             ),
     *             @OA\Property(
     *                 property="supported_elns",
     *                 type="array",
     *
     *                 @OA\Items(type="string"),
     *                 example={"chemotion"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - authentication required",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Authentication required"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Draft not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Draft not found with the provided external ID"
     *             ),
     *             @OA\Property(
     *                 property="external_id",
     *                 type="string",
     *                 example="CHEM-EXP-2024-001"
     *             )
     *         )
     *     )
     * )
     *
     * Get processing status of ELN submission by external ID
     *
     * @param  string  $eln
     * @param  string  $external_id
     * @return \Illuminate\Http\Response
     */
    public function status($eln, $external_id)
    {
        // Validate that the ELN is supported
        if (! in_array(strtolower($eln), self::SUPPORTED_ELNS)) {
            return response()->json([
                'error' => 'Unsupported ELN system',
                'supported_elns' => self::SUPPORTED_ELNS,
            ], 400);
        }

        // Check authentication
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'error' => 'Authentication required',
            ], 401);
        }

        // Get user and team info using same logic as upload method
        $team = $user->currentTeam;
        if ($team->personal_team) {
            $user_id = $user->id;
            $team_id = $team->id;
        } else {
            $team_id = $user->current_team_id;
            $user_id = $team->user_id;
        }

        // Find the draft by external_id and ensure it belongs to the user/team
        $draft = Draft::where('external_id', $external_id)
            ->where('eln', strtolower($eln))
            ->where('owner_id', $user_id)
            ->where('team_id', $team_id)
            ->withCount('files')
            ->first();

        if ($draft) {
            return response()->json([
                'success' => true,
                'data' => [
                    'draft_id' => $draft->id,
                    'draft_key' => $draft->key,
                    'external_id' => $draft->external_id,
                    'eln_system' => $draft->eln,
                    'name' => $draft->name,
                    'description' => $draft->description,
                    'status' => $draft->status ?? null,
                    'current_step' => $draft->current_step ?? null,
                    'callback_url' => $draft->callback_url,
                    'zip_url' => $draft->zip_url,
                    'release_date' => $draft->release_date,
                    'created_at' => $draft->created_at,
                    'updated_at' => $draft->updated_at,
                    'owner_id' => $draft->owner_id,
                    'team_id' => $draft->team_id,
                    'files_count' => $draft->files_count,
                ],
            ]);
        } else {
            $studies = Study::with(['sample.molecules', 'datasets'])
                ->where('external_id', $external_id)
                ->get();

            if ($studies->count() > 0) {
                $studyResources = $studies->map(function ($study) {
                    return (new StudyResource($study))->lite(false, ['sample', 'datasets']);
                });

                return response()->json([
                    'success' => true,
                    'data' => $studyResources,
                ]);
            } else {
                return response()->json([
                    'error' => 'Submission not found with the provided external ID',
                    'external_id' => $external_id,
                ], 404);
            }
        }
    }
}
