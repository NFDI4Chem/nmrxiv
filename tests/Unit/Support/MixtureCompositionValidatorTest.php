<?php

namespace Tests\Unit\Support;

use App\Enums\MixtureCompositionBasis;
use App\Support\Mixture\MixtureCompositionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MixtureCompositionValidatorTest extends TestCase
{
    public function test_sum_returns_rounded_total(): void
    {
        $this->assertSame(100.0, MixtureCompositionValidator::sum([62.4, 28.1, 9.5]));
    }

    #[DataProvider('normalizedToleranceProvider')]
    public function test_normalized_tolerance(float $total, bool $expected): void
    {
        $this->assertSame(
            $expected,
            MixtureCompositionValidator::isWithinNormalizedTolerance($total)
        );
    }

    /**
     * @return array<string, array{0: float, 1: bool}>
     */
    public static function normalizedToleranceProvider(): array
    {
        return [
            'exactly 100' => [100.0, true],
            'within tolerance high' => [100.4, true],
            'within tolerance low' => [99.6, true],
            'outside tolerance high' => [100.6, false],
            'outside tolerance low' => [99.4, false],
        ];
    }

    public function test_sum_warning_is_null_when_within_tolerance(): void
    {
        $this->assertNull(
            MixtureCompositionValidator::sumWarning(
                [50, 50],
                MixtureCompositionBasis::MolePercent
            )
        );
    }

    public function test_sum_warning_is_suppressed_when_residual_marked(): void
    {
        $this->assertNull(
            MixtureCompositionValidator::sumWarning(
                [40, 40],
                MixtureCompositionBasis::MolePercent,
                hasResidual: true
            )
        );
    }

    public function test_sum_warning_includes_basis_unit_label(): void
    {
        $warning = MixtureCompositionValidator::sumWarning(
            [40, 40],
            MixtureCompositionBasis::WeightPercent
        );

        $this->assertNotNull($warning);
        $this->assertStringContainsString('80', $warning);
        $this->assertStringContainsString('wt %', $warning);
    }

    public function test_molar_ratio_basis_skips_sum_warning(): void
    {
        $this->assertNull(
            MixtureCompositionValidator::sumWarning(
                [1, 2, 3],
                MixtureCompositionBasis::MolarRatio
            )
        );
    }
}
