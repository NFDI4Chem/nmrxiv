<?php

namespace App\Http\Controllers;

use App\Actions\FundingReference\RemoveFundingReference as RemoveFundingReferenceAction;
use App\Actions\FundingReference\SyncFundingReferences as SyncFundingReferencesAction;
use App\Http\Resources\FundingReferenceResource;
use App\Models\FundingReference;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class FundingReferenceController extends Controller
{
    public function __construct(
        private SyncFundingReferencesAction $syncFundingReferences,
        private RemoveFundingReferenceAction $removeFundingReference,
    ) {}

    /**
     * Save and sync updated funding reference details for a project.
     */
    public function save(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to update funding references for this project.');
        }

        try {
            $request->validate([
                'funding_references' => ['required', 'array', 'max:20'],
                'funding_references.*' => ['required', 'array'],
            ]);

            $fundingReferences = $request->get('funding_references', []);

            if (count($fundingReferences) > 0) {
                $processedFundingReferences = $this->syncFundingReferences->sync(
                    $project,
                    $fundingReferences,
                    $request->user()
                );

                return $this->successResponse(
                    $request,
                    'Funding reference updated successfully',
                    $processedFundingReferences
                );
            }

            return $this->successResponse($request, 'No funding references to process');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse(
                $request,
                'An error occurred while updating funding references.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete funding reference for a project.
     */
    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to remove funding references from this project.');
        }

        try {
            $request->validate([
                'funding_references' => ['required', 'array', 'min:1'],
                'funding_references.*.id' => [
                    'required',
                    'integer',
                    'min:1',
                    Rule::exists('funding_reference_project', 'funding_reference_id')
                        ->where(fn ($query) => $query->where('project_id', $project->id)),
                ],
            ]);

            $fundingReferences = $request->get('funding_references');

            if (count($fundingReferences) > 0) {
                $this->removeFundingReference->remove($project, $fundingReferences[0]['id']);
            }

            return $this->successResponse($request, 'Funding reference deleted successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse(
                $request,
                'An error occurred while deleting the funding reference.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function unauthorizedResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'error' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        return back()->with('error', $message);
    }

    /**
     * @param  array<int, FundingReference>  $data
     */
    private function successResponse(Request $request, string $message, array $data = []): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            $response = [
                'message' => $message,
                'success' => true,
            ];

            if ($data !== []) {
                $response['data'] = [
                    'funding_references' => FundingReferenceResource::collection($data),
                ];
            }

            return response()->json($response, Response::HTTP_OK);
        }

        return back()->with('success', $message);
    }

    private function validationErrorResponse(Request $request, ValidationException $exception): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return back()->withErrors($exception->errors())->withInput();
    }

    private function errorResponse(Request $request, string $message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'error' => true,
            ], $statusCode);
        }

        return back()->with('error', $message);
    }
}
