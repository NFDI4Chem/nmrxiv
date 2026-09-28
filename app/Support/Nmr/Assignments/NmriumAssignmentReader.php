<?php

namespace App\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;

/**
 * Reads assignments made in NMRium: `ranges.values[].signals[].atoms` of 1D
 * 1H/13C spectra, as 0-based indices into the first NMRium molecule. For 1H
 * these point at the implicit hydrogens openchemlib appends after the heavy
 * atoms, which NMRKit resolves to their carbons.
 */
final class NmriumAssignmentReader
{
    private const UNASSIGNED_KINDS = ['solvent' => 'solvent', 'impurity' => 'impurity'];

    /**
     * @param  array<string, mixed>  $nmriumInfo
     */
    public function read(array $nmriumInfo): ?AssignmentSet
    {
        $data = is_array($nmriumInfo['data'] ?? null) ? $nmriumInfo['data'] : [];
        $spectra = $data['spectra'] ?? $nmriumInfo['spectra'] ?? [];
        $molecules = $data['molecules'] ?? $nmriumInfo['molecules'] ?? [];

        $molfile = null;
        foreach ((array) $molecules as $molecule) {
            if (is_array($molecule) && is_string($molecule['molfile'] ?? null) && trim($molecule['molfile']) !== '') {
                $molfile = $molecule['molfile'];
                break;
            }
        }
        if ($molfile === null) {
            return null;
        }

        $best = [];
        $solvent = null;
        foreach ((array) $spectra as $spectrum) {
            $parsed = is_array($spectrum) ? $this->readSpectrum($spectrum) : null;
            if ($parsed === null || $parsed['assignments'] === []) {
                continue;
            }

            $nucleus = $parsed['nucleus'];
            if (! isset($best[$nucleus]) || count($parsed['assignments']) > count($best[$nucleus]['assignments'])) {
                $best[$nucleus] = $parsed;
            }
            $solvent ??= $parsed['solvent'];
        }

        $assignments = [];
        $unassigned = [];
        foreach (AssignmentSet::NUCLEI as $nucleus) {
            if (isset($best[$nucleus])) {
                array_push($assignments, ...$best[$nucleus]['assignments']);
                array_push($unassigned, ...$best[$nucleus]['unassigned']);
            }
        }

        if ($assignments === []) {
            return null;
        }

        return new AssignmentSet(
            molfile: $molfile,
            source: AssignmentSource::Nmrium,
            assignments: $assignments,
            solvent: $solvent,
            unassignedPeaks: $unassigned,
        );
    }

    /**
     * @param  array<string, mixed>  $spectrum
     * @return array{nucleus: string, solvent: ?string, assignments: list<array<string, mixed>>, unassigned: list<array<string, mixed>>}|null
     */
    private function readSpectrum(array $spectrum): ?array
    {
        $info = is_array($spectrum['info'] ?? null) ? $spectrum['info'] : [];
        if ((int) ($info['dimension'] ?? 1) !== 1) {
            return null;
        }

        $nucleus = is_array($info['nucleus'] ?? null) ? ($info['nucleus'][0] ?? null) : ($info['nucleus'] ?? null);
        if (! in_array($nucleus, AssignmentSet::NUCLEI, true)) {
            return null;
        }

        $assignments = [];
        $unassigned = [];
        foreach ($spectrum['ranges']['values'] ?? [] as $range) {
            foreach ($range['signals'] ?? [] as $signal) {
                if (! is_numeric($signal['delta'] ?? null)) {
                    continue;
                }

                $shift = round((float) $signal['delta'], 4);
                $kind = $signal['kind'] ?? $range['kind'] ?? 'signal';
                $atoms = array_values(array_unique(array_map(
                    fn ($atom): int => (int) $atom + 1,
                    array_filter($signal['atoms'] ?? [], 'is_numeric'),
                )));
                sort($atoms);

                if (isset(self::UNASSIGNED_KINDS[$kind])) {
                    $unassigned[] = ['nucleus' => $nucleus, 'shift' => $shift, 'kind' => self::UNASSIGNED_KINDS[$kind]];
                } elseif ($kind === 'signal' && $atoms === []) {
                    $unassigned[] = ['nucleus' => $nucleus, 'shift' => $shift, 'kind' => 'unknown'];
                } elseif ($kind === 'signal') {
                    $row = ['nucleus' => $nucleus, 'atoms' => $atoms, 'shift' => $shift];
                    if (is_string($signal['multiplicity'] ?? null) && $signal['multiplicity'] !== '') {
                        $row['multiplicity'] = $signal['multiplicity'];
                    }
                    $assignments[] = $row;
                }
            }
        }

        return [
            'nucleus' => $nucleus,
            'solvent' => is_string($info['solvent'] ?? null) && $info['solvent'] !== '' ? $info['solvent'] : null,
            'assignments' => $assignments,
            'unassigned' => $unassigned,
        ];
    }
}
