<?php

namespace App\Actions\Author;

use App\Actions\Project\UpdateProject;
use App\Models\Author;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SyncProjectAuthors
{
    public function __construct(private UpdateProject $updater) {}

    /**
     * Sync (create/update + attach) authors for the given project.
     *
     * @param  array<int, array<string,mixed>>  $authors
     * @return array<int, Author>
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(Project $project, array $authors): array
    {
        if (empty($authors)) {
            return [];
        }

        // Prevent N+1 on pivot lookups.
        $project->load('authors');

        $processed = [];
        foreach ($authors as $authorData) {
            $this->validateAuthorData($authorData);

            $familyName = $authorData['family_name'];
            $givenName = $authorData['given_name'];

            if ($familyName !== null && $givenName !== null) {
                $author = $this->findOrCreateAuthor($project, $authorData, $familyName, $givenName);
                $author->contributor_type = $authorData['contributor_type'] ?? 'Researcher';
                $processed[] = $author;
            }
        }

        DB::transaction(function () use ($project, $processed): void {
            $this->updater->attachAuthor($project, $processed);
        });

        $project->load('authors');

        return $processed;
    }

    /**
     * @param  array<string,mixed>  $authorData
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
     * @param  array<string,mixed>  $authorData
     */
    private function findOrCreateAuthor(Project $project, array $authorData, string $familyName, string $givenName): Author
    {
        $existing = Author::query()
            ->whereHas('projects', function ($query) use ($project): void {
                $query->where('projects.id', $project->id);
            })
            ->where('family_name', $familyName)
            ->where('given_name', $givenName)
            ->first();

        $attributes = $this->prepareAuthorAttributes($authorData, $familyName, $givenName);

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return Author::create($attributes);
    }

    /**
     * @param  array<string,mixed>  $authorData
     * @return array<string,mixed>
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
