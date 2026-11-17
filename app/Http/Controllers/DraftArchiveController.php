<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExtractDraftArchivesRequest;
use App\Jobs\ExtractDraftArchive;
use App\Models\Draft;
use App\Models\FileSystemObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

/**
 * Queue and track server-side extraction of zip archives uploaded to a draft.
 */
class DraftArchiveController extends Controller
{
    /**
     * Queue extraction for the uploaded zip archives identified by storage key.
     */
    public function extract(ExtractDraftArchivesRequest $request, Draft $draft): JsonResponse
    {
        $paths = collect($request->validated('keys'))
            ->map(fn (string $key) => '/'.ltrim($key, '/'))
            ->unique()
            ->values();

        $archives = $this->zipFiles($draft)
            ->whereIn('path', $paths)
            ->get()
            ->filter(fn (FileSystemObject $archive) => $archive->isExtractableArchive())
            ->reject(fn (FileSystemObject $archive) => in_array(
                $archive->extraction()['status'] ?? null,
                [FileSystemObject::EXTRACTION_PENDING, FileSystemObject::EXTRACTION_PROCESSING],
                true
            ));

        foreach ($archives as $archive) {
            $archive->markExtraction(FileSystemObject::EXTRACTION_PENDING);

            ExtractDraftArchive::dispatch($draft->id, $archive->id);
        }

        return response()->json([
            'queued' => $archives->count(),
            'archives' => $this->present($archives),
        ], 202);
    }

    /**
     * List archives in the draft that are waiting, extracting, or failed.
     */
    public function index(Draft $draft): JsonResponse
    {
        $this->authorize('updateDraft', $draft);

        $archives = $this->zipFiles($draft)
            ->whereIn('info->extraction->status', [
                FileSystemObject::EXTRACTION_PENDING,
                FileSystemObject::EXTRACTION_PROCESSING,
                FileSystemObject::EXTRACTION_FAILED,
            ])
            ->get();

        return response()->json(['archives' => $this->present($archives)]);
    }

    /**
     * @return Builder<FileSystemObject>
     */
    private function zipFiles(Draft $draft): Builder
    {
        return FileSystemObject::query()
            ->where('draft_id', $draft->id)
            ->where('type', 'file')
            ->whereRaw('LOWER(name) LIKE ?', ['%.zip']);
    }

    /**
     * @param  Collection<int, FileSystemObject>  $archives
     * @return list<array{id: int, name: string, relative_url: string, path: string, status: string|null, error: string|null, extracted: int|null, total: int|null}>
     */
    private function present(Collection $archives): array
    {
        return $archives->map(function (FileSystemObject $archive) {
            $extraction = $archive->extraction() ?? [];

            return [
                'id' => $archive->id,
                'name' => $archive->name,
                'relative_url' => $archive->relative_url,
                'path' => $archive->path,
                'status' => $extraction['status'] ?? null,
                'error' => $extraction['error'] ?? null,
                'extracted' => $extraction['extracted'] ?? null,
                'total' => $extraction['total'] ?? null,
            ];
        })->values()->all();
    }
}
