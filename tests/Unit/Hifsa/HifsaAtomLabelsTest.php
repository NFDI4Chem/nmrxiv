<?php

namespace Tests\Unit\Hifsa;

use App\Support\Hifsa\HifsaAtomLabels;
use PHPUnit\Framework\TestCase;

class HifsaAtomLabelsTest extends TestCase
{
    public function test_parse_atom_handles_element_serial_and_suffix(): void
    {
        $this->assertSame([
            'element' => 'H',
            'serial' => 14,
            'suffix' => null,
            'raw' => 'H14',
        ], HifsaAtomLabels::parseAtom('H14'));

        $this->assertSame([
            'element' => 'H',
            'serial' => 14,
            'suffix' => 'a',
            'raw' => 'H14a',
        ], HifsaAtomLabels::parseAtom('H14a'));

        $this->assertNull(HifsaAtomLabels::parseAtom('not-an-atom'));
        $this->assertNull(HifsaAtomLabels::parseAtom(''));
    }

    public function test_parse_group_splits_equivalent_atoms(): void
    {
        $this->assertSame([
            [
                'element' => 'C',
                'serial' => 10,
                'suffix' => null,
                'raw' => 'C10',
            ],
            [
                'element' => 'C',
                'serial' => 11,
                'suffix' => null,
                'raw' => 'C11',
            ],
        ], HifsaAtomLabels::parseGroup('C10,C11'));

        $this->assertSame([], HifsaAtomLabels::parseGroup(null));
    }

    public function test_pair_coupling_zips_equivalent_groups(): void
    {
        $pairs = HifsaAtomLabels::pairCoupling('C24,C25', 'H24,H25');

        $this->assertCount(2, $pairs);
        $this->assertSame(24, $pairs[0]['from']['serial']);
        $this->assertSame(24, $pairs[0]['to']['serial']);
        $this->assertSame('H', $pairs[0]['to']['element']);
        $this->assertSame(25, $pairs[1]['from']['serial']);
        $this->assertSame(25, $pairs[1]['to']['serial']);
    }

    public function test_pair_coupling_handles_single_from_to(): void
    {
        $pairs = HifsaAtomLabels::pairCoupling('C14', 'H14');

        $this->assertCount(1, $pairs);
        $this->assertSame('C14', $pairs[0]['from']['raw']);
        $this->assertSame('H14', $pairs[0]['to']['raw']);
    }

    public function test_pair_coupling_connects_geminal_same_group(): void
    {
        $pairs = HifsaAtomLabels::pairCoupling('H28,H29', 'H28,H29');

        $this->assertCount(1, $pairs);
        $this->assertSame('H28', $pairs[0]['from']['raw']);
        $this->assertSame('H29', $pairs[0]['to']['raw']);
    }

    public function test_pair_coupling_does_not_clamp_unequal_group_lengths(): void
    {
        $pairs = HifsaAtomLabels::pairCoupling('C24,C25', 'H24');

        $this->assertCount(1, $pairs);
        $this->assertSame('C24', $pairs[0]['from']['raw']);
        $this->assertSame('H24', $pairs[0]['to']['raw']);
    }

    public function test_resolve_molecule_requires_inchi_key_match(): void
    {
        $molecules = [
            [
                'id' => 1,
                'inchi_key' => 'OTHERKEY-UHFFFAOYSA-N',
                'sdf' => "mol1\n",
            ],
            [
                'id' => 2,
                'inchi_key' => 'LGPKJUJXISCYQZ-HTCHUFIESA-N',
                'sdf' => "mol2\n",
            ],
            [
                'id' => 3,
                'inchi_key' => null,
                'sdf' => null,
            ],
        ];

        $matched = HifsaAtomLabels::resolveMolecule($molecules, [
            'inchi_key' => 'LGPKJUJXISCYQZ-HTCHUFIESA-N',
        ]);

        $this->assertSame(2, $matched['id']);
    }

    public function test_resolve_molecule_refuses_wrong_enantiomer_fallback(): void
    {
        $molecules = [
            [
                'id' => 7,
                'inchi_key' => 'LGPKJUJXISCYQZ-HTCHUFIESA-N',
                'sdf' => "7r\n",
            ],
            [
                'id' => 8,
                'inchi_key' => 'LGPKJUJXISCYQZ-BOXYVWFNSA-N',
                'sdf' => "7s\n",
            ],
        ];

        $matched = HifsaAtomLabels::resolveMolecule($molecules, [
            'inchi_key' => 'LGPKJUJXISCYQZ-BOXYVWFNSA-N',
        ]);

        $this->assertSame(8, $matched['id']);

        $this->assertNull(HifsaAtomLabels::resolveMolecule($molecules, [
            'inchi_key' => 'MISSING-UHFFFAOYSA-N',
        ]));

        $this->assertNull(HifsaAtomLabels::resolveMolecule($molecules, [
            'inchi_key' => null,
        ]));

        $this->assertNull(HifsaAtomLabels::resolveMolecule([
            ['id' => 11, 'sdf' => "has-sdf\n"],
        ], ['inchi_key' => 'MISSING']));
    }
}
