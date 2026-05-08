<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\MichiV1Schema;
use PHPUnit\Framework\TestCase;

class MichiV1SchemaTest extends TestCase
{
    public function test_schema_contains_required_l1_fields(): void
    {
        $ids = array_column(MichiV1Schema::rows(), 'id');

        $expectedL1 = [
            'nfdi.nmr.sample.compound',
            'nfdi.nmr.sample.solvent',
            'nfdi.nmr.acquisition.nucleus',
            'nfdi.nmr.acquisition.proton_frequency',
            'nfdi.nmr.acquisition.method',
            'nfdi.nmr.acquisition.pulse',
        ];

        foreach ($expectedL1 as $expected) {
            $this->assertContains($expected, $ids, "MIChI v1 L1 field missing: {$expected}");
        }
    }

    public function test_every_row_has_required_keys(): void
    {
        $required = ['id', 'label', 'level', 'cardinality', 'extractor', 'kind'];

        foreach (MichiV1Schema::rows() as $row) {
            foreach ($required as $key) {
                $this->assertArrayHasKey($key, $row, "row {$row['id']} missing key {$key}");
            }
        }
    }

    public function test_levels_are_one_or_two(): void
    {
        foreach (MichiV1Schema::rows() as $row) {
            $this->assertContains($row['level'], [1, 2], "row {$row['id']} has invalid level");
        }
    }

    public function test_kinds_are_known(): void
    {
        foreach (MichiV1Schema::rows() as $row) {
            $this->assertContains($row['kind'], ['subject', 'numeric', 'boolean'], "row {$row['id']} has invalid kind");
        }
    }
}
