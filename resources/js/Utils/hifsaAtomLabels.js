/**
 * Cosmic Truth / HiFSA atom label helpers (H14, C10,C11, coupling pairs).
 */

/**
 * @typedef {{ element: string, serial: number, suffix: string|null, raw: string }} HifsaAtom
 */

/**
 * @param {string|null|undefined} label
 * @returns {HifsaAtom|null}
 */
export function parseAtom(label) {
    if (label == null) {
        return null;
    }

    const raw = String(label).trim();

    if (!raw) {
        return null;
    }

    const match = raw.match(/^([A-Za-z]{1,2})(\d+)([A-Za-z]?)$/);

    if (!match) {
        return null;
    }

    return {
        element: match[1].toUpperCase(),
        serial: Number.parseInt(match[2], 10),
        suffix: match[3] ? match[3].toLowerCase() : null,
        raw,
    };
}

/**
 * @param {string|null|undefined} name
 * @returns {HifsaAtom[]}
 */
export function parseGroup(name) {
    if (name == null || String(name).trim() === "") {
        return [];
    }

    return String(name)
        .split(/\s*,\s*/)
        .map((part) => parseAtom(part))
        .filter(Boolean);
}

/**
 * Zip from/to Cosmic Truth groups for coupling arrows.
 * Unequal lengths zip only min(len) pairs (no clamping onto the last atom).
 *
 * @param {string|null|undefined} from
 * @param {string|null|undefined} to
 * @returns {{ from: HifsaAtom, to: HifsaAtom }[]}
 */
export function pairCoupling(from, to) {
    const fromAtoms = parseGroup(from);
    const toAtoms = parseGroup(to);

    if (!fromAtoms.length || !toAtoms.length) {
        return [];
    }

    // Geminal / same-group couplings like H28,H29 → H28,H29 should connect
    // the two partners, not zip identical endpoints onto themselves.
    if (
        fromAtoms.length === toAtoms.length &&
        fromAtoms.length >= 2 &&
        fromAtoms.every(
            (atom, index) =>
                atom.raw === toAtoms[index].raw &&
                atom.serial === toAtoms[index].serial &&
                atom.element === toAtoms[index].element
        )
    ) {
        const pairs = [];

        for (let i = 0; i < fromAtoms.length - 1; i++) {
            pairs.push({
                from: fromAtoms[i],
                to: fromAtoms[i + 1],
            });
        }

        return pairs;
    }

    const count = Math.min(fromAtoms.length, toAtoms.length);
    const pairs = [];

    for (let i = 0; i < count; i++) {
        pairs.push({
            from: fromAtoms[i],
            to: toAtoms[i],
        });
    }

    return pairs;
}

/**
 * Pick a study molecule only when InChIKey matches the spin system.
 * Never fall back to "first SDF" (wrong enantiomer / compound risk).
 *
 * @param {Array<object>|null|undefined} molecules
 * @param {object|null|undefined} spinSystem
 * @returns {object|null}
 */
export function resolveMolecule(molecules, spinSystem = null) {
    const list = Array.isArray(molecules) ? molecules.filter(Boolean) : [];

    if (!list.length) {
        return null;
    }

    const inchiKey = spinSystem?.inchi_key || null;

    if (typeof inchiKey !== "string" || inchiKey === "") {
        return null;
    }

    return (
        list.find((molecule) => {
            const candidate =
                molecule.inchi_key || molecule.standard_inchi_key || "";
            const sdf = molecule.sdf;

            return (
                typeof candidate === "string" &&
                candidate.toLowerCase() === inchiKey.toLowerCase() &&
                typeof sdf === "string" &&
                sdf.trim() !== ""
            );
        }) || null
    );
}

/**
 * Whether a spin-system group should show a structure viewer (solute with SDF).
 *
 * @param {string} groupName
 * @param {Array<object>} spinsystems
 * @returns {boolean}
 */
export function isSoluteSpinSystem(groupName, spinsystems = []) {
    const systems = Array.isArray(spinsystems) ? spinsystems : [];
    const match = systems.find(
        (row) => (row?.name || "") === groupName && row?.ss_type
    );

    if (match) {
        return String(match.ss_type).toLowerCase() === "solute";
    }

    // Fallback: Cosmic Truth often names the solute after the SDF file.
    return /\.sdf$/i.test(groupName || "");
}

/**
 * Cosmic Truth uses large negative sentinels (e.g. -1e12) for unset shifts,
 * commonly on oxygens and other nuclei without a fitted δ.
 *
 * @param {unknown} value
 * @returns {value is number}
 */
export function isDisplayableShiftPpm(value) {
    return (
        typeof value === "number" &&
        Number.isFinite(value) &&
        Math.abs(value) < 1000
    );
}

/**
 * True when every atom label is present in the Cosmic Truth atom map.
 *
 * @param {HifsaAtom[]} atoms
 * @param {Record<string, number>|null|undefined} atomMap
 * @returns {boolean}
 */
export function groupIsFullyMapped(atoms, atomMap) {
    if (!Array.isArray(atoms) || !atoms.length) {
        return false;
    }

    if (!atomMap || typeof atomMap !== "object") {
        return false;
    }

    return atoms.every((atom) => {
        const index = Number(atomMap[atom.raw]);

        return Number.isFinite(index) && index >= 1;
    });
}

/**
 * Format δ ppm for labels.
 *
 * @param {number|null|undefined} value
 * @param {number} digits
 * @returns {string|null}
 */
export function formatShiftPpm(value, digits = 4) {
    if (!isDisplayableShiftPpm(value)) {
        return null;
    }

    return value.toFixed(digits);
}

/**
 * Format J Hz for labels.
 *
 * @param {number|null|undefined} value
 * @param {number} digits
 * @returns {string|null}
 */
export function formatCouplingHz(value, digits = 3) {
    if (
        typeof value !== "number" ||
        !Number.isFinite(value) ||
        Math.abs(value) >= 1e6
    ) {
        return null;
    }

    return value.toFixed(digits);
}

/**
 * Build shift overlay specs for the 3D viewer.
 *
 * @param {Array<object>} rows
 * @param {Record<string, number>|null|undefined} atomMap
 * @returns {{ rowIndex: number, atoms: HifsaAtom[], text: string, label: string }[]}
 */
export function buildShiftOverlays(rows, atomMap = null) {
    if (!Array.isArray(rows)) {
        return [];
    }

    return rows
        .map((row, rowIndex) => {
            const atoms = parseGroup(row?.name);
            const ppm = formatShiftPpm(row?.shift);
            const label = row?.name || "";

            if (!atoms.length || !ppm || !groupIsFullyMapped(atoms, atomMap)) {
                return null;
            }

            return {
                rowIndex,
                atoms,
                label,
                text: `${label}  ${ppm} ppm`,
            };
        })
        .filter(Boolean);
}

/**
 * Build coupling overlay specs (arrows + midpoint J labels).
 *
 * @param {Array<object>} rows
 * @param {Record<string, number>|null|undefined} atomMap
 * @returns {{ rowIndex: number, pairs: { from: HifsaAtom, to: HifsaAtom }[], text: string, label: string }[]}
 */
export function buildCouplingOverlays(rows, atomMap = null) {
    if (!Array.isArray(rows)) {
        return [];
    }

    return rows
        .map((row, rowIndex) => {
            const pairs = pairCoupling(row?.shift_from, row?.shift_to);
            const hz = formatCouplingHz(row?.coupling, 3);
            const label = row?.name || "";

            if (!pairs.length || !hz) {
                return null;
            }

            const allAtoms = pairs.flatMap((pair) => [pair.from, pair.to]);

            if (!groupIsFullyMapped(allAtoms, atomMap)) {
                return null;
            }

            return {
                rowIndex,
                pairs,
                label,
                text: `${hz} Hz`,
            };
        })
        .filter(Boolean);
}

/**
 * Whether a chemical-shift or coupling table row can be drawn on the CT structure.
 *
 * @param {object} row
 * @param {"shifts"|"couplings"} mode
 * @param {Record<string, number>|null|undefined} atomMap
 * @returns {boolean}
 */
export function rowIsDrawable(row, mode, atomMap) {
    if (!atomMap || typeof atomMap !== "object") {
        return false;
    }

    if (mode === "couplings") {
        const pairs = pairCoupling(row?.shift_from, row?.shift_to);

        if (!pairs.length || !formatCouplingHz(row?.coupling, 3)) {
            return false;
        }

        return groupIsFullyMapped(
            pairs.flatMap((pair) => [pair.from, pair.to]),
            atomMap
        );
    }

    const atoms = parseGroup(row?.name);

    return (
        atoms.length > 0 &&
        Boolean(formatShiftPpm(row?.shift)) &&
        groupIsFullyMapped(atoms, atomMap)
    );
}
