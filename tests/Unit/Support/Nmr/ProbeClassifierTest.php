<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\ProbeClassifier;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProbeClassifierTest extends TestCase
{
    #[DataProvider('classifyProvider')]
    public function test_classifies_probe_names(string $probeName, string $expected): void
    {
        $this->assertSame($expected, ProbeClassifier::classify($probeName));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function classifyProvider(): array
    {
        return [
            'bbo rt zgrad' => ['5 mm BBO 1H-BB-D Z-GRD', 'BBO · RT · Z-grad'],
            'cryo inverse' => ['5 mm CPTCI 1H-13C/15N Z-GRD', 'Inverse · cryo · Z-grad'],
            'txi rt' => ['5 mm TXI 1H-13C/15N Z-GRD', 'Inverse · RT · Z-grad'],
            'cryo bbo' => ['5 mm CPBBO BB-1H Z-GRD', 'BBO · cryo · Z-grad'],
            'unknown probe' => ['Custom Probe XYZ', 'Other · RT'],
            'empty' => ['', 'Other'],
        ];
    }

    public function test_null_probe_is_other(): void
    {
        $this->assertSame('Other', ProbeClassifier::classify(null));
    }
}
