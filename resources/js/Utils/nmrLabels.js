const SUPERSCRIPT_DIGITS = "⁰¹²³⁴⁵⁶⁷⁸⁹";
const SUBSCRIPT_DIGITS = "₀₁₂₃₄₅₆₇₈₉";

export const NMR_NUCLEUS_LABELS = {
    "1H": "¹H",
    "13C": "¹³C",
    "15N": "¹⁵N",
    "19F": "¹⁹F",
    "31P": "³¹P",
};

export function formatNmrIsotopeLabel(value) {
    if (value === null || value === undefined || value === "") {
        return "";
    }

    const normalized = String(value).trim();
    const match = normalized.match(/^(\d+)([A-Za-z][a-z]?)$/);

    if (!match) {
        return normalized;
    }

    const massNumber = match[1];
    const element =
        match[2].charAt(0).toUpperCase() + match[2].slice(1).toLowerCase();
    const key = `${massNumber}${element}`;

    if (NMR_NUCLEUS_LABELS[key]) {
        return NMR_NUCLEUS_LABELS[key];
    }

    const superscript = massNumber
        .split("")
        .map((digit) => SUPERSCRIPT_DIGITS[Number(digit)] ?? digit)
        .join("");

    return `${superscript}${element}`;
}

export function formatMeasuringFrequencyLabel(value) {
    if (value === null || value === undefined || value === "") {
        return "";
    }

    const normalized = String(value).trim();

    if (normalized.toLowerCase().endsWith("mhz")) {
        return normalized.replace(/mhz$/i, "MHz");
    }

    if (normalized.toLowerCase().endsWith("hz")) {
        return normalized.replace(/hz$/i, "Hz");
    }

    return `${normalized} MHz`;
}

export function formatSolventLabel(value) {
    if (value === null || value === undefined || value === "") {
        return "";
    }

    return String(value).replace(
        /\d/g,
        (digit) => SUBSCRIPT_DIGITS[Number(digit)] ?? digit
    );
}

export function formatTemperatureLabel(value) {
    if (value === null || value === undefined || value === "") {
        return "";
    }

    const normalized = String(value).trim();

    if (normalized.toLowerCase().endsWith("k")) {
        return normalized.replace(/k$/i, " K");
    }

    return `${normalized} K`;
}

export function formatDistributionLabel(value, format = "default") {
    if (format === "nucleus") {
        return formatNmrIsotopeLabel(value);
    }

    if (format === "measuring_frequency") {
        return formatMeasuringFrequencyLabel(value);
    }

    if (format === "solvent") {
        return formatSolventLabel(value);
    }

    if (format === "temperature") {
        return formatTemperatureLabel(value);
    }

    return value === null || value === undefined ? "" : String(value);
}
