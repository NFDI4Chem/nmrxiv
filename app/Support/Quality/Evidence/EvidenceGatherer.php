<?php

declare(strict_types=1);

namespace App\Support\Quality\Evidence;

use App\Models\Team;
use App\Support\Quality\MoleculeEvidence;
use App\Support\Quality\SpectrumDescriptor;

/**
 * Runs configured collectors and merges their fragments into MoleculeEvidence.
 */
final class EvidenceGatherer
{
    /**
     * @param  list<class-string<EvidenceCollector>>|null  $collectorClasses
     */
    public function __construct(
        private readonly ?array $collectorClasses = null,
    ) {}

    /**
     * @param  list<int>  $moleculeIds
     * @return array<int, MoleculeEvidence>
     */
    public function gather(array $moleculeIds, ?Team $team = null): array
    {
        if ($moleculeIds === []) {
            return [];
        }

        /** @var array<int, array{spectra: list<SpectrumDescriptor>, has_assignments: bool, extra: array<string, mixed>}> $merged */
        $merged = [];
        foreach ($moleculeIds as $id) {
            $merged[(int) $id] = [
                'spectra' => [],
                'has_assignments' => false,
                'extra' => [],
            ];
        }

        foreach ($this->collectors() as $collector) {
            foreach ($collector->collect($moleculeIds, $team) as $moleculeId => $fragment) {
                $moleculeId = (int) $moleculeId;
                if (! isset($merged[$moleculeId])) {
                    continue;
                }

                if (isset($fragment['spectra']) && is_array($fragment['spectra'])) {
                    foreach ($fragment['spectra'] as $spectrum) {
                        if ($spectrum instanceof SpectrumDescriptor) {
                            $merged[$moleculeId]['spectra'][] = $spectrum;
                        }
                    }
                }

                if (array_key_exists('has_assignments', $fragment)) {
                    $merged[$moleculeId]['has_assignments'] = (bool) $fragment['has_assignments']
                        || $merged[$moleculeId]['has_assignments'];
                }

                if (isset($fragment['extra']) && is_array($fragment['extra'])) {
                    $merged[$moleculeId]['extra'] = array_merge(
                        $merged[$moleculeId]['extra'],
                        $fragment['extra'],
                    );
                }
            }
        }

        $result = [];
        foreach ($merged as $moleculeId => $data) {
            $result[$moleculeId] = new MoleculeEvidence(
                moleculeId: $moleculeId,
                spectra: $data['spectra'],
                hasAssignments: $data['has_assignments'],
                extra: $data['extra'],
            );
        }

        return $result;
    }

    /**
     * @return list<EvidenceCollector>
     */
    private function collectors(): array
    {
        $classes = $this->collectorClasses ?? config('quality.collectors', []);
        $collectors = [];
        foreach ($classes as $class) {
            if (! is_string($class) || ! class_exists($class)) {
                continue;
            }
            $instance = app($class);
            if ($instance instanceof EvidenceCollector) {
                $collectors[] = $instance;
            }
        }

        return $collectors;
    }
}
