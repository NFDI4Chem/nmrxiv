<?php

namespace App\Services;

use App\Actions\Project\UpdateProject;
use App\Models\Author;
use App\Models\Project;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthorService
{
    /**
     * Create a new AuthorService instance.
     *
     * @return void
     */
    public function __construct(
        private UpdateProject $updater
    ) {}

    /**
     * Process and sync authors for a project.
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function syncAuthors(Project $project, array $authors): array
    {
        if (empty($authors)) {
            return [];
        }

        // Eager load existing authors to prevent N+1 queries
        $project->load('authors');

        $processedAuthors = [];

        foreach ($authors as $authorData) {
            $this->validateAuthorData($authorData);

            $familyName = $authorData['family_name'];
            $givenName = $authorData['given_name'];

            if (! is_null($familyName) && ! is_null($givenName)) {
                $author = $this->findOrCreateAuthor($project, $authorData, $familyName, $givenName);
                $author->contributor_type = $authorData['contributor_type'] ?? 'Researcher';
                $processedAuthors[] = $author;
            }
        }

        // Use database transaction for bulk operations
        DB::transaction(function () use ($project, $processedAuthors): void {
            $this->updater->attachAuthor($project, $processedAuthors);
        });

        // Reload the relationship to get fresh pivot data
        $project->load('authors');

        return $processedAuthors;
    }

    /**
     * Remove author from project.
     */
    public function removeAuthorFromProject(Project $project, int $authorId): void
    {
        DB::transaction(function () use ($project, $authorId): void {
            $this->updater->detachAuthor($project, $authorId);
        });
    }

    /**
     * Update contributor type for an author in a project.
     */
    public function updateContributorType(Project $project, int $authorId, string $role): bool
    {
        $contributorTypes = Config::get('doi.'.Config::get('doi.default').'.contributor_types');

        if (! in_array($role, $contributorTypes)) {
            return false;
        }

        $this->updater->updateContributorType($project, $authorId, $role);

        return true;
    }

    /**
     * Validate author data against validation rules.
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateAuthorData(array $authorData): void
    {
        Validator::make($authorData, [
            'given_name' => ['required', 'string', 'max:255'],
            'family_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:100'],
            'email_id' => ['nullable', 'email', 'max:320'],
            'orcid_id' => ['nullable', 'string', 'max:19'],
            'affiliation' => ['nullable', 'string', 'max:500'],
            'contributor_type' => ['nullable', 'string', 'max:50'],
        ])->validate();
    }

    /**
     * Find existing author for project or create new one.
     */
    private function findOrCreateAuthor(Project $project, array $authorData, string $familyName, string $givenName): Author
    {
        $existingAuthor = Author::query()
            ->whereHas('projects', function ($query) use ($project): void {
                $query->where('projects.id', $project->id);
            })
            ->where('family_name', $familyName)
            ->where('given_name', $givenName)
            ->first();

        if ($existingAuthor) {
            $existingAuthor->update($this->prepareAuthorAttributes($authorData, $familyName, $givenName));

            return $existingAuthor;
        }

        return Author::create($this->prepareAuthorAttributes($authorData, $familyName, $givenName));
    }

    /**
     * Prepare author attributes for create or update operations.
     */
    private function prepareAuthorAttributes(array $authorData, string $familyName, string $givenName): array
    {
        return [
            'title' => $authorData['title'] ?? null,
            'given_name' => $givenName,
            'family_name' => $familyName,
            'orcid_id' => $authorData['orcid_id'] ?? null,
            'email_id' => $authorData['email_id'] ?? null,
            'affiliation' => $authorData['affiliation'] ?? null,
        ];
    }
}
