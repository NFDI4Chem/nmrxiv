# Data quality scoring (developers)

nmrXiv scores public compounds for NMR data completeness using a **versioned, config-driven rubric**. This page is for contributors changing the rules or extending the scoring system.

## Architecture

```
config/quality.php
        │
        ▼
QualityRubric  ◄── EvidenceGatherer
        │              ├── SpectraEvidenceCollector
        │              └── AssignmentEvidenceCollector
        ▼
QualityResult (tier + breakdown + version)
        │
        ├── molecules.annotation_level / quality_breakdown  (global)
        └── team_molecule_quality_scores                    (per workspace)
```

- **Global scores** power compound cards and the compound page.
- **Team-scoped scores** power library contributor stars so a workspace is only credited for its own public data.

## Config reference: `config/quality.php`

| Key | Purpose |
| --- | --- |
| `version` | Integer; bump on every rule change |
| `docs_url` | Link used by the in-app "How is this scored?" modal |
| `families` | Experiment tokens / nuclei / dimension → family keys |
| `criteria` | Criterion id → class (+ family for experiment criteria) |
| `tiers` | Star level → label + `requires` (nested array = any-of) |
| `bonuses` | Criterion ids shown as badges only |
| `contributor` | Qualifying tier + threshold table |
| `collectors` | Evidence collector classes |

`QualityRubric` validates the config on construction (unknown keys, duplicate experiment tokens). Broken edits fail tests rather than production.

## Adding an experiment token

Example: accept `h2bc` as satisfying the COSY family.

1. Add `'h2bc'` to `families.cosy.experiments` in `config/quality.php`.
2. Bump `version`.
3. Update unit tests and the user-facing docs (`docs/data-quality/*`).
4. Run `php artisan nmrxiv:score-molecules --stale`.

## Adding a criterion

1. Implement `App\Support\Quality\Criteria\QualityCriterion`.
2. Register it under `criteria` in `config/quality.php`.
3. Reference it from a tier's `requires` (and/or `bonuses`).
4. Bump `version`, update tests and docs, rescore with `--stale`.

If the criterion needs new evidence (for example raw FID presence), also implement `EvidenceCollector` and add the class to `collectors`.

## Commands and jobs

```bash
php artisan nmrxiv:score-molecules
php artisan nmrxiv:score-molecules --molecule=123 --skip-teams
php artisan nmrxiv:score-molecules --stale
php artisan nmrxiv:score-molecules --dry
```

| Option | Meaning |
| --- | --- |
| `--molecule=*` | Restrict to molecule id(s) |
| `--team=*` | Restrict team-scoped scoring |
| `--skip-teams` | Global scores only |
| `--stale` | Only rows with an older or missing rubric version |
| `--chunk=100` | Evidence-gathering batch size |
| `--dry` | Compute without writing |

Scheduled nightly at **03:30** (`routes/console.php`).

`App\Jobs\ScoreMoleculeQuality` is dispatched (unique per study/project) from:

- Publish / unpublish / archive / delete / restore project actions
- Publish study
- `DatasetController::updateAssignments` when the study is public

## Change checklist

Every rubric change must, in the same PR:

1. Bump `config/quality.php` → `version`
2. Update unit / feature tests (including an extensibility case if you add a criterion)
3. Update `docs/data-quality/overview.md`, `experiments.md`, and `contributor-stars.md` as needed
4. Run `nmrxiv:score-molecules --stale` after deploy (or rely on the nightly job)
