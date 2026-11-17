<?php

namespace App\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;

/**
 * Reads NMReDATA SD files: `NMREDATA_ASSIGNMENT` lines ("label, shift,
 * atoms...", where "H3" means the hydrogens on atom 3) and the
 * `NMREDATA_1D_1H` / `NMREDATA_1D_13C` signal lists that tie labels to a
 * nucleus and carry multiplicity (S=) and proton count (N=).
 */
final class NmredataAssignmentReader
{
    public static function supports(SdfRecord $record): bool
    {
        return $record->tag('NMREDATA_ASSIGNMENT') !== null;
    }

    public function read(SdfRecord $record): AssignmentSet
    {
        $symbols = $record->atomSymbols();
        $signals = $this->signalsByLabel($record);
        $assignments = [];

        foreach ($record->tagLines('NMREDATA_ASSIGNMENT') as $line) {
            $parts = array_values(array_filter(array_map('trim', explode(',', $line)), fn (string $part): bool => $part !== ''));
            if (count($parts) < 3 || ! is_numeric($parts[1])) {
                continue;
            }

            [$label, $shift] = [$parts[0], (float) $parts[1]];
            $tokens = array_slice($parts, 2);
            $nucleus = $signals[$label]['nucleus'] ?? $this->inferNucleus($tokens, $symbols);
            if ($nucleus === null) {
                continue;
            }

            $atoms = [];
            foreach ($tokens as $token) {
                if (preg_match('/^H?(\d+)$/i', $token, $match) === 1) {
                    $atoms[] = (int) $match[1];
                }
            }
            $atoms = array_values(array_unique($atoms));
            sort($atoms);
            if ($atoms === []) {
                continue;
            }

            $row = ['nucleus' => $nucleus, 'atoms' => $atoms, 'label' => $label, 'shift' => round($shift, 4)];
            if (isset($signals[$label]['multiplicity'])) {
                $row['multiplicity'] = $signals[$label]['multiplicity'];
            }
            if ($nucleus === '1H' && isset($signals[$label]['n_h'])) {
                $row['n_h'] = $signals[$label]['n_h'];
            }
            $assignments[] = $row;
        }

        if ($assignments === []) {
            throw new InvalidAssignmentFileException('The NMReDATA file contains no 1H or 13C assignments.');
        }

        $solvent = $record->tag('NMREDATA_SOLVENT');

        return new AssignmentSet(
            molfile: $record->molfile,
            source: AssignmentSource::Nmredata,
            assignments: $assignments,
            solvent: $solvent !== null ? rtrim(trim($solvent), '\\') : null,
        );
    }

    /**
     * @return array<string, array{nucleus: string, multiplicity?: string, n_h?: int}>
     */
    private function signalsByLabel(SdfRecord $record): array
    {
        $signals = [];

        foreach (array_keys($record->tags) as $tag) {
            if (preg_match('/^NMREDATA_1D_(1H|13C)(?![0-9])/', $tag, $match) !== 1) {
                continue;
            }

            foreach ($record->tagLines($tag) as $line) {
                $fields = [];
                foreach (array_map('trim', explode(',', $line)) as $part) {
                    if (preg_match('/^([A-Za-z]+)=(.*)$/', $part, $field) === 1) {
                        $fields[strtoupper($field[1])] = trim($field[2]);
                    }
                }
                if (! isset($fields['L'])) {
                    continue;
                }

                $signal = ['nucleus' => $match[1]];
                if (($fields['S'] ?? '') !== '') {
                    $signal['multiplicity'] = $fields['S'];
                }
                if (isset($fields['N']) && ctype_digit($fields['N'])) {
                    $signal['n_h'] = (int) $fields['N'];
                }
                $signals[$fields['L']] = $signal;
            }
        }

        return $signals;
    }

    /**
     * @param  list<string>  $tokens
     * @param  array<int, string>  $symbols
     */
    private function inferNucleus(array $tokens, array $symbols): ?string
    {
        foreach ($tokens as $token) {
            if (str_starts_with(strtoupper($token), 'H')) {
                return '1H';
            }
        }

        $first = ctype_digit($tokens[0] ?? '') ? ($symbols[(int) $tokens[0]] ?? null) : null;

        return match ($first) {
            'H' => '1H',
            'C' => '13C',
            default => null,
        };
    }
}
