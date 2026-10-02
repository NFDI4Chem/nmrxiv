/**
 * Peak table model for the spectra search.
 *
 * Rows are encoded in URLs and API calls with the compact form understood by
 * App\Support\Search\SpectrumQueryParser, e.g. "3.75:3H:s;5.0-5.5;!9.5-10.5;?7.26"
 * (a region is "from-to", "!" means must not have, "?" means nice to have).
 */

export const NUCLEI = ["1H", "13C"];

export const NUCLEUS_LABELS = { "1H": "¹H", "13C": "¹³C" };

export const PLAUSIBLE_RANGE = { "1H": [-2, 16], "13C": [-20, 250] };

export const CLOSENESS = {
    strict: { label: "Strict", "1H": 0.02, "13C": 0.5 },
    normal: { label: "Normal", "1H": 0.05, "13C": 1.0 },
    relaxed: { label: "Relaxed", "1H": 0.1, "13C": 2.0 },
};

export const RULES = [
    { value: "must", label: "Must have" },
    { value: "nice", label: "Nice to have" },
    { value: "not", label: "Must not have" },
];

export const SHAPES = [
    { value: "", label: "Any" },
    { value: "s", label: "s (singlet)" },
    { value: "d", label: "d (doublet)" },
    { value: "t", label: "t (triplet)" },
    { value: "q", label: "q (quartet)" },
    { value: "p", label: "p (quintet)" },
    { value: "dd", label: "dd" },
    { value: "dt", label: "dt" },
    { value: "td", label: "td" },
    { value: "ddd", label: "ddd" },
    { value: "m", label: "m (multiplet)" },
];

export const SOLVENTS = [
    "CDCl3",
    "DMSO-d6",
    "CD3OD",
    "D2O",
    "Acetone-d6",
    "C6D6",
    "CD3CN",
    "THF-d8",
    "Toluene-d8",
    "Pyridine-d5",
];

const RULE_PREFIX = { must: "", nice: "?", not: "!" };
const NUMBER = "-?(?:\\d+(?:\\.\\d*)?|\\.\\d+)";
const POSITION_PATTERN = new RegExp(
    `^(${NUMBER})(?:\\s*[-–]\\s*(${NUMBER}))?$`
);
const TOKEN_PATTERN = new RegExp(
    `^([!?+])?(${NUMBER}(?:[-–]${NUMBER})?)((?::[^:]*)*)$`
);
const SHAPE_VALUES = new Set(
    SHAPES.map((shape) => shape.value).filter(Boolean)
);

let nextRowId = 1;

/**
 * @typedef {object} PeakRow
 * @property {number} id
 * @property {string} position "3.75" or a region "5.0-5.5"
 * @property {string} protons
 * @property {string} shape
 * @property {"must"|"nice"|"not"} rule
 */

/** @returns {PeakRow} */
export function newRow(overrides = {}) {
    return {
        id: nextRowId++,
        position: "",
        protons: "",
        shape: "",
        rule: "must",
        ...overrides,
    };
}

export function emptyQueryForm() {
    return {
        peaks: { "1H": [], "13C": [] },
        mode: "contains",
        closeness: "normal",
        solvent: "",
        sameSolvent: false,
        ignoreSolventPeaks: true,
        allowOffset: true,
    };
}

/**
 * @param {string} position
 * @returns {{from: number, to: number}|null|false} null when empty, false when not a number
 */
export function parsePosition(position) {
    const text = String(position ?? "")
        .trim()
        .replace(/,/g, ".");

    if (text === "") {
        return null;
    }

    const match = text.match(POSITION_PATTERN);
    if (!match) {
        return false;
    }

    const first = parseFloat(match[1]);
    const second = match[2] !== undefined ? parseFloat(match[2]) : first;

    return { from: Math.min(first, second), to: Math.max(first, second) };
}

/**
 * The problem with a row, in plain words, or null when it is fine or empty.
 *
 * @param {PeakRow} row
 * @param {string} nucleus
 */
export function rowError(row, nucleus) {
    const position = parsePosition(row.position);

    if (position === null) {
        return null;
    }
    if (position === false) {
        return `“${row.position}” is not a number or a region like 5.0-5.5`;
    }

    const [min, max] = PLAUSIBLE_RANGE[nucleus];
    if (position.from < min || position.to > max) {
        return `${NUCLEUS_LABELS[nucleus]} peaks must be between ${min} and ${max} ppm`;
    }

    if (nucleus === "1H" && String(row.protons ?? "").trim() !== "") {
        const protons = Number(String(row.protons).replace(/h$/i, ""));
        if (!(protons > 0)) {
            return "Protons must be a number, like 3";
        }
    }

    return null;
}

/**
 * @param {PeakRow[]} rows
 * @param {string} nucleus
 */
export function encodeRows(rows, nucleus) {
    return rows
        .filter((row) => parsePosition(row.position) && !rowError(row, nucleus))
        .map((row) => {
            const { from, to } = parsePosition(row.position);
            let token = `${RULE_PREFIX[row.rule] ?? ""}${formatShift(from)}`;
            if (to > from) {
                token += `-${formatShift(to)}`;
            }

            if (nucleus === "1H") {
                const protons = String(row.protons ?? "")
                    .trim()
                    .replace(/h$/i, "");
                if (protons !== "") {
                    token += `:${Number(protons)}H`;
                }
                if (row.shape) {
                    token += `:${row.shape}`;
                }
            }

            return token;
        })
        .join(";");
}

/**
 * @param {string} encoded
 * @returns {PeakRow[]}
 */
export function decodeRows(encoded) {
    return String(encoded ?? "")
        .split(";")
        .map((token) => token.trim())
        .filter(Boolean)
        .map((token) => {
            const match = token.match(TOKEN_PATTERN);
            if (!match) {
                return newRow({ position: token });
            }

            const row = newRow({
                position: match[2].replace("–", "-"),
                rule:
                    match[1] === "!"
                        ? "not"
                        : match[1] === "?"
                        ? "nice"
                        : "must",
            });
            for (const detail of match[3].split(":").filter(Boolean)) {
                const protons = detail.match(/^(\d+(?:\.\d+)?)\s*H$/i);
                if (protons) {
                    row.protons = protons[1];
                } else if (SHAPE_VALUES.has(detail.toLowerCase())) {
                    row.shape = detail.toLowerCase();
                }
            }

            return row;
        });
}

/**
 * Rows from API peaks ({from, to, rule, protons, shape}).
 *
 * @param {Array<object>} peaks
 * @returns {PeakRow[]}
 */
export function rowsFromApiPeaks(peaks = []) {
    return peaks.map((peak) =>
        newRow({
            position:
                peak.to > peak.from
                    ? `${formatShift(peak.from)}-${formatShift(peak.to)}`
                    : formatShift(peak.from),
            protons: peak.protons ? String(peak.protons) : "",
            shape: SHAPE_VALUES.has(peak.shape) ? peak.shape : "",
            rule: peak.rule ?? "must",
        })
    );
}

/**
 * "Nice to have" rows from a detected spectrum, without solvent and water peaks.
 *
 * @param {{nucleus: string, signals: Array<{shift: number, multiplicity: ?string, is_solvent: boolean}>}} spectrum
 * @returns {PeakRow[]}
 */
export function rowsFromDetectedSpectrum(spectrum) {
    const digits = spectrum.nucleus === "1H" ? 2 : 1;

    return spectrum.signals
        .filter((signal) => !signal.is_solvent)
        .sort((a, b) => b.shift - a.shift)
        .map((signal) =>
            newRow({
                position: signal.shift.toFixed(digits),
                shape:
                    spectrum.nucleus === "1H" &&
                    SHAPE_VALUES.has(signal.multiplicity)
                        ? signal.multiplicity
                        : "",
                rule: "nice",
            })
        );
}

/**
 * Whether the form has at least one valid peak to look for.
 */
export function canSearch(form) {
    const peakRows = NUCLEI.flatMap((nucleus) =>
        form.peaks[nucleus].map((row) => ({ row, nucleus }))
    );

    return (
        peakRows.every(({ row, nucleus }) => !rowError(row, nucleus)) &&
        peakRows.some(
            ({ row }) => row.rule !== "not" && parsePosition(row.position)
        )
    );
}

/**
 * API / URL parameters for the form; defaults are left out.
 */
export function formToApiParams(form) {
    const peaks = {};
    for (const nucleus of NUCLEI) {
        const encoded = encodeRows(form.peaks[nucleus], nucleus);
        if (encoded) {
            peaks[nucleus] = encoded;
        }
    }

    const params = { peaks };
    if (form.mode !== "contains") {
        params.mode = form.mode;
    }
    if (form.closeness !== "normal") {
        params.closeness = form.closeness;
    }
    if (form.solvent) {
        params.solvent = form.solvent;
    }
    if (form.solvent && form.sameSolvent) {
        params.same_solvent = 1;
    }
    if (!form.ignoreSolventPeaks) {
        params.ignore_solvent_peaks = 0;
    }
    if (!form.allowOffset) {
        params.allow_offset = 0;
    }

    return params;
}

export function apiParamsToForm(params = {}) {
    const form = emptyQueryForm();
    for (const nucleus of NUCLEI) {
        form.peaks[nucleus] = decodeRows(params.peaks?.[nucleus]);
    }

    form.mode = params.mode === "whole" ? "whole" : "contains";
    form.closeness = CLOSENESS[params.closeness] ? params.closeness : "normal";
    form.solvent = params.solvent ?? "";
    form.sameSolvent = isTrue(params.same_solvent, false);
    form.ignoreSolventPeaks = isTrue(params.ignore_solvent_peaks, true);
    form.allowOffset = isTrue(params.allow_offset, true);

    return form;
}

/**
 * Plain summary of the peaks, e.g. "¹H 3.75, 5.0–5.5 · ¹³C 170.1".
 */
export function peaksSummary(params = {}) {
    return NUCLEI.filter((nucleus) => params.peaks?.[nucleus])
        .map((nucleus) => {
            const rows = decodeRows(params.peaks[nucleus]);
            const shown = rows
                .slice(0, 6)
                .map((row) =>
                    row.rule === "not"
                        ? `not ${row.position.replace("-", "–")}`
                        : row.position.replace("-", "–")
                )
                .join(", ");

            return `${NUCLEUS_LABELS[nucleus]} ${shown}${
                rows.length > 6 ? ` +${rows.length - 6} more` : ""
            }`;
        })
        .join(" · ");
}

export function formatShift(value) {
    return String(Math.round(Number(value) * 10000) / 10000);
}

function isTrue(value, fallback) {
    if (value === undefined || value === null || value === "") {
        return fallback;
    }

    return !["0", "false", "no", "off"].includes(String(value).toLowerCase());
}
