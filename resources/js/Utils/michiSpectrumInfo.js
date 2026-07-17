/**
 * MIChI v1 fields with NMRium mapping (MARGARITAS spreadsheet / nmrXiv-mapping tab).
 *
 * @see https://nfdi4chem.github.io/workshops/docs/michi/tabular/nmr/v1/table
 */

/** @type {readonly string[]} */
export const MICHI_CONSUMED_INFO_KEYS = [
    "solvent",
    "nucleus",
    "baseFrequency",
    "originFrequency",
    "experiment",
    "relaxationTime",
    "numberOfPoints",
    "temperature",
    "numberOfScans",
    "acquisitionTime",
    "probeName",
    "phc0",
    "phc1",
];

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function isEmpty(value) {
    if (value === null || value === undefined || value === "") {
        return true;
    }

    if (Array.isArray(value) && value.length === 0) {
        return true;
    }

    return false;
}

/**
 * @param {unknown} value
 * @param {string|null} unit
 * @returns {string}
 */
function formatWithUnit(value, unit) {
    const text = Array.isArray(value)
        ? value.map((entry) => String(entry)).join(", ")
        : String(value);

    if (!unit) {
        return text;
    }

    return `${text} ${unit}`;
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
function formatBoolean(value) {
    if (value === true || value === "true" || value === "TRUE") {
        return "TRUE";
    }

    if (value === false || value === "false" || value === "FALSE") {
        return "FALSE";
    }

    return null;
}

/**
 * @param {{ filters?: Array<{ name?: string, value?: unknown }> }|null|undefined} spectrum
 * @param {string} name
 * @returns {unknown}
 */
function findFilterValue(spectrum, name) {
    const filters = spectrum?.filters;
    if (!Array.isArray(filters)) {
        return null;
    }

    const filter = filters.find((entry) => entry?.name === name);

    return filter?.value ?? null;
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
function formatApodizationFunctions(value) {
    if (!value || typeof value !== "object") {
        return null;
    }

    const names = Object.entries(value)
        .filter(
            ([, entryValue]) => typeof entryValue === "number" && entryValue > 0
        )
        .map(([key]) => key);

    return names.length > 0 ? names.join(", ") : null;
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
function formatApodizationParameters(value) {
    if (!value || typeof value !== "object") {
        return null;
    }

    const pairs = Object.entries(value)
        .filter(
            ([, entryValue]) => typeof entryValue === "number" && entryValue > 0
        )
        .map(([key, entryValue]) => `${key}: ${entryValue}`);

    return pairs.length > 0 ? pairs.join("; ") : null;
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
function formatBaselineCorrectionParameters(value) {
    if (!value || typeof value !== "object") {
        return null;
    }

    const pairs = Object.entries(value)
        .filter(([key]) => key !== "algorithm")
        .filter(([, entryValue]) => !isEmpty(entryValue))
        .map(([key, entryValue]) => {
            if (entryValue !== null && typeof entryValue === "object") {
                return `${key}: ${JSON.stringify(entryValue)}`;
            }

            return `${key}: ${entryValue}`;
        });

    return pairs.length > 0 ? pairs.join("; ") : null;
}

/**
 * @typedef {{ id: string, label: string, extract: (spectrum: object) => string|null }} MichiRowDefinition
 */

/** @type {MichiRowDefinition[]} */
const MICHI_ROW_DEFINITIONS = [
    {
        id: "solvent",
        label: "NMR Solvent",
        extract(spectrum) {
            const value = spectrum?.info?.solvent;

            return isEmpty(value) ? null : formatWithUnit(value, null);
        },
    },
    {
        id: "nucleus",
        label: "Acquisition Nucleus",
        extract(spectrum) {
            const value = spectrum?.info?.nucleus;

            return isEmpty(value) ? null : formatWithUnit(value, null);
        },
    },
    {
        id: "irradiation_frequency",
        label: "Irradiation Frequency",
        extract(spectrum) {
            const info = spectrum?.info ?? {};
            const value = !isEmpty(info.baseFrequency)
                ? info.baseFrequency
                : info.originFrequency;

            return isEmpty(value) ? null : formatWithUnit(value, "MHz");
        },
    },
    {
        id: "experiment_type",
        label: "NMR Method",
        extract(spectrum) {
            const value = spectrum?.info?.experiment;

            return isEmpty(value) ? null : formatWithUnit(value, null);
        },
    },
    {
        id: "relaxation_delay",
        label: "Relaxation Delay",
        extract(spectrum) {
            const value = spectrum?.info?.relaxationTime;

            return isEmpty(value) ? null : formatWithUnit(value, "s");
        },
    },
    {
        id: "acquisition_data_points",
        label: "Number of Acquisition Data Points",
        extract(spectrum) {
            const value = spectrum?.info?.numberOfPoints;

            return isEmpty(value) ? null : formatWithUnit(value, "data points");
        },
    },
    {
        id: "temperature",
        label: "Sample Temperature Information",
        extract(spectrum) {
            const value = spectrum?.info?.temperature;

            return isEmpty(value) ? null : formatWithUnit(value, "K");
        },
    },
    {
        id: "scans",
        label: "Number of Scans",
        extract(spectrum) {
            const value = spectrum?.info?.numberOfScans;

            return isEmpty(value) ? null : formatWithUnit(value, null);
        },
    },
    {
        id: "acquisition_time",
        label: "Acquisition Time",
        extract(spectrum) {
            const value = spectrum?.info?.acquisitionTime;

            return isEmpty(value) ? null : formatWithUnit(value, "s");
        },
    },
    {
        id: "probe",
        label: "NMR Probe",
        extract(spectrum) {
            const value = spectrum?.info?.probeName;

            return isEmpty(value) ? null : formatWithUnit(value, null);
        },
    },
    {
        id: "ph0",
        label: "Zero Order Phase Correction (ph0)",
        extract(spectrum) {
            const value = spectrum?.info?.phc0;

            return isEmpty(value) ? null : formatWithUnit(value, "°");
        },
    },
    {
        id: "ph1",
        label: "First Order Phase Correction (ph1)",
        extract(spectrum) {
            const value = spectrum?.info?.phc1;

            return isEmpty(value) ? null : formatWithUnit(value, "°");
        },
    },
    {
        id: "zero_filling_points",
        label: "Number of Zero Filling Points",
        extract(spectrum) {
            const value = findFilterValue(spectrum, "zeroFilling")?.nbPoints;

            return isEmpty(value) ? null : formatWithUnit(value, "data points");
        },
    },
    {
        id: "apodization_function",
        label: "Window Function for Apodization",
        extract(spectrum) {
            return formatApodizationFunctions(
                findFilterValue(spectrum, "apodization")
            );
        },
    },
    {
        id: "apodization_parameters",
        label: "Window Function Parameters",
        extract(spectrum) {
            return formatApodizationParameters(
                findFilterValue(spectrum, "apodization")
            );
        },
    },
    {
        id: "baseline_correction",
        label: "Baseline Correction",
        extract(spectrum) {
            const value = findFilterValue(spectrum, "baselineCorrection");

            if (!value || typeof value !== "object") {
                return null;
            }

            const algorithm = value.algorithm;

            return isEmpty(algorithm) ? null : String(algorithm);
        },
    },
    {
        id: "baseline_correction_parameters",
        label: "Baseline Correction Parameters",
        extract(spectrum) {
            return formatBaselineCorrectionParameters(
                findFilterValue(spectrum, "baselineCorrection")
            );
        },
    },
    {
        id: "absolute_correction",
        label: "Usage of Absolute Correction",
        extract(spectrum) {
            const value = findFilterValue(spectrum, "phaseCorrection");

            if (!value || typeof value !== "object" || !("absolute" in value)) {
                return null;
            }

            return formatBoolean(value.absolute);
        },
    },
    {
        id: "phase_correction",
        label: "Phase Correction",
        extract(spectrum) {
            const value = findFilterValue(spectrum, "phaseCorrection");

            if (!value || typeof value !== "object") {
                return null;
            }

            if (!isEmpty(value.algorithm)) {
                return String(value.algorithm);
            }

            return "manual";
        },
    },
];

/**
 * @param {{ info?: Record<string, unknown>, filters?: Array<{ name?: string, value?: unknown }> }|null|undefined} spectrum
 * @returns {Array<{ key: string, label: string, value: string, source: 'michi' }>}
 */
export function extractMichiRows(spectrum) {
    if (!spectrum || typeof spectrum !== "object") {
        return [];
    }

    return MICHI_ROW_DEFINITIONS.map((row) => {
        const value = row.extract(spectrum);

        if (isEmpty(value)) {
            return null;
        }

        return {
            key: `michi:${row.id}`,
            label: row.label,
            value: String(value),
            source: "michi",
        };
    }).filter(Boolean);
}
