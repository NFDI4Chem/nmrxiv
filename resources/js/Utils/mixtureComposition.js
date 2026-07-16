/**
 * Normalize study molecule API responses (legacy array or structured payload).
 *
 * @param {object} sample
 * @param {Array|{ molecules?: Array, mixture_composition?: object|null }} payload
 */
export function applySampleMoleculeResponse(sample, payload) {
    if (!sample) {
        return;
    }

    const resolved =
        payload?.data &&
        typeof payload.data === "object" &&
        !Array.isArray(payload.data) &&
        (Array.isArray(payload.data.molecules) ||
            Object.prototype.hasOwnProperty.call(
                payload.data,
                "mixture_composition"
            ))
            ? payload.data
            : payload;

    if (Array.isArray(resolved)) {
        sample.molecules = resolved;
        return;
    }

    if (resolved && typeof resolved === "object") {
        if (Array.isArray(resolved.molecules)) {
            sample.molecules = resolved.molecules;
        }
        if (
            Object.prototype.hasOwnProperty.call(
                resolved,
                "mixture_composition"
            )
        ) {
            sample.mixture_composition = resolved.mixture_composition;
        }
    }
}

export const MIXTURE_BASIS_OPTIONS = [
    {
        value: "mole_percent",
        label: "mol %",
        displayLabel: "mole % (mol/mol)",
        helper: "NMR integrals give mole fractions directly, so mol % is preferred.",
    },
    {
        value: "weight_percent",
        label: "wt %",
        displayLabel: "weight % (wt/wt)",
        helper: "Mass fractions normalized to 100 wt %.",
    },
    {
        value: "volume_percent",
        label: "vol %",
        displayLabel: "volume % (vol/vol)",
        helper: "Volume fractions normalized to 100 vol %.",
    },
    {
        value: "molar_ratio",
        label: "molar ratio",
        displayLabel: "molar ratio",
        helper: "Relative molar amounts; values are not required to sum to 100.",
    },
];

export const MIXTURE_METHOD_OPTIONS = [
    { value: "qnmr", label: "qNMR" },
    { value: "gravimetric", label: "Gravimetric" },
    { value: "supplier_stated", label: "Supplier stated" },
    { value: "other", label: "Other" },
];

export const MIXTURE_NUCLEUS_OPTIONS = [
    { value: "1H", label: "¹H" },
    { value: "13C", label: "¹³C" },
    { value: "19F", label: "¹⁹F" },
    { value: "31P", label: "³¹P" },
];

export const MIXTURE_SUM_TOLERANCE = 0.5;

export function basisUnitLabel(basis) {
    return (
        MIXTURE_BASIS_OPTIONS.find((option) => option.value === basis)?.label ??
        "mol %"
    );
}

export function basisDisplayLabel(basis) {
    return (
        MIXTURE_BASIS_OPTIONS.find((option) => option.value === basis)
            ?.displayLabel ?? "mole % (mol/mol)"
    );
}

export function formatMixtureValue(value) {
    const n = Number(value);
    if (!Number.isFinite(n)) {
        return value != null && value !== "" ? String(value) : "0";
    }
    if (Math.abs(n - Math.round(n)) < 1e-9) {
        return String(Math.round(n));
    }

    return n.toFixed(3).replace(/\.?0+$/, "");
}

export function mixtureComponentSum(components) {
    if (!Array.isArray(components)) {
        return 0;
    }

    return components.reduce((total, component) => {
        const value = Number(component?.value);
        return total + (Number.isFinite(value) ? value : 0);
    }, 0);
}

export function mixtureExpectsNormalizedTotal(basis) {
    return basis !== "molar_ratio";
}

export function mixtureSumWarning(components, basis, hasResidual = false) {
    if (hasResidual || !mixtureExpectsNormalizedTotal(basis)) {
        return null;
    }

    const total = mixtureComponentSum(components);
    if (Math.abs(total - 100) <= MIXTURE_SUM_TOLERANCE) {
        return null;
    }

    return `Components sum to ${formatMixtureValue(total)} ${basisUnitLabel(
        basis
    )} — intentional (residual/unquantified) or a data-entry issue?`;
}

export function isMixtureSpectrumVerifiable(composition) {
    if (!composition?.basis) {
        return false;
    }

    const components = composition?.components ?? [];
    if (!components.length) {
        return false;
    }

    return components.every(
        (component) =>
            component?.integrated_signal != null &&
            String(component.integrated_signal).trim() !== ""
    );
}

export function mixtureComponentLabel(component) {
    const molecule = component?.molecule;
    return (
        molecule?.iupac_name ??
        molecule?.name ??
        molecule?.cas ??
        molecule?.canonical_smiles ??
        (molecule?.inchi_key
            ? `InChIKey: ${String(molecule.inchi_key).slice(0, 14)}…`
            : "Component")
    );
}

export function mixtureComponentInchiKey(component) {
    const molecule = component?.molecule;
    const key = molecule?.inchi_key ?? molecule?.standard_inchi_key;
    if (!key) {
        return null;
    }

    return String(key);
}

/**
 * @returns {'neutral'|'complete'|'warning'}
 */
export function mixtureTotalStatus(total, basis, hasResidual = false) {
    if (!mixtureExpectsNormalizedTotal(basis) || hasResidual) {
        return "neutral";
    }

    if (Math.abs(total - 100) <= MIXTURE_SUM_TOLERANCE) {
        return "complete";
    }

    return "warning";
}
