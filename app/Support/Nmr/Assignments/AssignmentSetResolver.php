<?php

namespace App\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;
use App\Models\Study;
use Illuminate\Http\UploadedFile;

/**
 * Builds an AssignmentSet from whatever the author has: assignments drawn in
 * the sample's NMRium workspace, an uploaded Mnova/NMReDATA SD file, or rows
 * typed against a structure.
 */
final class AssignmentSetResolver
{
    public function __construct(
        private NmriumAssignmentReader $nmriumReader,
        private MnovaSdfAssignmentReader $mnovaReader,
        private NmredataAssignmentReader $nmredataReader,
    ) {}

    /**
     * @param  array{source: string, molfile?: string|null, solvent?: string|null, assignments?: list<array<string, mixed>>|null}  $validated
     *
     * @throws InvalidAssignmentFileException
     */
    public function fromValidatedInput(array $validated, ?UploadedFile $file, ?Study $study = null): ?AssignmentSet
    {
        $set = match ($validated['source']) {
            'nmrium' => $study !== null ? $this->fromStudyNmrium($study) : null,
            'file' => $this->fromFile((string) $file?->get()),
            default => $this->fromManual((string) $validated['molfile'], $validated['assignments'] ?? []),
        };

        $solvent = $validated['solvent'] ?? null;
        if ($set !== null && is_string($solvent) && trim($solvent) !== '') {
            return new AssignmentSet($set->molfile, $set->source, $set->assignments, trim($solvent), $set->unassignedPeaks);
        }

        return $set;
    }

    public function fromStudyNmrium(Study $study): ?AssignmentSet
    {
        $info = $study->nmrium?->nmrium_info;
        if (is_string($info)) {
            $info = json_decode($info, true);
        }

        return is_array($info) ? $this->nmriumReader->read($info) : null;
    }

    public function fromFile(string $contents): AssignmentSet
    {
        $record = SdfRecord::parse($contents);

        if (NmredataAssignmentReader::supports($record)) {
            return $this->nmredataReader->read($record);
        }
        if (MnovaSdfAssignmentReader::supports($record)) {
            return $this->mnovaReader->read($record);
        }

        throw new InvalidAssignmentFileException(
            'No assigned shifts found in this file. Export the SD file from Mnova with the assignments, or use an NMReDATA file.'
        );
    }

    /**
     * @param  list<array{nucleus: string, atoms?: list<int|string>, shift: float|string, label?: string|null, multiplicity?: string|null, n_h?: int|string|null}>  $rows
     */
    public function fromManual(string $molfile, array $rows, ?string $solvent = null): AssignmentSet
    {
        $assignments = array_map(function (array $row): array {
            $assignment = [
                'nucleus' => $row['nucleus'],
                'atoms' => array_values(array_map('intval', $row['atoms'] ?? [])),
                'shift' => round((float) $row['shift'], 4),
            ];
            foreach (['label', 'multiplicity'] as $key) {
                if (is_string($row[$key] ?? null) && trim($row[$key]) !== '') {
                    $assignment[$key] = trim($row[$key]);
                }
            }
            if (isset($row['n_h']) && $row['n_h'] !== '') {
                $assignment['n_h'] = (int) $row['n_h'];
            }

            return $assignment;
        }, $rows);

        return new AssignmentSet(
            molfile: $molfile,
            source: AssignmentSource::Manual,
            assignments: array_values($assignments),
            solvent: $solvent,
        );
    }
}
