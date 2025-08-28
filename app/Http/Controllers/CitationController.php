<?php

namespace App\Http\Controllers;

use App\Http\Resources\CitationResource;
use App\Models\Project;
use App\Services\CitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CitationController extends Controller
{
    /**
     * Create a new CitationController instance.
     */
    public function __construct(
        private CitationService $citationService
    ) {}

    /**
     * Save and sync updated citation details for a project.
     */
    public function save(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to update citations for this project.');
        }

        try {
            // Validate the request structure first
            $request->validate([
                'citations' => ['required', 'array', 'max:100'],
                'citations.*' => ['required', 'array'],
            ]);

            $citations = $request->get('citations', []);

            if (count($citations) > 0) {
                $processedCitations = $this->citationService->syncCitations($project, $citations, $request->user());

                return $this->successResponse($request, 'Citation updated successfully', $processedCitations);
            }

            return $this->successResponse($request, 'No citations to process');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse($request, 'An error occurred while updating citations.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete citation for a project.
     */
    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to remove citations from this project.');
        }

        try {
            // Validate request structure
            $request->validate([
                'citations' => ['required', 'array', 'min:1'],
                'citations.*.id' => ['required', 'integer', 'min:1'],
            ]);

            $citations = $request->get('citations');

            if (count($citations) > 0) {
                $this->citationService->removeCitationFromProject($project, $citations[0]['id']);
            }

            return $this->successResponse($request, 'Citation deleted successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse($request, 'An error occurred while deleting the citation.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return unauthorized response for failed authorization checks.
     */
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
     * Return success response with optional data payload.
     */
    private function successResponse(Request $request, string $message, array $data = []): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            $response = [
                'message' => $message,
                'success' => true,
            ];

            // Add structured data using CitationResource if citations are provided
            if (! empty($data)) {
                $response['data'] = [
                    'citations' => CitationResource::collection($data),
                ];
            }

            return response()->json($response, Response::HTTP_OK);
        }

        return back()->with('success', $message);
    }

    /**
     * Return validation error response with detailed error messages.
     */
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

    /**
     * Return generic error response with custom message and status code.
     */
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
