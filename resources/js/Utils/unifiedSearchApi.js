import axios from "axios";

/** @readonly */
export const SEARCH_SCOPE = {
    CATALOG: "catalog",
    COMPOUNDS: "compounds",
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
