import { SEARCH_SCOPE, buildSearchPagePath } from "@/Utils/unifiedSearchApi.js";

/**
 * Map a stats distribution bucket to metadata search query params.
 *
 * @param {string} distributionKey
 * @param {string} value
 * @returns {Record<string, string>|null}
 */
export function statsLegendSearchParams(distributionKey, value) {
    if (value === null || value === undefined || value === "") {
        return null;
    }

    if (String(value) === "Other") {
        return null;
    }

    switch (distributionKey) {
        case "dimension":
            if (value === "1D") {
                return { q: "dimension 1" };
            }

            if (value === "2D") {
                return { q: "dimension 2" };
            }

            return null;
        case "nucleus":
            return { nucleus: String(value) };
        case "solvent":
            return { solvent: String(value) };
        case "experiment":
            return { nmr_method: String(value) };
        case "measuring_frequency_mhz":
            return { proton_frequency: String(value) };
        case "manufacturer":
            return { manufacturer: String(value) };
        case "temperature_k":
            return { temperature: String(value) };
        case "pulse_sequence":
            return { pulse_sequence: String(value) };
        case "tube_diameter_mm":
            return { tube_diameter: String(value) };
        case "number_of_scans":
            return { number_of_scans: String(value) };
        case "instrument_model":
            return { instrument_model: String(value) };
        default:
            return null;
    }
}

/**
 * @param {string} distributionKey
 * @param {string} value
 * @returns {string|null}
 */
export function buildStatsLegendSearchPath(distributionKey, value) {
    const params = statsLegendSearchParams(distributionKey, value);

    if (!params) {
        return null;
    }

    return buildSearchPagePath(SEARCH_SCOPE.METADATA, params);
}

/**
 * @param {string} nucleus
 * @param {string} frequency
 * @returns {string}
 */
export function buildNucleusFrequencySearchPath(nucleus, frequency) {
    return buildSearchPagePath(SEARCH_SCOPE.METADATA, {
        nucleus: String(nucleus),
        proton_frequency: String(frequency),
    });
}

/**
 * @param {string} dimension
 * @param {string} nucleus
 * @returns {string}
 */
export function buildDimensionNucleusSearchPath(dimension, nucleus) {
    const dimensionQuery = dimension === "1D" ? "dimension 1" : "dimension 2";

    return buildSearchPagePath(SEARCH_SCOPE.METADATA, {
        q: dimensionQuery,
        nucleus: String(nucleus),
    });
}

/**
 * @param {string} dimension
 * @param {string} experiment
 * @returns {string}
 */
export function buildDimensionExperimentSearchPath(dimension, experiment) {
    const dimensionQuery = dimension === "1D" ? "dimension 1" : "dimension 2";

    return buildSearchPagePath(SEARCH_SCOPE.METADATA, {
        q: dimensionQuery,
        nmr_method: String(experiment),
    });
}
