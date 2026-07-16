# API

The infrastructure of nmrXiv facilitates API access, enabling software developers to interact with the data programmatically. The interactive reference is generated from OpenAPI annotations and is available at [Swagger / Scalar](https://nmrxiv.org/api/documentation).

All public search endpoints are read-only `GET` requests under `/api/v1/search/`. No authentication is required for public metadata search.

## Search endpoints

| Endpoint | Method | Purpose |
| --- | --- | --- |
| `/api/v1/search/catalog` | `GET` | Free-text search across project, sample, and spectra names/descriptions |
| `/api/v1/search/metadata` | `GET` | Structured NMR metadata search over indexed NMRium fields |
| `/api/v1/search/metadata/facets` | `GET` | Available values for metadata filters (for building UIs) |
| `/api/v1/search/compounds` | `POST` | Structure and compound property search |

## Metadata search

Search public **samples** (studies) and **spectra** (datasets) using denormalized NMRium acquisition metadata. Metadata is extracted from each dataset's NMRium `info` block into indexed columns on the `datasets` table.

### `GET /api/v1/search/metadata`

Returns paginated studies and datasets that match the supplied criteria. **At least one** query parameter must be provided.

#### Query parameters

| Parameter | Type | Indexed field | Description |
| --- | --- | --- | --- |
| `q` | string | `spectra_search_text` | Free-text keywords (AND semantics across tokens) |
| `solvent` | string | `spectra_solvent` | Exact solvent match (case-insensitive), e.g. `CDCl3` |
| `temperature` | number | `spectra_temperature` | Temperature in kelvin; integer values match ±0.5 K |
| `tube_diameter` | string | `spectra_tube_diameter` | Tube diameter in mm: `3`, `5`, or `10` |
| `nucleus` | string | `spectra_nucleus` | Acquisition nucleus, e.g. `1H`, `13C` |
| `proton_frequency` | number | `spectra_base_frequency` | Observed base frequency in MHz (±0.5 MHz) |
| `nmr_method` | string | `spectra_experiment` | Experiment / method, e.g. `hsqc`, `1d` |
| `pulse_sequence` | string | `spectra_pulse_sequence` | Pulse sequence name, e.g. `zg30` |
| `number_of_scans` | integer | `spectra_number_of_scans` | Number of scans |
| `manufacturer` | string | `spectra_manufacturer` | Instrument manufacturer |
| `instrument_model` | string | `spectra_probe_name` | Probe name |
| `per_page` | integer | — | Results per group (default `12`, max `24`) |
| `studies_page` | integer | — | Sample results page (default `1`) |
| `datasets_page` | integer | — | Spectra results page (default `1`) |

#### Example

```bash
curl -G "https://nmrxiv.org/api/v1/search/metadata" \
  --data-urlencode "solvent=CDCl3" \
  --data-urlencode "nucleus=1H" \
  --data-urlencode "proton_frequency=600" \
  --data-urlencode "per_page=12"
```

#### Response `200`

```json
{
  "query": {
    "q": "",
    "tokens": [],
    "solvent": "CDCl3",
    "nucleus": "1H",
    "proton_frequency": 600
  },
  "studies": {
    "data": [],
    "meta": { "total": 1, "current_page": 1, "per_page": 12, "last_page": 1 }
  },
  "datasets": {
    "data": [],
    "meta": { "total": 1, "current_page": 1, "per_page": 12, "last_page": 1 }
  }
}
```

#### Error responses

| Status | When |
| --- | --- |
| `404` | No matching public studies or datasets |
| `422` | Validation failed (e.g. no criteria supplied) |

### `GET /api/v1/search/metadata/facets`

Returns distinct values for each metadata filter that would yield results given the **other** active criteria. Use this to populate select lists or disable unavailable options in a search UI.

Accepts the same filter parameters as metadata search (except pagination). All parameters are optional; with no filters, facets reflect the full public catalog.

#### Example

```bash
curl -G "https://nmrxiv.org/api/v1/search/metadata/facets" \
  --data-urlencode "solvent=CDCl3"
```

#### Response `200`

```json
{
  "facets": {
    "solvent": ["CDCl3", "DMSO"],
    "temperature": ["294"],
    "tube_diameter": [],
    "nucleus": ["1H", "13C"],
    "proton_frequency": ["600"],
    "nmr_method": ["1d", "hsqc"],
    "pulse_sequence": ["zg30"],
    "number_of_scans": ["16"],
    "manufacturer": ["Bruker"],
    "instrument_model": ["BBO"]
  }
}
```

Facet keys with empty arrays indicate that no public indexed spectra currently carry that metadata.

## Catalog text search

### `GET /api/v1/search/catalog`

Free-text search across published project, sample, and spectra names and descriptions. Requires `q`.

```bash
curl -G "https://nmrxiv.org/api/v1/search/catalog" \
  --data-urlencode "q=caffeine"
```

## Indexing note for operators

Metadata search relies on denormalized columns populated from NMRium. After deployment or bulk imports, run:

```bash
php artisan migrate
php artisan nmrxiv:extract-dataset-spectra-info
```

Re-run with `--force` to refresh all datasets after extractor changes.

## OpenAPI

Machine-readable specifications are generated with `php artisan l5-swagger:generate` and served at `/api/documentation`. Metadata operations are tagged **Search** with operation IDs `searchMetadata` and `searchMetadataFacets`.
