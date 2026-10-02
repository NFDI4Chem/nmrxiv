<?php

namespace Tests\Unit\Support\Quality;

use App\Support\Quality\Criteria\QualityCriterion;
use App\Support\Quality\MoleculeEvidence;
use App\Support\Quality\QualityRubric;
use App\Support\Quality\SpectrumDescriptor;
use InvalidArgumentException;
use Tests\TestCase;

class QualityRubricTest extends TestCase
{
    public function test_evaluates_tier_boundaries(): void
    {
        $rubric = new QualityRubric;

        $none = $rubric->evaluate(new MoleculeEvidence(1));
        $this->assertSame(0, $none->tier);

        $one = $rubric->evaluate(new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
        ]));
        $this->assertSame(1, $one->tier);

        $two = $rubric->evaluate(new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
            new SpectrumDescriptor(1, ['13C'], '1d'),
        ]));
        $this->assertSame(2, $two->tier);

        $three = $rubric->evaluate(new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
            new SpectrumDescriptor(1, ['13C'], '1d'),
            new SpectrumDescriptor(2, ['1H', '13C'], 'hsqc'),
        ]));
        $this->assertSame(3, $three->tier);

        $four = $rubric->evaluate(new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
            new SpectrumDescriptor(1, ['13C'], '1d'),
            new SpectrumDescriptor(2, ['1H'], 'cosy'),
            new SpectrumDescriptor(2, ['1H', '13C'], 'hsqc'),
            new SpectrumDescriptor(2, ['1H', '13C'], 'hmbc'),
        ]));
        $this->assertSame(4, $four->tier);
        $this->assertFalse($four->criteria['assignments']);
        $this->assertSame(['assignments'], $four->nextTierMissing);

        $five = $rubric->evaluate(new MoleculeEvidence(
            moleculeId: 1,
            spectra: [
                new SpectrumDescriptor(1, ['1H'], '1d'),
                new SpectrumDescriptor(1, ['13C'], '1d'),
                new SpectrumDescriptor(2, ['1H'], 'cosy'),
                new SpectrumDescriptor(2, ['1H', '13C'], 'hsqc'),
                new SpectrumDescriptor(2, ['1H', '13C'], 'hmbc'),
                new SpectrumDescriptor(2, ['1H'], 'noesy'),
                new SpectrumDescriptor(1, ['13C'], 'dept'),
            ],
            hasAssignments: true,
        ));
        $this->assertSame(5, $five->tier);
        $this->assertTrue($five->bonuses['noe']);
        $this->assertTrue($five->bonuses['dept']);
        $this->assertSame([], $five->nextTierMissing);
    }

    public function test_tier_three_accepts_hmbc_without_hsqc(): void
    {
        $rubric = new QualityRubric;
        $result = $rubric->evaluate(new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
            new SpectrumDescriptor(1, ['13C'], '1d'),
            new SpectrumDescriptor(2, ['1H', '13C'], 'hmbc'),
        ]));

        $this->assertSame(3, $result->tier);
    }

    public function test_rejects_invalid_config(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new QualityRubric([
            'version' => 1,
            'families' => [],
            'criteria' => [],
            'tiers' => [
                1 => ['label' => 'Broken', 'requires' => ['missing_key']],
            ],
            'bonuses' => [],
            'collectors' => [],
        ]);
    }

    public function test_extensible_with_custom_criterion(): void
    {
        $custom = new class implements QualityCriterion
        {
            public function key(): string
            {
                return 'has_fid';
            }

            public function label(): string
            {
                return 'Raw FID';
            }

            public function isMet(MoleculeEvidence $evidence): bool
            {
                return (bool) ($evidence->extra['has_fid'] ?? false);
            }
        };

        $evidence = new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
        ], extra: ['has_fid' => true]);

        $this->assertTrue($custom->isMet($evidence));
        $this->assertFalse($custom->isMet(new MoleculeEvidence(1)));

        // Versioned config can be swapped without code changes to the evaluator.
        $config = config('quality');
        $config['version'] = 99;
        $rubric = new QualityRubric($config);
        $this->assertSame(99, $rubric->version());
    }

    public function test_for_frontend_exposes_docs_url_and_tiers(): void
    {
        $payload = (new QualityRubric)->forFrontend();

        $this->assertSame(1, $payload['version']);
        $this->assertNotEmpty($payload['docs_url']);
        $this->assertArrayHasKey(4, $payload['tiers']);
        $this->assertArrayHasKey('proton', $payload['criteria']);
    }
}
