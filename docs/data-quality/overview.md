# Data completeness scores

Stars on nmrXiv compounds measure how complete the **publicly available NMR evidence** is for a compound's constitution. They are not a measure of spectral quality, spectrometer field strength, or whether the proposed structure is correct.

## Tiers

| Stars | Label | Requirements |
| --- | --- | --- |
| 0 | Not yet rated | No public spectra scored for this compound |
| 1 | Spectra available | At least one public spectrum |
| 2 | 1D characterised | ¹H and ¹³C |
| 3 | Heteronuclear correlation | ¹H, ¹³C, and HSQC **or** HMBC |
| 4 | Full elucidation set | ¹H, ¹³C, COSY/TOCSY, HSQC, and HMBC |
| 5 | Fully assigned | Full elucidation set **plus** NMR assignments |

**Bonus badges** (shown when present, not required for any tier):

- **DEPT** — carbon multiplicity
- **NOESY/ROESY** — through-space contacts for relative configuration

See [Why each experiment matters](./experiments.md) for the chemistry behind these choices.

## What counts

- Only **public** studies and datasets are scored.
- Experiment types come from NMRium spectrum metadata (`nucleus`, `experiment`, `dimension`).
- Assignments count when either:
  - the Assignments tab has an ACS-style string or atom–peak table, or
  - NMRium ranges/zones contain non-empty `diaIDs` linking atoms to signals.
- Every score stores which **rubric version** produced it. When the scoring rules change, scores are recomputed.

## Where you see stars

- Compound cards in search and library results
- The compound page checklist (present / missing experiments)
- The [compound library](./contributor-stars.md) data-quality panel (contributor / workspace stars)

## FAQ

### My compound has fewer stars than expected

Check that the missing experiments are **published** and that NMRium recognises their experiment type (for example `hsqc`, `hmbc`, `cosy`). Private drafts do not count.

### Why didn't my DEPT count as ¹³C?

DEPT does not observe quaternary carbons, so it cannot replace a ¹³C spectrum. DEPT is shown as a bonus badge instead. A multiplicity-edited HSQC often covers the same multiplicity information once ¹³C and HSQC are present.

### How do I raise a score?

Add the missing experiment (or assignments), then publish the study. Scores refresh when a study is published and again on a nightly safety-net job.

### How is the contributor score calculated?

See [Library and contributor stars](./contributor-stars.md).
