<?php

namespace Tests\Unit\Support\Nmr\Assignments;

use App\Enums\AssignmentSource;
use App\Support\Nmr\Assignments\NmriumAssignmentReader;
use Tests\TestCase;

class NmriumAssignmentReaderTest extends TestCase
{
    /**
     * Nicotine as NMRium stores it: signal atoms are 0-based; 1H signals point
     * at implicit hydrogens (index 21 is the H on C-2', molfile atom 6).
     *
     * @return array<string, mixed>
     */
    public static function nicotineState(): array
    {
        $molfile = file_get_contents(base_path('tests/Fixtures/Assignments/nicotine.mol'));
        $range = fn (float $delta, array $atoms, string $kind = 'signal'): array => [
            'kind' => $kind,
            'signals' => [['delta' => $delta, 'atoms' => $atoms, 'kind' => $kind, 'multiplicity' => 'm']],
        ];

        return [
            'version' => 7,
            'data' => [
                'molecules' => [['id' => 'nicotine', 'molfile' => $molfile]],
                'spectra' => [
                    [
                        'info' => ['nucleus' => '13C', 'dimension' => 1, 'solvent' => 'CDCl3'],
                        'ranges' => ['values' => [
                            $range(149.91, [11]),
                            $range(68.94, [5]),
                            $range(40.35, [0]),
                            $range(77.16, [], 'solvent'),
                        ]],
                    ],
                    [
                        'info' => ['nucleus' => '13C', 'dimension' => 1, 'experiment' => 'dept'],
                        'ranges' => ['values' => [$range(149.9, [11])]],
                    ],
                    [
                        'info' => ['nucleus' => '1H', 'dimension' => 1],
                        'ranges' => ['values' => [
                            $range(3.08, [21]),
                            $range(2.17, [12, 13, 14]),
                            $range(1.56, []),
                        ]],
                    ],
                    [
                        'info' => ['nucleus' => ['1H', '13C'], 'dimension' => 2],
                        'ranges' => ['values' => [$range(1.0, [1])]],
                    ],
                ],
            ],
        ];
    }

    public function test_reads_signal_atoms_as_one_based_indices_of_the_nmrium_molecule(): void
    {
        $set = (new NmriumAssignmentReader)->read(self::nicotineState());

        $this->assertNotNull($set);
        $this->assertSame(AssignmentSource::Nmrium, $set->source);
        $this->assertSame('CDCl3', $set->solvent);
        $this->assertStringContainsString('V2000', $set->molfile);
        $this->assertSame(
            [
                ['nucleus' => '13C', 'atoms' => [12], 'shift' => 149.91, 'multiplicity' => 'm'],
                ['nucleus' => '13C', 'atoms' => [6], 'shift' => 68.94, 'multiplicity' => 'm'],
                ['nucleus' => '13C', 'atoms' => [1], 'shift' => 40.35, 'multiplicity' => 'm'],
                ['nucleus' => '1H', 'atoms' => [22], 'shift' => 3.08, 'multiplicity' => 'm'],
                ['nucleus' => '1H', 'atoms' => [13, 14, 15], 'shift' => 2.17, 'multiplicity' => 'm'],
            ],
            $set->assignments
        );
    }

    public function test_solvent_and_unassigned_signals_join_the_fit_only_as_unassigned_peaks(): void
    {
        $set = (new NmriumAssignmentReader)->read(self::nicotineState());

        $this->assertSame(
            [
                ['nucleus' => '13C', 'shift' => 77.16, 'kind' => 'solvent'],
                ['nucleus' => '1H', 'shift' => 1.56, 'kind' => 'unknown'],
            ],
            $set->unassignedPeaks
        );
    }

    public function test_returns_null_without_a_molecule_or_without_assigned_signals(): void
    {
        $state = self::nicotineState();
        $withoutMolecule = $state;
        $withoutMolecule['data']['molecules'] = [];

        $unassigned = $state;
        foreach ($unassigned['data']['spectra'] as &$spectrum) {
            foreach ($spectrum['ranges']['values'] as &$range) {
                $range['signals'][0]['atoms'] = [];
            }
        }

        $this->assertNull((new NmriumAssignmentReader)->read($withoutMolecule));
        $this->assertNull((new NmriumAssignmentReader)->read($unassigned));
    }
}
