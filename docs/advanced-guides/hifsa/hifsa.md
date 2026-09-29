# HiFSA

**HiFSA** (<sup>1</sup>H iterative Full Spin Analysis) is a quantum-mechanical approach to interpreting <sup>1</sup>H NMR spectra. Instead of reading off peak positions and apparent splittings by eye, HiFSA builds a spin-system model of the molecule and iteratively adjusts its chemical shifts (δ), coupling constants (*J*), line shapes and populations until the simulated spectrum matches the experimental one.

The result is a complete, reproducible "fingerprint" of the spectrum that:

* Captures every chemical shift and coupling constant, including those hidden in higher-order multiplets and overlapping signals.
* Can be re-simulated at any field strength, making it independent of the spectrometer frequency used for acquisition.
* Supports structure verification, identity testing and purity/quantitative (qHNMR) analysis, because the fit quality can be measured objectively.

## Producing a HiFSA analysis

HiFSA analyses are typically performed in **Cosmic Truth (CT)** by [NMR Solutions](https://ctb.nmrsolutions.fi/login?returnUrl=~dashboard). A typical workflow is:

1. Load the experimental <sup>1</sup>H spectrum (FID) and the structure of the compound (e.g. an SDF/MOL file).
2. Let Cosmic Truth build the spin systems for the solute and solvent(s).
3. Iterate the quantum-mechanical fit until the simulated and experimental spectra agree.
4. Export the results. nmrXiv understands the following exports:
    * **The analysis report** as a PDF.
    * **The analysis export** as a zip archive whose name ends in `_export.zip`. It contains the Cosmic Truth analysis CSV (with `ANALYSIS INFO` and `SCORES` sections) and, optionally, reference CSVs (`*_REF.csv`) and per-spin-system CSVs under `EXTRA/`.
    * A `.blob` analysis file, if your export includes one.

### Fit scores

Cosmic Truth summarises the quality of a HiFSA fit with five scores: **Match**, **RMS**, **Shift similarity**, **Coupling similarity** and **Intensity**. nmrXiv displays these values as reported by Cosmic Truth; refer to the Cosmic Truth documentation for their exact definitions.

## Submitting HiFSA results to nmrXiv

HiFSA results are submitted alongside the raw data of the sample they belong to. No extra option has to be selected during upload: nmrXiv detects HiFSA folders automatically based on how the files are organised.

### Folder structure

Put all HiFSA files in their own folder, and place that folder **inside the sample (study) folder** or **next to the folder that holds the raw data**. The two layouts below are both recognised:

```
Compound/                 ← study (sample)
├── 1/                    ← Bruker dataset (acqus, acqu, pdata)
├── 2/                    ← Bruker dataset
└── hifsa/                ← HiFSA folder
    ├── report.pdf
    ├── compound_export.zip
    └── compound.blob
```

```
Compound/
├── raw/                  ← study (contains the raw datasets)
│   ├── 1/
│   └── 2/
├── proc/                 ← processed data (ignored as a sample)
└── hifsa/                ← HiFSA folder, sibling of the study folder
    ├── report.pdf
    └── compound_export.zip
```

A folder is recognised as a HiFSA folder when **either**:

* it is named `hifsa` (upper or lower case), **or**
* it directly contains a `.blob` file.

Inside the HiFSA folder, nmrXiv looks for the following files at the top level of the folder:

| File | Required | Used for |
| --- | --- | --- |
| `*.pdf` | Optional | Previewing the HiFSA report. If there are several PDFs, the first one is used. |
| `*_export.zip` | Optional | Extracting the fit scores, spin systems, chemical shifts, couplings, line shapes and integration statistics. |
| `*.blob` | Optional | Detection only (it marks the folder as a HiFSA folder). |

::: tip
A HiFSA folder is **not** a sample and **not** a dataset. nmrXiv never creates a study or a spectrum from it. A HiFSA folder on its own (without raw data next to it) will therefore not produce a study. Always upload HiFSA results together with the raw spectra of the same sample.
:::

::: warning
Do not name a folder that contains raw instrument data `hifsa`. HiFSA detection takes precedence over instrument detection, so the raw data in that folder would not be turned into a dataset.
:::

### Reviewing HiFSA results during submission

After the draft has been processed, open the sample in step 2 of the submission. Samples with HiFSA results show a collapsible **HiFSA** section below the spectra viewer:

* **If an `_export.zip` with scores was found**, nmrXiv shows:
    * A radar chart and summary cards of the five fit scores.
    * The analysis remarks, a link back to the analysis in Cosmic Truth, the solvent, the temperature and provenance information (who created/modified the analysis and when).
    * Collapsible tables for the **spin systems** (name, type, formula, molecular weight, InChIKey, population, LRMS), **chemical shifts** (δ in ppm, spin and nuclei count, line shape, LRMS), **coupling constants** (*J* in Hz), **line shapes** (line width and Gaussian fraction) and **QMGI** integration statistics. Shift, coupling, line-shape and QMGI rows are grouped by spin system.
* **If only a PDF was found**, the HiFSA report is shown inline in the browser.

If the solvent is not stated in the analysis CSV, nmrXiv falls back to the reference CSVs, then to the solvent spin systems (preferring a deuterated solvent over residual water), and finally to the analysis remarks (for example `... in DMSO-d6`).

### Troubleshooting

| Symptom | Likely cause |
| --- | --- |
| No HiFSA section appears | The HiFSA folder is not named `hifsa` and has no `.blob` file, or it is not inside/next to the study folder (for example it is nested one level too deep, or placed under a different sample). |
| Only the PDF is shown, no scores | There is no file ending in `_export.zip` directly in the HiFSA folder, or the zip does not contain a CSV with `ANALYSIS INFO` / `SCORES` sections, or none of the five scores is present. |
| The folder with the HiFSA files became a separate study | The folder was not recognised as a HiFSA folder (not named `hifsa`, no `.blob` file) and also contained raw data that was detected as a dataset. Keep HiFSA exports and raw data in separate folders and name the HiFSA folder `hifsa`. |

## Integration in nmrXiv (developer notes)

This section describes how HiFSA support is implemented. No configuration flag or environment variable is required: detection is based solely on folder structure.

### Detection

When a draft is processed, `FileSystemController::processFolder()` walks the uploaded folder tree. `FileSystemController::isHiFSA()` is checked **before** processed-data, Bruker, Varian and Magritek detection, for both regular and Chemotion ELN drafts. Matching directories are stored with `instrument_type = 'hifsa'`, and their children are not traversed further.

`ProcessDraft::shouldCreateDataset()` excludes `hifsa` (together with `processed`, `nmredata` and `mol`), so no dataset is created for these folders, and they are not picked up by the orphaned-file handling either.

### Associating a HiFSA folder with a study

Because HiFSA folders are not linked to a study directly, `App\Support\Draft\HifsaPdfResolver` matches them structurally. For a given study, it looks for a file-system object in the same draft with `instrument_type = 'hifsa'` whose parent is either:

* the study's folder (the HiFSA folder is a child of the study), or
* the parent of the study's folder (the HiFSA folder is a sibling of the study).

### Parsing and storage

`HifsaPdfResolver::persistCsvData()` runs when a draft is finalised (`ProcessDraft::finalizeProcessing()`) and whenever the draft info endpoint (`DraftController::info()`) is called. For every study it:

1. Skips the study if `hifsa_data` already contains the scores and all section arrays. Older, score-only payloads are re-parsed and upgraded automatically.
2. Locates the `*_export.zip` in the HiFSA folder and streams it from the default filesystem disk (`config('filesystems.default')`) into a temporary file.
3. Picks the analysis CSV: any CSV containing `"ANALYSIS INFO"` or `"SCORES"`, preferring one at the root of the zip over files in sub-folders such as `EXTRA/`.
4. Parses the sections `ANALYSIS INFO`, `SCORES`, `SPINSYSTEMS`, `CHEMICAL SHIFTS (PPM)`, `COUPLING CONSTANTS (HZ)`, `LINESHAPES` and `QMGI`. Cosmic Truth placeholders for "not determined" values (`-1`, `±Infinity`) are stored as `null`.
5. Fills in the solvent and temperature from `*_REF.csv` files if the analysis CSV does not include them.
6. Saves the result in the `studies.hifsa_data` JSON column. If none of the five scores could be read, nothing is stored.

The stored payload has the following shape:

```json
{
  "url": "https://ctb.nmrsolutions.fi//analysis-v/…",
  "ct_key": "AnA_…",
  "name": "sample_analysis",
  "remarks": "sample.fid - compound.sdf in DMSO-d6",
  "solvent": "DMSO-d6",
  "temperature": "298.15",
  "created": { "by": "…", "at": "2026-01-20T17:18:47Z" },
  "modified": { "by": "…", "at": "2026-01-21T09:00:00Z" },
  "scores": {
    "match": 0.85,
    "rms": 0.88,
    "shift_similarity": 0.43,
    "coupling_similarity": 0.84,
    "intensity": 0.99
  },
  "spinsystems": [],
  "chemical_shifts": [],
  "couplings": [],
  "lineshapes": [],
  "qmgi": []
}
```

`HifsaPdfResolver::enrichStudies()` additionally sets a transient `hifsa_pdf_url` attribute on each study that has a HiFSA PDF. It points to the `dashboard.draft.hifsa` route (`GET /dashboard/drafts/{draft}/hifsa/{filesystemobject}`), which streams the PDF inline. The route is only available to users who can update the draft, and only serves `.pdf` files that belong to that draft.

### Frontend

* `resources/js/Pages/Upload.vue` renders the HiFSA section in submission step 2 when a study has `hifsa_data.scores` or a `hifsa_pdf_url`.
* `resources/js/Shared/HifsaScoresPanel.vue` renders the score radar chart, the metadata cards and the detail tables.

### Requirements

* The PHP `zip` extension (`ZipArchive`) must be installed on all application workers.
* The uploaded files must be present on the default filesystem disk at the path stored on their file-system record. Missing or unreadable files are skipped and logged as warnings prefixed with `HiFSA export zip …`.
* The `add_hifsa_data_to_studies_table` migration must have been run.

### Tests

* `tests/Feature/FileSystemTest.php`: detection of HiFSA folders and exclusion from sample detection.
* `tests/Feature/Draft/ProcessDraftWarningsTest.php`: HiFSA folders do not create datasets.
* `tests/Feature/Draft/DraftHifsaPdfTest.php`: PDF resolution, CSV parsing, payload upgrades and PDF streaming.
