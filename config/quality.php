<?php

use App\Support\Quality\Criteria\AnySpectrumCriterion;
use App\Support\Quality\Criteria\AssignmentsCriterion;
use App\Support\Quality\Criteria\ExperimentFamilyCriterion;
use App\Support\Quality\Evidence\AssignmentEvidenceCollector;
use App\Support\Quality\Evidence\SpectraEvidenceCollector;

return [

    /*
    |--------------------------------------------------------------------------
    | Rubric Version
    |--------------------------------------------------------------------------
    |
    | Bump this whenever families, criteria, tiers, or bonuses change. Scores
    | store the version that produced them; `nmrxiv:score-molecules --stale`
    | rescores rows scored under an older version.
    |
    */

    'version' => 1,

    /*
    |--------------------------------------------------------------------------
    | Documentation
    |--------------------------------------------------------------------------
    */

    'docs_url' => env('QUALITY_DOCS_URL', 'https://docs.nmrxiv.org/data-quality/overview.html'),

    /*
    |--------------------------------------------------------------------------
    | Experiment Families
    |--------------------------------------------------------------------------
    |
    | Map NMRium experiment tokens (and optional nuclei / dimension) to the
    | families used by the rubric. Adding a pulse sequence (e.g. h2bc) is a
    | config edit — no PHP change required.
    |
    */

    'families' => [
        'proton' => [
            'label' => '1H',
            'dimension' => 1,
            'nuclei' => ['1H'],
            'experiments' => ['1d'],
        ],
        'carbon' => [
            'label' => '13C',
            'dimension' => 1,
            'nuclei' => ['13C'],
            'experiments' => ['1d', 'apt'],
        ],
        'dept' => [
            'label' => 'DEPT',
            'dimension' => 1,
            'nuclei' => ['13C'],
            'experiments' => ['dept', 'dept45', 'dept90', 'dept135'],
        ],
        'cosy' => [
            'label' => 'COSY/TOCSY',
            'dimension' => 2,
            'experiments' => ['cosy', 'tocsy'],
        ],
        'hsqc' => [
            'label' => 'HSQC/HMQC',
            'dimension' => 2,
            'experiments' => ['hsqc', 'hmqc', 'edited-hsqc', 'hsqc-tocsy'],
        ],
        'hmbc' => [
            'label' => 'HMBC',
            'dimension' => 2,
            'experiments' => ['hmbc'],
        ],
        'noe' => [
            'label' => 'NOESY/ROESY',
            'dimension' => 2,
            'experiments' => ['noesy', 'roesy'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Criteria
    |--------------------------------------------------------------------------
    |
    | Each entry is keyed by a stable criterion id used in tier rules and the
    | stored breakdown. Experiment-family criteria reference a family key;
    | other criteria resolve via their class alone.
    |
    */

    'criteria' => [
        'any_spectrum' => [
            'class' => AnySpectrumCriterion::class,
            'label' => 'Any spectrum',
        ],
        'proton' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'proton',
        ],
        'carbon' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'carbon',
        ],
        'dept' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'dept',
        ],
        'cosy' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'cosy',
        ],
        'hsqc' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'hsqc',
        ],
        'hmbc' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'hmbc',
        ],
        'noe' => [
            'class' => ExperimentFamilyCriterion::class,
            'family' => 'noe',
        ],
        'assignments' => [
            'class' => AssignmentsCriterion::class,
            'label' => 'Assigned',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiers
    |--------------------------------------------------------------------------
    |
    | Rules: every entry in `requires` must pass. A nested array means "any of".
    | Tiers must build on lower tiers (validated when the rubric is constructed).
    |
    */

    'tiers' => [
        1 => [
            'label' => 'Spectra available',
            'requires' => ['any_spectrum'],
        ],
        2 => [
            'label' => '1D characterised',
            'requires' => ['proton', 'carbon'],
        ],
        3 => [
            'label' => 'Heteronuclear correlation',
            'requires' => ['proton', 'carbon', ['hsqc', 'hmbc']],
        ],
        4 => [
            'label' => 'Full elucidation set',
            'requires' => ['proton', 'carbon', 'cosy', 'hsqc', 'hmbc'],
        ],
        5 => [
            'label' => 'Fully assigned',
            'requires' => ['proton', 'carbon', 'cosy', 'hsqc', 'hmbc', 'assignments'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bonus Badges
    |--------------------------------------------------------------------------
    |
    | Criteria shown as badges but not required for any tier.
    |
    */

    'bonuses' => ['dept', 'noe'],

    /*
    |--------------------------------------------------------------------------
    | Contributor Stars
    |--------------------------------------------------------------------------
    |
    | Library / workspace stars are based on how many of that workspace's
    | compounds reach at least `qualifying_tier`.
    |
    */

    'contributor' => [
        'qualifying_tier' => 4,
        'thresholds' => [
            1 => 1,
            2 => 5,
            3 => 15,
            4 => 40,
            5 => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Evidence Collectors
    |--------------------------------------------------------------------------
    */

    'collectors' => [
        SpectraEvidenceCollector::class,
        AssignmentEvidenceCollector::class,
    ],

];
