<?php

namespace Tests\Unit\Support\Nmr;

use App\Support\Nmr\ExperimentCategoryClassifier;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ExperimentCategoryClassifierTest extends TestCase
{
    #[DataProvider('classifyProvider')]
    public function test_classifies_pulse_sequences_and_experiments(
        ?string $pulseSequence,
        ?string $experiment,
        ?string $nucleus,
        ?int $dimension,
        string $expected,
    ): void {
        $this->assertSame(
            $expected,
            ExperimentCategoryClassifier::classify($pulseSequence, $experiment, $nucleus, $dimension)
        );
    }

    /**
     * @return array<string, array{0: ?string, 1: ?string, 2: ?string, 3: ?int, 4: string}>
     */
    public static function classifyProvider(): array
    {
        return [
            'bruker cosy' => ['cosygpqf', 'cosy', null, 2, 'COSY'],
            'bruker hsqc' => ['hsqcedetgp', 'hsqc', null, 2, 'HSQC'],
            'bruker hmbc' => ['hmbcetgpl3nd', 'hmbc', null, 2, 'HMBC'],
            'bruker noesy' => ['noesygpph', 'noesy', null, 2, 'NOESY'],
            'bruker roesy' => ['roesyph', null, null, 2, 'ROESY'],
            'bruker tocsy' => ['mlevph', 'tocsy', null, 2, 'TOCSY'],
            'dept' => ['dept135', 'dept', '13C', 1, 'DEPT'],
            'jeol proton' => ['proton.jxp', 'proton', '1H', 1, '1H'],
            'jeol carbon' => ['carbon.jxp', 'carbon', '13C', 1, '13C'],
            'bruker proton zg30' => ['zg30', 'proton', '1H', 1, '1H'],
            'bruker carbon zgpg30 not proton' => ['zgpg30', 'carbon', '13C', 1, '13C'],
            '1d nucleus fallback' => [null, null, '1H', 1, '1H'],
            '13c nucleus fallback' => [null, null, '13C', 1, '13C'],
            'unknown' => ['customseq', 'custom', '15N', 2, 'Other'],
        ];
    }
}
