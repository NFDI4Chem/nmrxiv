# Library and contributor stars

Each shareable [compound library](https://docs.nmrxiv.org) page (`/library/{code}`) shows a **Data quality** panel with contributor stars for that workspace.

## How they are counted

1. Every public compound is scored under that workspace using **only spectra and assignments from the workspace's own public studies**.
2. Compounds that reach the **qualifying tier** (default: 4★, full elucidation set) or higher are counted.
3. Contributor stars follow configurable thresholds (defaults):

| Contributor stars | Fully characterised compounds (4★+) |
| --- | --- |
| 1 | 1 |
| 2 | 5 |
| 3 | 15 |
| 4 | 40 |
| 5 | 100 |

A workspace never receives credit for an HMBC (or any other spectrum) uploaded by another group, even if it shares the same molecule record in the public catalogue.

## Reading the panel

- **Stars** — the contributor level for this library
- **Fully characterised compounds** — how many of this workspace's compounds are at 4★ or 5★
- **Tier distribution bar** — how many compounds sit at each star level
- **Next-level hint** — how many more 4★+ compounds are needed for the next contributor star

## Personal vs group workspaces

- **Personal libraries** only count studies owned by the library owner.
- **Group (team) libraries** count all non-deleted studies in that workspace (outside trashed projects).

## Raising the score

Publish more compounds with a full elucidation set (¹H, ¹³C, COSY/TOCSY, HSQC, HMBC), ideally with assignments for 5★ compounds. Scores refresh on publish and on a nightly job.
