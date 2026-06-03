<?php

namespace App\Support\Nmr;

use App\Models\Study;

/**
 * Detects NMRium spectra whose per-spectrum {@code info} block is empty or corrupt.
 */
class NmriumSpectraInfoInspector
{
    /**
     * @param  array<int, array<string, mixed>>  $spectra
     */
    public static function needsRefresh(array $spectra): bool
    {
        foreach ($spectra as $spec) {
            $info = $spec['info'] ?? null;
            if (! is_array($info) || empty($info)) {
                return true;
            }
            if (isset($info['im']) || isset($info['re'])) {
                return true;
            }
            if (! array_key_exists('experiment', $info) && ! array_key_exists('nucleus', $info)) {
                return true;
            }
        }

        return false;
    }

    public static function studyNeedsRefresh(Study $study): bool
    {
        $nmrium = $study->nmrium;
        if (! $nmrium) {
            return false;
        }

        $payload = $nmrium->nmrium_info ?? [];
        $spectra = $payload['data']['spectra'] ?? [];

        if (! is_array($spectra) || $spectra === []) {
            return false;
        }

        return self::needsRefresh($spectra);
    }

    /**
     * @return array{0: int|null, 1: int} study id (if any) and total count needing refresh
     */
    public static function firstStudyNeedingRefresh(): array
    {
        $count = 0;
        $firstId = null;

        Study::query()
            ->whereHas('nmrium')
            ->select(['id'])
            ->orderBy('id')
            ->with('nmrium')
            ->chunkById(100, function ($studies) use (&$count, &$firstId) {
                foreach ($studies as $study) {
                    if (! self::studyNeedsRefresh($study)) {
                        continue;
                    }

                    $count++;
                    if ($firstId === null) {
                        $firstId = $study->id;
                    }
                }
            });

        return [$firstId, $count];
    }
}
