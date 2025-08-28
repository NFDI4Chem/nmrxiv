<?php

namespace App\Services;

use App\Actions\Project\UpdateProject;
use App\Models\Citation;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CitationService
{
    /**
     * Create a new CitationService instance.
     */
    public function __construct(
        private UpdateProject $updater
    ) {}

    /**
     * Process and sync citations for a project.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function syncCitations(Project $project, array $citations, $user): array
    {
        if (empty($citations)) {
            return [];
        }

        // Eager load existing citations to prevent N+1 queries
        $project->load('citations');

        $processedCitations = [];

        foreach ($citations as $citationData) {
            $this->validateCitationData($citationData);

            $doi = $citationData['doi'];

            if (! is_null($doi)) {
                $citation = $this->findOrCreateCitation($project, $citationData, $doi);
                $processedCitations[] = $citation;
            }
        }

        // Use database transaction for bulk operations
        DB::transaction(function () use ($project, $processedCitations, $user): void {
            $this->updater->syncCitations($project, $processedCitations, $user);
        });

        return $processedCitations;
    }

    /**
     * Remove citation from project.
     */
    public function removeCitationFromProject(Project $project, int $citationId): void
    {
        DB::transaction(function () use ($project, $citationId): void {
            $this->updater->detachCitation($project, $citationId);
        });
    }

    /**
     * Validate citation data against validation rules.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateCitationData(array $citationData): void
    {
        Validator::make($citationData, [
            'title' => ['required', 'string'],
            'doi' => ['required', 'string'],
            'authors' => ['required', 'string'],
            'citation_text' => ['nullable', 'string'],
        ])->validate();
    }

    /**
     * Find existing citation for project or create new one.
     */
    private function findOrCreateCitation(Project $project, array $citationData, string $doi): Citation
    {
        $existingCitation = $project->citations->filter(function ($citation) use ($doi) {
            return $doi === $citation->doi;
        })->first();

        if ($existingCitation) {
            $existingCitation->update($this->prepareCitationAttributes($citationData));

            return $existingCitation;
        }

        return Citation::create($this->prepareCitationAttributes($citationData));
    }

    /**
     * Prepare citation attributes for create or update operations.
     */
    private function prepareCitationAttributes(array $citationData): array
    {
        return [
            'doi' => $citationData['doi'] ?? null,
            'title' => $citationData['title'] ?? null,
            'authors' => $citationData['authors'] ?? null,
            'citation_text' => $citationData['citation_text'] ?? null,
        ];
    }
}
