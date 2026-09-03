<?php

declare(strict_types=1);

namespace App\Support\Public;

use App\Models\Molecule;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Support\Facades\DB;

/**
 * Write public-catalog membership and card badges onto molecules.
 */
final class PublicMoleculeCatalogIndexer
{
    /**
     * @param  list<int>|null  $moleculeIds  Null refreshes the full catalog.
     * @return array{indexed: int, cleared: int}
     */
    public function refresh(?array $moleculeIds = null, int $chunkSize = 100): array
    {
        $chunkSize = max(1, $chunkSize);

        if ($moleculeIds !== null) {
            $moleculeIds = array_values(array_unique(array_map(
                static fn (mixed $id): int => (int) $id,
                $moleculeIds,
            )));

            if ($moleculeIds === []) {
                PublicMoleculeAggregates::forgetPublicCatalogTotalCache();

                return ['indexed' => 0, 'cleared' => 0];
            }
        }

        $publicIds = $this->publicMoleculeIds($moleculeIds);
        $publicLookup = array_flip($publicIds);

        $previouslyPublic = Molecule::query()
            ->where('has_public_spectra', true)
            ->when($moleculeIds !== null, fn ($query) => $query->whereIn('id', $moleculeIds))
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        $toClear = array_values(array_filter(
            $previouslyPublic,
            static fn (int $id): bool => ! isset($publicLookup[$id]),
        ));

        $this->clear($toClear);

        $indexed = 0;
        foreach (array_chunk($publicIds, $chunkSize) as $chunk) {
            $this->writeChunk($chunk);
            $indexed += count($chunk);
        }

        PublicMoleculeAggregates::forgetPublicCatalogTotalCache();

        return [
            'indexed' => $indexed,
            'cleared' => count($toClear),
        ];
    }

    /**
     * @return array{indexed: int, cleared: int}
     */
    public function refreshForProject(Project $project): array
    {
        return $this->refresh($this->moleculeIdsForProject($project->id));
    }

    /**
     * @return array{indexed: int, cleared: int}
     */
    public function refreshForStudy(Study $study): array
    {
        return $this->refresh($this->moleculeIdsForStudy($study->id));
    }

    /**
     * @param  list<int>|null  $moleculeIds
     * @return list<int>
     */
    private function publicMoleculeIds(?array $moleculeIds): array
    {
        $exists = PublicMoleculeAggregates::hasPublicSpectraExistsSql('molecules.id');
        $sql = "SELECT molecules.id FROM molecules WHERE molecules.identifier IS NOT NULL AND {$exists}";
        $bindings = [];

        if ($moleculeIds !== null) {
            $placeholders = implode(',', array_fill(0, count($moleculeIds), '?'));
            $sql .= " AND molecules.id IN ({$placeholders})";
            $bindings = $moleculeIds;
        }

        return array_map(
            static fn (object $row): int => (int) $row->id,
            DB::select($sql, $bindings),
        );
    }

    /**
     * @param  list<int>  $moleculeIds
     */
    private function writeChunk(array $moleculeIds): void
    {
        $payload = PublicMoleculeAggregates::catalogCardPayload($moleculeIds);
        $indexedAt = now();

        foreach (Molecule::query()->whereIn('id', $moleculeIds)->get() as $molecule) {
            $id = (int) $molecule->id;

            $molecule->forceFill([
                'has_public_spectra' => true,
                'public_samples_count' => $payload['sample_counts'][$id] ?? 0,
                'public_experiment_type_counts' => $payload['experiment_counts'][$id] ?? [],
                'public_catalog_indexed_at' => $indexedAt,
            ])->saveQuietly();
        }
    }

    /**
     * @param  list<int>  $moleculeIds
     */
    private function clear(array $moleculeIds): void
    {
        if ($moleculeIds === []) {
            return;
        }

        $indexedAt = now();

        foreach (array_chunk($moleculeIds, 500) as $chunk) {
            foreach (Molecule::query()->whereIn('id', $chunk)->get() as $molecule) {
                $molecule->forceFill([
                    'has_public_spectra' => false,
                    'public_samples_count' => 0,
                    'public_experiment_type_counts' => [],
                    'public_catalog_indexed_at' => $indexedAt,
                ])->saveQuietly();
            }
        }
    }

    /**
     * @return list<int>
     */
    private function moleculeIdsForProject(int $projectId): array
    {
        return DB::table('molecule_sample')
            ->join('samples', 'samples.id', '=', 'molecule_sample.sample_id')
            ->join('studies', 'studies.id', '=', 'samples.study_id')
            ->where('studies.project_id', $projectId)
            ->distinct()
            ->pluck('molecule_sample.molecule_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * @return list<int>
     */
    private function moleculeIdsForStudy(int $studyId): array
    {
        return DB::table('molecule_sample')
            ->join('samples', 'samples.id', '=', 'molecule_sample.sample_id')
            ->where('samples.study_id', $studyId)
            ->distinct()
            ->pluck('molecule_sample.molecule_id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }
}
