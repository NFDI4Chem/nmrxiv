<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use App\Http\Resources\StudyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for handling oEmbed functionality.
 *
 * This controller provides oEmbed endpoints for embedding NMRXiv studies and datasets
 * in external applications. It supports the oEmbed 1.0 specification for rich content embedding.
 */
class OEmbedController extends Controller
{
    /**
     * Validate that the content is publicly accessible.
     *
     * @param  mixed  $model  The model to check
     * @return bool
     */
    private function validatePublicAccess($model): bool
    {
        if (! $model) {
            return false;
        }

        // Check if content is public
        if (! isset($model->is_public) || ! $model->is_public) {
            return false;
        }

        // Additional checks for archived/deleted content
        if (isset($model->is_archived) && $model->is_archived) {
            return false;
        }

        if (isset($model->is_deleted) && $model->is_deleted) {
            return false;
        }

        return true;
    }

    /**
     * Validate that the URL belongs to this application.
     *
     * @param  string  $url  The URL to validate
     * @return bool
     */
    private function validateOEmbedUrl(string $url): bool
    {
        $allowedHosts = [parse_url(config('app.url'), PHP_URL_HOST)];
        $requestHost = parse_url($url, PHP_URL_HOST);

        return $requestHost && in_array($requestHost, $allowedHosts, true);
    }

    /**
     * Sanitize text content to prevent XSS.
     *
     * @param  string|null  $text
     * @return string
     */
    private function sanitizeText(?string $text): string
    {
        if (! $text) {
            return '';
        }

        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate numeric parameters.
     *
     * @param  string  $value
     * @param  int  $min
     * @param  int  $max
     * @param  int  $default
     * @return int
     */
    private function validateNumericParameter(string $value, int $min = 100, int $max = 1000, int $default = 300): int
    {
        $numeric = filter_var($value, FILTER_VALIDATE_INT);
        if ($numeric === false || $numeric < $min || $numeric > $max) {
            return $default;
        }

        return $numeric;
    }
    /**
     * Generate oEmbed response for spectra content.
     *
     * This method processes oEmbed requests for NMRXiv spectra, returning standardized
     * oEmbed JSON responses that can be used by external applications to embed content.
     *
     * @param  Request  $request  The incoming HTTP request containing oEmbed parameters
     * @return JsonResponse JSON response containing oEmbed data or error message
     */
    public function spectra(Request $request): JsonResponse
    {
        // Validate input parameters
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|max:2048',
            'format' => 'sometimes|string|in:json',
            'height' => 'sometimes|string|max:10',
            'width' => 'sometimes|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        $url = $request->get('url');

        // Validate URL domain
        if (! $this->validateOEmbedUrl($url)) {
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        // Parse URL and extract identifier
        $parsedURL = parse_url($url);
        if (! $parsedURL || ! isset($parsedURL['path'])) {
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        $URLPath = $parsedURL['path'];
        $pathSegments = preg_split('#/#', $URLPath);
        $identifier = $pathSegments[1] ?? null;

        if (empty($identifier)) {
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        // Get optional parameters with proper validation
        $format = $request->get('format', 'json');
        $height = $this->validateNumericParameter($request->get('height', '300'));
        $width = $this->validateNumericParameter($request->get('width', '320'));

        try {
            $resolvedModel = resolveIdentifier($identifier);

            if (! $resolvedModel || ! is_array($resolvedModel)) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            $namespace = $resolvedModel['namespace'] ?? null;
            $model = $resolvedModel['model'] ?? null;

            if (! $model || ! isset($model->name) || ! isset($model->owner)) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            // CRITICAL SECURITY FIX: Check if content is publicly accessible
            if (! $this->validatePublicAccess($model)) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            // Safely get thumbnail URL
            $thumbnailUrl = null;
            if (isset($model->study_preview_urls) && is_array($model->study_preview_urls) && ! empty($model->study_preview_urls)) {
                $thumbnailUrl = filter_var($model->study_preview_urls[0], FILTER_VALIDATE_URL) ?: null;
            }

            // Sanitize all text outputs to prevent XSS
            $title = $this->sanitizeText($model->name ?? 'Untitled');
            $authorName = $this->sanitizeText($model->owner->name ?? 'Unknown Author');
            $authorUsername = $this->sanitizeText($model->owner->username ?? '');
            $embedIdentifier = $this->sanitizeText($model->identifier ?? $identifier);

            // Generate secure iframe HTML
            $iframeHtml = sprintf(
                '<iframe id="nmrxiv_embed" class="nmrxiv_embed_iframe" style="width: 100%%; overflow: hidden;" src="%s/embed/%s" width="%d" height="%d" frameborder="0" scrolling="no" sandbox="allow-scripts allow-same-origin"></iframe>',
                htmlspecialchars(config('app.url'), ENT_QUOTES),
                htmlspecialchars($embedIdentifier, ENT_QUOTES),
                $width,
                $height
            );

            $data = [
                'success' => true,
                'type' => 'rich',
                'version' => '1.0',
                'provider_name' => $this->sanitizeText(config('app.name') ?? 'NMRXiv'),
                'provider_url' => htmlspecialchars(config('app.url') ?? '', ENT_QUOTES),
                'title' => $title,
                'author_name' => $authorName,
                'author_url' => htmlspecialchars(config('app.url').'/author/'.$authorUsername, ENT_QUOTES),
                'height' => (string) $height,
                'width' => (string) $width,
                'thumbnail_width' => '300',
                'thumbnail_height' => '125',
                'thumbnail_url' => $thumbnailUrl,
                'html' => $iframeHtml,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request'], 500);
        }

        return response()->json(['error' => 'Content not found'], 404);
    }

    /**
     * Render embedded content for studies and datasets.
     *
     * This method generates the actual embedded view that will be displayed in iframes
     * when content is embedded via oEmbed. It supports both Study and Dataset models.
     *
     * @param  Request  $request  The incoming HTTP request
     * @param  string  $identifier  The unique identifier for the study or dataset
     * @return Response|JsonResponse Inertia response for the embedded content or JSON error response
     */
    public function embed(Request $request, string $identifier): Response|JsonResponse
    {
        // Validate identifier parameter
        if (empty($identifier) || strlen($identifier) > 50) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        try {
            $resolvedModel = resolveIdentifier($identifier);

            if (! $resolvedModel || ! is_array($resolvedModel)) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            $namespace = $resolvedModel['namespace'] ?? null;
            $model = $resolvedModel['model'] ?? null;

            if (! $model || empty($namespace)) {
                return response()->json(['error' => 'Content not found'], 404);
            }

            // CRITICAL SECURITY FIX: Check if content is publicly accessible
            if (! $this->validatePublicAccess($model)) {
                return response()->json(['error' => 'Content not found'], 404);
            }
            if ($namespace === 'Study') {
                $study = $model;

                return Inertia::render('Public/Embed/Sample', [
                    'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'owner', 'license']),
                ]);
            } elseif ($namespace === 'Dataset') {
                $dataset = $model;

                if (! isset($dataset->study)) {
                    return response()->json(['error' => 'Content not found'], 404);
                }

                $study = $dataset->study;

                // Additional security check for the related study
                if (! $this->validatePublicAccess($study)) {
                    return response()->json(['error' => 'Content not found'], 404);
                }

                return Inertia::render('Public/Embed/Dataset', [
                    'study' => (new StudyResource($study))->lite(false, ['tags', 'sample', 'datasets', 'molecules', 'owner', 'license']),
                    'dataset' => (new DatasetResource($dataset)),
                ]);
            }

            return response()->json(['error' => 'Content not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request'], 500);
        }

        return response()->json(['error' => 'Content not found'], 404);
    }
}
