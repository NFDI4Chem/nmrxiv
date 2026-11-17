<?php

namespace App\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;

/**
 * Structure plus assigned 1H/13C shifts, in the shape NMRKit's
 * `POST /validate/assignments` expects. Atom numbers are 1-based indices of
 * `$molfile`; for 1H they may name the carrying heavy atom.
 */
final readonly class AssignmentSet
{
    public const NUCLEI = ['13C', '1H'];

    /**
     * @param  list<array{nucleus: string, atoms: list<int>, shift: float, label?: string, multiplicity?: string, n_h?: int, diastereotopic?: string}>  $assignments
     * @param  list<array{nucleus: string, shift: float, kind: string}>  $unassignedPeaks
     */
    public function __construct(
        public string $molfile,
        public AssignmentSource $source,
        public array $assignments,
        public ?string $solvent = null,
        public array $unassignedPeaks = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload, AssignmentSource $source): self
    {
        return new self(
            molfile: (string) ($payload['structure']['molfile'] ?? ''),
            source: $source,
            assignments: array_values($payload['assignments'] ?? []),
            solvent: $payload['conditions']['solvent'] ?? null,
            unassignedPeaks: array_values($payload['unassigned_peaks'] ?? []),
        );
    }

    /**
     * @return array{structure: array{molfile: string, source: string}, assignments: list<array<string, mixed>>, conditions?: array{solvent: string}, unassigned_peaks?: list<array<string, mixed>>}
     */
    public function toPayload(): array
    {
        $payload = [
            'structure' => ['molfile' => $this->molfile, 'source' => $this->source->value],
            'assignments' => $this->assignments,
        ];

        if ($this->solvent !== null && $this->solvent !== '') {
            $payload['conditions'] = ['solvent' => $this->solvent];
        }

        if ($this->unassignedPeaks !== []) {
            $payload['unassigned_peaks'] = $this->unassignedPeaks;
        }

        return $payload;
    }

    public function hash(): string
    {
        return hash('sha256', json_encode($this->toPayload(), JSON_PRESERVE_ZERO_FRACTION));
    }

    public function assignedCount(): int
    {
        return count(array_filter($this->assignments, fn (array $row): bool => $row['atoms'] !== []));
    }

    /**
     * @return list<string>
     */
    public function nuclei(): array
    {
        $present = array_unique(array_column($this->assignments, 'nucleus'));

        return array_values(array_intersect(self::NUCLEI, $present));
    }
}
