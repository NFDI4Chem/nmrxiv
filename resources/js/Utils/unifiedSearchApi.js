import axios from "axios";

/** @readonly */
export const SEARCH_SCOPE = {
    CATALOG: "catalog",
    COMPOUNDS: "compounds",
    METADATA: "metadata",
};

export const emptyCatalogSection = () => ({
    data: [],
    meta: {
        total: 0,
        current_page: 1,
        per_page: 12,
        last_page: 1,
    },
});

export const emptyCatalogResults = () => ({
    query: "",
    tokens: [],
    projects: emptyCatalogSection(),
    studies: emptyCatalogSection(),
    datasets: emptyCatalogSection(),
});

export const emptyMetadataSection = () => ({
    data: [],
    meta: {
        total: 0,
        current_page: 1,
        per_page: 12,
        last_page: 1,
    },
});

export const emptyMetadataResults = () => ({
    query: {},
    studies: emptyMetadataSection(),
    datasets: emptyMetadataSection(),
});

const METADATA_PARAM_KEYS = [
    "q",
    "solvent",
    "temperature",
    "tube_diameter",
    "nucleus",
    "proton_frequency",
    "nmr_method",
    "pulse_sequence",
    "number_of_scans",
    "manufacturer",
    "instrument_model",
    "per_page",
    "studies_page",
    "datasets_page",
];

export const METADATA_FORM_URL_KEYS = [
    "q",
    "solvent",
    "temperature",
    "tube_diameter",
    "nucleus",
    "proton_frequency",
    "nmr_method",
    "pulse_sequence",
    "number_of_scans",
    "manufacturer",
    "instrument_model",
];

/**
 * Public catalog search (projects, samples, spectra).
 *
 * @param {object} params
 * @param {string} params.q
 * @param {number} [params.per_page]
 * @param {number} [params.projects_page]
 * @param {number} [params.studies_page]
 * @param {number} [params.datasets_page]
 */
export async function fetchCatalogSearch(params) {
    const { data } = await axios.get("/api/v1/search/catalog", {
        params,
    });

    return data;
}

/**
 * Public metadata search (NMRium spectrum info on datasets).
 *
 * @param {object} params
 */
export async function fetchMetadataSearch(params) {
    const { data } = await axios.get("/api/v1/search/metadata", {
        params,
    });

    return data;
}

/**
 * Available metadata facet values for the current filter state.
 *
 * @param {object} params
 */
export async function fetchMetadataFacets(params = {}) {
    const mapped = metadataParamsFromForm(params);
    const query = {};

    for (const [key, value] of Object.entries(mapped)) {
        if (value !== null && value !== undefined && value !== "") {
            query[key] = value;
        }
    }

    const { data } = await axios.get("/api/v1/search/metadata/facets", {
        params: query,
    });

    return data.facets ?? {};
}

/**
 * Map MetadataSearchContent camelCase fields to API query params.
 *
 * @param {object} params
 */
export function metadataParamsFromForm(params = {}) {
    return {
        q: params.freeText?.trim() || params.q?.trim() || "",
        solvent: params.solvent?.trim() || params.solvent || "",
        temperature: params.temperature ?? "",
        tube_diameter:
            params.tubeDiameter?.trim() || params.tube_diameter?.trim() || "",
        nucleus:
            params.acquisitionNucleus?.trim() || params.nucleus?.trim() || "",
        proton_frequency:
            params.protonFrequency || params.proton_frequency || "",
        nmr_method: params.nmrMethod?.trim() || params.nmr_method?.trim() || "",
        pulse_sequence:
            params.pulseSequence?.trim() || params.pulse_sequence?.trim() || "",
        number_of_scans: params.numberOfScans ?? params.number_of_scans ?? "",
        manufacturer: params.manufacturer?.trim() || "",
        instrument_model:
            params.instrumentModel?.trim() ||
            params.instrument_model?.trim() ||
            "",
    };
}

/**
 * Map URL / API metadata params back to MetadataSearchContent form fields.
 *
 * @param {object} params
 */
export function metadataParamsToForm(params = {}) {
    return {
        freeText: params.q ?? params.freeText ?? "",
        solvent: params.solvent ?? "",
        temperature:
            params.temperature != null && params.temperature !== ""
                ? String(params.temperature)
                : "",
        tubeDiameter: params.tube_diameter ?? params.tubeDiameter ?? "",
        acquisitionNucleus: params.nucleus ?? params.acquisitionNucleus ?? "",
        protonFrequency:
            params.proton_frequency != null && params.proton_frequency !== ""
                ? String(params.proton_frequency)
                : params.protonFrequency ?? "",
        nmrMethod: params.nmr_method ?? params.nmrMethod ?? "",
        pulseSequence: params.pulse_sequence ?? params.pulseSequence ?? "",
        numberOfScans:
            params.number_of_scans != null && params.number_of_scans !== ""
                ? String(params.number_of_scans)
                : params.numberOfScans ?? "",
        manufacturer: params.manufacturer ?? "",
        instrumentModel:
            params.instrument_model ?? params.instrumentModel ?? "",
    };
}

/**
 * @param {string} [href]
 */
export function readMetadataParamsFromUrl(href) {
    const url = new URL(href || window.location.href);
    const perPage = parseInt(url.searchParams.get("per_page") || "12", 10);
    const fallbackPage = parseInt(url.searchParams.get("page") || "1", 10);

    const params = {
        scope: url.searchParams.get("scope") || SEARCH_SCOPE.METADATA,
        per_page: Number.isFinite(perPage) ? perPage : 12,
        studies_page: parseInt(
            url.searchParams.get("studies_page") || String(fallbackPage),
            10
        ),
        datasets_page: parseInt(
            url.searchParams.get("datasets_page") || String(fallbackPage),
            10
        ),
    };

    for (const key of METADATA_PARAM_KEYS) {
        if (
            key === "per_page" ||
            key === "studies_page" ||
            key === "datasets_page"
        ) {
            continue;
        }

        const value = url.searchParams.get(key);
        if (value !== null && value !== "") {
            params[key] = value;
        }
    }

    return params;
}

/**
 * @param {object} params
 */
export function syncMetadataBrowserUrl(params) {
    const url = new URL(window.location.href);

    url.pathname = "/search";
    url.searchParams.set("scope", SEARCH_SCOPE.METADATA);
    url.searchParams.delete("query");
    url.searchParams.delete("type");
    url.searchParams.delete("page");
    url.searchParams.delete("tab");

    for (const key of METADATA_PARAM_KEYS) {
        const value = params[key];
        if (value !== null && value !== undefined && value !== "") {
            url.searchParams.set(key, String(value));
        } else {
            url.searchParams.delete(key);
        }
    }

    window.history.replaceState(null, "", url.toString());
}

/**
 * @param {string} [href]
 */
export function readAdvancedFormParamsFromUrl(href) {
    const url = new URL(href || window.location.href);
    const params = {};

    for (const key of METADATA_FORM_URL_KEYS) {
        const value = url.searchParams.get(key);
        if (value !== null && value !== "") {
            params[key] = value;
        }
    }

    return metadataParamsToForm(params);
}

/**
 * Persist advanced search form values on the homepage URL.
 *
 * @param {object} formParams
 */
export function syncAdvancedFormBrowserUrl(formParams = {}) {
    const url = new URL(window.location.href);
    const mapped = metadataParamsFromForm(formParams);

    url.searchParams.set("tab", "advanced");
    url.searchParams.delete("scope");

    for (const key of METADATA_FORM_URL_KEYS) {
        const value = mapped[key];
        if (value !== null && value !== undefined && value !== "") {
            url.searchParams.set(key, String(value));
        } else {
            url.searchParams.delete(key);
        }
    }

    window.history.replaceState(null, "", url.toString());
}

/**
 * @param {object} params Form or API metadata params
 */
export function buildAdvancedFormPath(params = {}) {
    const search = new URLSearchParams();
    const mapped = metadataParamsFromForm(params);

    search.set("tab", "advanced");

    for (const key of METADATA_FORM_URL_KEYS) {
        const value = mapped[key];
        if (value !== null && value !== undefined && value !== "") {
            search.set(key, String(value));
        }
    }

    return `/?${search.toString()}`;
}

/**
 * Compound / structure search.
 *
 * @param {object} options
 * @param {string} [options.query]
 * @param {string} [options.type]
 * @param {string|null} [options.tagType]
 * @param {number} [options.limit]
 * @param {number} [options.page]
 * @param {string|null} [options.sort]
 */
export async function fetchCompoundSearch({
    query = "",
    type = null,
    tagType = null,
    limit = 24,
    page = 1,
    sort = null,
} = {}) {
    let url = `/api/v1/search/compounds?limit=${limit}&page=${page}`;

    if (sort) {
        url += `&sort=${encodeURIComponent(sort)}`;
    }

    const { data } = await axios.post(url, {
        query,
        type,
        tagType,
    });

    return data;
}

/**
 * @param {string} scope
 * @param {object} params
 */
export function buildSearchPagePath(scope, params = {}) {
    const search = new URLSearchParams();
    search.set("scope", scope);

    if (scope === SEARCH_SCOPE.CATALOG) {
        if (params.q) {
            search.set("q", params.q);
        }
        if (params.per_page && params.per_page !== 12) {
            search.set("per_page", String(params.per_page));
        }
        if (params.projects_page > 1) {
            search.set("projects_page", String(params.projects_page));
        }
        if (params.studies_page > 1) {
            search.set("studies_page", String(params.studies_page));
        }
        if (params.datasets_page > 1) {
            search.set("datasets_page", String(params.datasets_page));
        }
    } else if (scope === SEARCH_SCOPE.COMPOUNDS) {
        if (params.query) {
            search.set("query", params.query);
        }
        if (params.type) {
            search.set("type", params.type);
        }
        if (params.sort) {
            search.set("sort", params.sort);
        }
        if (params.page > 1) {
            search.set("page", String(params.page));
        }
        if (params.limit && params.limit !== 24) {
            search.set("limit", String(params.limit));
        }
        if (params.tagType) {
            search.set("tagType", params.tagType);
        }
    } else if (scope === SEARCH_SCOPE.METADATA) {
        const mapped = metadataParamsFromForm(params);

        for (const [key, value] of Object.entries(mapped)) {
            if (value !== null && value !== undefined && value !== "") {
                search.set(key, String(value));
            }
        }

        if (params.per_page && params.per_page !== 12) {
            search.set("per_page", String(params.per_page));
        }
        if (params.studies_page > 1) {
            search.set("studies_page", String(params.studies_page));
        }
        if (params.datasets_page > 1) {
            search.set("datasets_page", String(params.datasets_page));
        }
    }

    const query = search.toString();

    return query ? `/search?${query}` : "/search";
}

/**
 * @param {string} [href]
 */
export function readCatalogParamsFromUrl(href) {
    const url = new URL(href || window.location.href);

    const perPage = parseInt(url.searchParams.get("per_page") || "12", 10);
    const fallbackPage = parseInt(url.searchParams.get("page") || "1", 10);

    return {
        scope: url.searchParams.get("scope") || SEARCH_SCOPE.CATALOG,
        q: url.searchParams.get("q") || "",
        per_page: Number.isFinite(perPage) ? perPage : 12,
        projects_page: parseInt(
            url.searchParams.get("projects_page") || String(fallbackPage),
            10
        ),
        studies_page: parseInt(
            url.searchParams.get("studies_page") || String(fallbackPage),
            10
        ),
        datasets_page: parseInt(
            url.searchParams.get("datasets_page") || String(fallbackPage),
            10
        ),
    };
}

/**
 * @param {object} params
 */
export function syncCatalogBrowserUrl(params) {
    const url = new URL(window.location.href);

    url.pathname = "/search";
    url.searchParams.set("scope", SEARCH_SCOPE.CATALOG);

    if (params.q) {
        url.searchParams.set("q", params.q);
    } else {
        url.searchParams.delete("q");
    }

    url.searchParams.delete("query");
    url.searchParams.delete("type");
    url.searchParams.delete("page");

    const setPageParam = (key, page) => {
        if (page > 1) {
            url.searchParams.set(key, String(page));
        } else {
            url.searchParams.delete(key);
        }
    };

    setPageParam("projects_page", params.projects_page);
    setPageParam("studies_page", params.studies_page);
    setPageParam("datasets_page", params.datasets_page);

    if (params.per_page && params.per_page !== 12) {
        url.searchParams.set("per_page", String(params.per_page));
    } else {
        url.searchParams.delete("per_page");
    }

    window.history.replaceState(null, "", url.toString());
}

/**
 * @param {string} queryTerm
 * @param {number} page
 * @param {string|null} sort
 * @param {string|null} type
 */
export function syncCompoundsBrowserUrl(queryTerm, page, sort, type) {
    const url = new URL(window.location.href);

    url.pathname = "/search";
    url.searchParams.set("scope", SEARCH_SCOPE.COMPOUNDS);

    if (queryTerm) {
        url.searchParams.set("query", queryTerm);
    } else {
        url.searchParams.delete("query");
    }

    url.searchParams.delete("q");

    if (page > 1) {
        url.searchParams.set("page", String(page));
    } else {
        url.searchParams.delete("page");
    }

    if (sort) {
        url.searchParams.set("sort", sort);
    } else {
        url.searchParams.delete("sort");
    }

    if (type) {
        url.searchParams.set("type", type);
    } else {
        url.searchParams.delete("type");
    }

    window.history.replaceState(null, "", url.toString());
}
