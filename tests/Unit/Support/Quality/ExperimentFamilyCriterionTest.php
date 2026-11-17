<?php

namespace Tests\Unit\Support\Quality;

use App\Support\Quality\Criteria\ExperimentFamilyCriterion;
use App\Support\Quality\MoleculeEvidence;
use App\Support\Quality\SpectrumDescriptor;
use Tests\TestCase;

class ExperimentFamilyCriterionTest extends TestCase
{
    public function test_matches_proton_1d(): void
    {
        $criterion = ExperimentFamilyCriterion::fromFamilyConfig('proton', [
            'label' => '1H',
            'dimension' => 1,
            'nuclei' => ['1H'],
            'experiments' => ['1d'],
        ]);

        $evidence = new MoleculeEvidence(1, [
            new SpectrumDescriptor(1, ['1H'], '1d'),
        ]);

        $this->assertTrue($criterion->isMet($evidence));
    }

    public function test_carbon_accepts_apt_but_not_dept(): void
    {
        $carbon = ExperimentFamilyCriterion::fromFamilyConfig('carbon', config('quality.families.carbon'));
        $dept = ExperimentFamilyCriterion::fromFamilyConfig('dept', config('quality.families.dept'));

        $apt = new MoleculeEvidence(1, [new SpectrumDescriptor(1, ['13C'], 'apt')]);
        $deptSpectrum = new MoleculeEvidence(1, [new SpectrumDescriptor(1, ['13C'], 'dept135')]);
        $proton = new MoleculeEvidence(1, [new SpectrumDescriptor(1, ['1H'], '1d')]);

        $this->assertTrue($carbon->isMet($apt));
        $this->assertFalse($carbon->isMet($deptSpectrum));
        $this->assertFalse($carbon->isMet($proton));

        $this->assertTrue($dept->isMet($deptSpectrum));
        $this->assertFalse($dept->isMet($apt));
    }

    public function test_hsqc_requires_matching_experiment_token(): void
    {
        $hsqc = ExperimentFamilyCriterion::fromFamilyConfig('hsqc', config('quality.families.hsqc'));

        $this->assertTrue($hsqc->isMet(new MoleculeEvidence(1, [
            new SpectrumDescriptor(2, ['1H', '13C'], 'edited-hsqc'),
        ])));

        $this->assertFalse($hsqc->isMet(new MoleculeEvidence(1, [
            new SpectrumDescriptor(2, ['1H', '13C'], 'hmbc'),
        ])));
    }

    public function test_dimension_mismatch_rejects_spectrum(): void
    {
        $proton = ExperimentFamilyCriterion::fromFamilyConfig('proton', config('quality.families.proton'));

        $this->assertFalse($proton->isMet(new MoleculeEvidence(1, [
            new SpectrumDescriptor(2, ['1H'], '1d'),
        ])));
    }

    public function test_null_dimension_on_spectrum_is_allowed(): void
    {
        $proton = ExperimentFamilyCriterion::fromFamilyConfig('proton', config('quality.families.proton'));

        $this->assertTrue($proton->isMet(new MoleculeEvidence(1, [
            new SpectrumDescriptor(null, ['1H'], '1d'),
        ])));
    }
}
