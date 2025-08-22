<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Models\Project;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * Create a new AuthorController instance.
     *
     * @return void
     */
    public function __construct(
        private AuthorService $authorService
    ) {}

    /**
     * Save and sync updated author details for a project.
     */
    public function save(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to update authors for this project.');
        }

        try {
            // Validate the request structure first
            $request->validate([
                'authors' => ['required', 'array', 'max:50'],
                'authors.*' => ['required', 'array'],
                'authors.*.given_name' => ['required', 'string'],
                'authors.*.family_name' => ['required', 'string'],
            ]);

            $authors = $request->get('authors', []);
            $processedAuthors = $this->authorService->syncAuthors($project, $authors);

            return $this->successResponse($request, 'Authors updated successfully', $processedAuthors);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse($request, 'An error occurred while updating authors.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete author for a project.
     */
    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to remove authors from this project.');
        }

        try {
            // Validate request structure
            $request->validate([
                'authors' => ['required', 'array', 'min:1'],
                'authors.*.id' => ['required', 'integer', 'min:1'],
            ]);

            $authors = $request->get('authors');
            $this->authorService->removeAuthorFromProject($project, $authors[0]['id']);

            return $this->successResponse($request, 'Author deleted successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse($request, 'An error occurred while deleting the author.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update existing contributor type for a given author in a project.
     */
    public function updateRole(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            return $this->unauthorizedResponse($request, 'You are not authorized to update author roles for this project.');
        }

        try {
            // Validate request structure
            $request->validate([
                'author_id' => ['required', 'integer', 'min:1'],
                'role' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s]+$/'],
            ]);

            $success = $this->authorService->updateContributorType(
                $project,
                $request->author_id,
                $request->role
            );

            if (! $success) {
                return $this->errorResponse($request, 'Invalid contributor type or missing author information.', Response::HTTP_BAD_REQUEST);
            }

            return $this->successResponse($request, 'Author role updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            return $this->errorResponse($request, 'An error occurred while updating the author role.', Response::HTTP_INTERNAL_SERVER_ERROR);
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

            // Add structured data using AuthorResource if authors are provided
            if (! empty($data)) {
                $response['data'] = [
                    'authors' => AuthorResource::collection($data),
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
