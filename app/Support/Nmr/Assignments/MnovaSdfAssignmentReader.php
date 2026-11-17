<?php

namespace App\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;

/**
 * Reads the SD file Mnova writes with "Export assignments": one
 * `CHEMICAL_SHIFTS.<nucleus>` item per nucleus with `atom,shift` lines, and
 * the author's atom numbering in `M  ZZC` lines.
 */
final class MnovaSdfAssignmentReader
{
    private const TAGS = ['13C' => 'CHEMICAL_SHIFTS.13C', '1H' => 'CHEMICAL_SHIFTS.1H'];

    public static function supports(SdfRecord $record): bool
    {
        return $record->tag(self::TAGS['13C']) !== null || $record->tag(self::TAGS['1H']) !== null;
    }

    public function read(SdfRecord $record): AssignmentSet
    {
        $labels = $record->atomLabels();
        $assignments = [];

        foreach (self::TAGS as $nucleus => $tag) {
            $groups = [];
            foreach ($record->tagLines($tag) as $line) {
                $parts = array_map('trim', explode(',', $line));
                if (count($parts) < 2 || ! ctype_digit($parts[0]) || ! is_numeric($parts[1])) {
                    continue;
                }
                $shift = round((float) $parts[1], 4);
                $groups[sprintf('%.4f', $shift)]['shift'] = $shift;
                $groups[sprintf('%.4f', $shift)]['atoms'][] = (int) $parts[0];
            }

            usort($groups, fn (array $a, array $b): int => $b['shift'] <=> $a['shift']);
            $rows = array_map(fn (array $group): array => $this->row($nucleus, $group, $labels), $groups);

            array_push($assignments, ...($nucleus === '1H' ? $this->markDiastereotopicPairs($rows) : $rows));
        }

        if ($assignments === []) {
            throw new InvalidAssignmentFileException('The Mnova export contains no chemical shifts.');
        }

        return new AssignmentSet(
            molfile: $record->molfile,
            source: AssignmentSource::MnovaSdf,
            assignments: $assignments,
            solvent: $record->tag('SOLVENT') !== null ? trim($record->tag('SOLVENT')) : null,
        );
    }

    /**
     * @param  array{shift: float, atoms: list<int>}  $group
     * @param  array<int, string>  $labels
     * @return array{nucleus: string, atoms: list<int>, shift: float, label?: string}
     */
    private function row(string $nucleus, array $group, array $labels): array
    {
        $atoms = array_values(array_unique($group['atoms']));
        sort($atoms);
        $row = ['nucleus' => $nucleus, 'atoms' => $atoms, 'shift' => $group['shift']];

        $names = array_map(fn (int $atom): ?string => $labels[$atom] ?? null, $atoms);
        if (! in_array(null, $names, true)) {
            $prefix = $nucleus === '13C' ? 'C-' : 'H-';
            $row['label'] = implode(', ', array_map(fn (string $name): string => $prefix.$name, $names));
        }

        return $row;
    }

    /**
     * A single heavy atom listed with two different 1H shifts is a CH2 whose
     * protons are diastereotopic.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function markDiastereotopicPairs(array $rows): array
    {
        $byAtom = [];
        foreach ($rows as $index => $row) {
            if (count($row['atoms']) === 1) {
                $byAtom[$row['atoms'][0]][] = $index;
            }
        }

        foreach ($byAtom as $indices) {
            if (count($indices) !== 2) {
                continue;
            }
            usort($indices, fn (int $a, int $b): int => $rows[$a]['shift'] <=> $rows[$b]['shift']);
            foreach (['a', 'b'] as $position => $suffix) {
                $rows[$indices[$position]]['diastereotopic'] = $suffix;
                if (isset($rows[$indices[$position]]['label'])) {
                    $rows[$indices[$position]]['label'] .= $suffix;
                }
            }
        }

        return $rows;
    }
}
