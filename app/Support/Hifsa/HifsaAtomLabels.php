<?php

namespace App\Support\Hifsa;

/**
 * Parse Cosmic Truth / HiFSA atom labels (H14, C10,C11) and pair coupling endpoints.
 */
class HifsaAtomLabels
{
    /**
     * Split a Cosmic Truth atom name into individual atom descriptors.
     *
     * @return list<array{element: string, serial: int, suffix: ?string, raw: string}>
     */
    public static function parseGroup(?string $name): array
    {
        if ($name === null || trim($name) === '') {
            return [];
        }

        $parts = preg_split('/\s*,\s*/', trim($name)) ?: [];
        $atoms = [];

        foreach ($parts as $part) {
            $parsed = self::parseAtom($part);

            if ($parsed !== null) {
                $atoms[] = $parsed;
            }
        }

        return $atoms;
    }

    /**
     * Parse a single Cosmic Truth atom label such as H14, C10, or H14a.
     *
     * @return array{element: string, serial: int, suffix: ?string, raw: string}|null
     */
    public static function parseAtom(?string $label): ?array
    {
        if ($label === null) {
            return null;
        }

        $raw = trim($label);

        if ($raw === '') {
            return null;
        }

        if (! preg_match('/^([A-Za-z]{1,2})(\d+)([A-Za-z]?)$/', $raw, $matches)) {
            return null;
        }

        return [
            'element' => strtoupper($matches[1]),
            'serial' => (int) $matches[2],
            'suffix' => $matches[3] !== '' ? strtolower($matches[3]) : null,
            'raw' => $raw,
        ];
    }

    /**
     * Pair from/to Cosmic Truth groups for coupling arrows.
     * Unequal lengths zip only min(count) pairs (no clamping onto the last atom).
     *
     * @return list<array{from: array{element: string, serial: int, suffix: ?string, raw: string}, to: array{element: string, serial: int, suffix: ?string, raw: string}}>
     */
    public static function pairCoupling(?string $from, ?string $to): array
    {
        $fromAtoms = self::parseGroup($from);
        $toAtoms = self::parseGroup($to);

        if ($fromAtoms === [] || $toAtoms === []) {
            return [];
        }

        // Geminal / same-group couplings like H28,H29 → H28,H29 should connect
        // the two partners, not zip identical endpoints onto themselves.
        $identicalGroups =
            count($fromAtoms) === count($toAtoms)
            && count($fromAtoms) >= 2;

        if ($identicalGroups) {
            foreach ($fromAtoms as $index => $atom) {
                if (
                    ($atom['raw'] ?? null) !== ($toAtoms[$index]['raw'] ?? null)
                    || ($atom['serial'] ?? null) !== ($toAtoms[$index]['serial'] ?? null)
                    || ($atom['element'] ?? null) !== ($toAtoms[$index]['element'] ?? null)
                ) {
                    $identicalGroups = false;
                    break;
                }
            }
        }

        if ($identicalGroups) {
            $pairs = [];

            for ($i = 0; $i < count($fromAtoms) - 1; $i++) {
                $pairs[] = [
                    'from' => $fromAtoms[$i],
                    'to' => $fromAtoms[$i + 1],
                ];
            }

            return $pairs;
        }

        $count = min(count($fromAtoms), count($toAtoms));
        $pairs = [];

        for ($i = 0; $i < $count; $i++) {
            $pairs[] = [
                'from' => $fromAtoms[$i],
                'to' => $toAtoms[$i],
            ];
        }

        return $pairs;
    }

    /**
     * Pick a study molecule only when InChIKey matches the spin system.
     * Never fall back to "first SDF" (wrong enantiomer / compound risk).
     *
     * @param  iterable<int, object|array<string, mixed>>  $molecules
     * @param  array<string, mixed>|null  $spinSystem
     * @return array<string, mixed>|object|null
     */
    public static function resolveMolecule(iterable $molecules, ?array $spinSystem = null): mixed
    {
        $list = [];

        foreach ($molecules as $molecule) {
            $list[] = $molecule;
        }

        if ($list === []) {
            return null;
        }

        $inchiKey = is_array($spinSystem)
            ? ($spinSystem['inchi_key'] ?? null)
            : null;

        if (! is_string($inchiKey) || $inchiKey === '') {
            return null;
        }

        foreach ($list as $molecule) {
            $candidate = self::moleculeValue($molecule, 'inchi_key')
                ?? self::moleculeValue($molecule, 'standard_inchi_key');

            if (is_string($candidate) && strcasecmp($candidate, $inchiKey) === 0) {
                $sdf = self::moleculeValue($molecule, 'sdf');

                if (is_string($sdf) && trim($sdf) !== '') {
                    return $molecule;
                }
            }
        }

        return null;
    }

    /**
     * @param  object|array<string, mixed>  $molecule
     */
    private static function moleculeValue(object|array $molecule, string $key): mixed
    {
        if (is_array($molecule)) {
            return $molecule[$key] ?? null;
        }

        return $molecule->{$key} ?? null;
    }
}
