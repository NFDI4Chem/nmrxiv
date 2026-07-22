<?php

namespace App\Actions\FundingReference;

use App\Models\FundingReference;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SyncFundingReferences
{
    public function __construct(
        private SyncFundingReferencePivot $fundingReferencePivot,
        private PushProjectDoiMetadata $pushProjectDoiMetadata,
    ) {}

    /**
     * Process and sync funding references for a project.
     *
     * @param  array<int, array<string, mixed>>  $fundingReferences
     * @return array<int, FundingReference>
     *
     * @throws ValidationException
     */
    public function sync(Project $project, array $fundingReferences, User $user): array
    {
        if ($fundingReferences === []) {
            return [];
        }

        $project->load('fundingReferences');

        $processedFundingReferences = [];

        foreach ($fundingReferences as $fundingReferenceData) {
            $this->validateFundingReferenceData($fundingReferenceData);

            $fundingReference = $this->findOrCreateFundingReference($project, $fundingReferenceData);
            $this->rememberFundingReference($project, $fundingReference);
            $processedFundingReferences[] = $fundingReference;
        }

        DB::transaction(function () use ($project, $processedFundingReferences, $user): void {
            $this->fundingReferencePivot->sync($project, $processedFundingReferences, $user);
        });

        $this->pushProjectDoiMetadata->push($project->fresh());

        return $processedFundingReferences;
    }

    /**
     * @param  array<string, mixed>  $fundingReferenceData
     *
     * @throws ValidationException
     */
    private function validateFundingReferenceData(array $fundingReferenceData): void
    {
        Validator::make($fundingReferenceData, [
            'funder_name' => ['required', 'string', 'max:255'],
            'funder_identifier' => ['nullable', 'string', 'max:255'],
            'funder_identifier_type' => ['nullable', 'string', 'in:ROR,Crossref Funder ID'],
            'award_number' => ['nullable', 'string', 'max:100'],
            'award_title' => ['nullable', 'string', 'max:500'],
            'award_uri' => ['nullable', 'url', 'max:500'],
        ])->after(function ($validator) use ($fundingReferenceData): void {
            $identifier = $this->normalizeText($fundingReferenceData['funder_identifier'] ?? null);
            $type = $this->normalizeText($fundingReferenceData['funder_identifier_type'] ?? null);

            if ($identifier !== null && $type === null) {
                $validator->errors()->add('funder_identifier_type', 'A funder identifier type is required when a funder identifier is provided.');
            }
        })->validate();
    }

    /**
     * @param  array<string, mixed>  $fundingReferenceData
     */
    private function findOrCreateFundingReference(Project $project, array $fundingReferenceData): FundingReference
    {
        if (! empty($fundingReferenceData['id'])) {
            $existingFundingReference = $this->findFundingReferenceByIdForProject(
                $project,
                (int) $fundingReferenceData['id']
            );

            if (! $existingFundingReference) {
                throw ValidationException::withMessages([
                    'funding_references' => ['The selected funding reference does not belong to this project.'],
                ]);
            }

            $existingFundingReference->update($this->prepareFundingReferenceAttributes($fundingReferenceData));

            return $existingFundingReference;
        }

        return FundingReference::create($this->prepareFundingReferenceAttributes($fundingReferenceData));
    }

    /**
     * @param  array<string, mixed>  $fundingReferenceData
     * @return array<string, mixed>
     */
    private function prepareFundingReferenceAttributes(array $fundingReferenceData): array
    {
        $funderIdentifier = $this->normalizeFunderIdentifier(
            $fundingReferenceData['funder_identifier'] ?? null,
            $fundingReferenceData['funder_identifier_type'] ?? null,
        );

        return [
            'funder_name' => $this->normalizeText($fundingReferenceData['funder_name'] ?? null),
            'funder_identifier' => $funderIdentifier,
            'funder_identifier_type' => $this->normalizeText($fundingReferenceData['funder_identifier_type'] ?? null),
            'award_number' => $this->normalizeText($fundingReferenceData['award_number'] ?? null),
            'award_title' => $this->normalizeText($fundingReferenceData['award_title'] ?? null),
            'award_uri' => $this->normalizeText($fundingReferenceData['award_uri'] ?? null),
        ];
    }

    private function rememberFundingReference(Project $project, FundingReference $fundingReference): void
    {
        $fundingReferences = $this->fundingReferenceCollection($project);

        if (! $fundingReferences->contains('id', $fundingReference->id)) {
            $project->setRelation('fundingReferences', $fundingReferences->push($fundingReference));
        }
    }

    private function findFundingReferenceByIdForProject(Project $project, int $id): ?FundingReference
    {
        return $this->fundingReferenceCollection($project)->firstWhere('id', $id)
            ?? $project->fundingReferences()->whereKey($id)->first();
    }

    /**
     * @return Collection<int, FundingReference>
     */
    private function fundingReferenceCollection(Project $project): Collection
    {
        return $project->fundingReferences;
    }

    private function normalizeFunderIdentifier(mixed $identifier, mixed $type): ?string
    {
        $normalizedIdentifier = $this->normalizeText($identifier);

        if ($normalizedIdentifier === null) {
            return null;
        }

        $normalizedType = $this->normalizeText($type);

        if ($normalizedType === 'ROR' && ! str_starts_with(strtolower($normalizedIdentifier), 'https://ror.org/')) {
            $rorId = ltrim($normalizedIdentifier, '/');

            return 'https://ror.org/'.$rorId;
        }

        return $normalizedIdentifier;
    }

    private function normalizeText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            return null;
        }

        return $normalizedValue;
    }
}
