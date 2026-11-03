/** Half-width used when a neighbouring signal does not clamp the range. */
const RANGE_HALF_WIDTH_PPM = 0.025;
const ATOM_HIT_RADIUS = 14;
const ATOM_GLOW_RADIUS = 20;

const ATOM_GLOW_COLORS = {
    C: "#f5c518",
    H: "#5dade2",
    O: "#e74c3c",
    N: "#5b8def",
    S: "#e8c547",
    F: "#90ee90",
    Cl: "#90ee90",
    Br: "#c07040",
    I: "#b060c0",
    P: "#ff8c00",
};
const ATOM_GLOW_DEFAULT = "#f5c518";

/**
 * Group shifts that fall within `tolerance` ppm, matching NMRKit and the
 * calculation results payload.
 *
 * @param {{ atom: number, prediction: number }[]} shifts
 * @param {number} tolerance
 */
export function groupEquivalentShifts(shifts, tolerance = 0.001) {
    const groups = [];

    for (const shift of shifts) {
        const atom = Number(shift?.atom);
        const prediction = Number(shift?.prediction);
        if (
            !Number.isFinite(atom) ||
            atom < 1 ||
            !Number.isFinite(prediction)
        ) {
            continue;
        }

        const match = groups.find(
            (group) => Math.abs(group.prediction - prediction) < tolerance
        );
        if (match) {
            match.count += 1;
            match.atoms.push(atom);
        } else {
            groups.push({
                prediction,
                count: 1,
                atoms: [atom],
            });
        }
    }

    return groups;
}

/**
 * @param {{ x?: number[], re?: number[] }} data
 * @param {number} ppm
 */
function peakHeightAt(data, ppm) {
    const xs = data?.x;
    const intensities = data?.re;
    if (!Array.isArray(xs) || xs.length === 0 || !Array.isArray(intensities)) {
        return 0;
    }

    let bestIndex = 0;
    let bestDistance = Infinity;
    for (let index = 0; index < xs.length; index += 1) {
        const distance = Math.abs(Number(xs[index]) - ppm);
        if (distance < bestDistance) {
            bestDistance = distance;
            bestIndex = index;
        }
    }

    const height = Number(intensities[bestIndex]);
    return Number.isFinite(height) ? height : 0;
}

/**
 * @param {number[]} atoms
 */
function atomKey(atoms) {
    return [...atoms].sort((left, right) => left - right).join("-");
}

/**
 * nmrdb.org signals already name their atoms, 0-based. Keep those ranges and
 * point each atom at the signal and range ids.
 *
 * @param {object} spectrum
 * @param {Map<number, { nucleus: string, atoms: number[], highlightIds: string[] }>} atomPeaks
 */
function linkExistingSignalAtoms(spectrum, atomPeaks) {
    const nucleus = spectrum?.info?.nucleus;
    const ranges = spectrum?.ranges?.values;
    if (typeof nucleus !== "string" || !Array.isArray(ranges)) {
        return false;
    }

    let linked = false;
    for (const range of ranges) {
        for (const signal of range?.signals || []) {
            const atoms = (Array.isArray(signal?.atoms) ? signal.atoms : [])
                .map((atom) => Number(atom) + 1)
                .filter((atom) => Number.isFinite(atom) && atom > 0);
            const highlightIds = [signal?.id, range?.id].filter(
                (id) => typeof id === "string" && id !== ""
            );
            if (!atoms.length || !highlightIds.length) {
                continue;
            }

            linked = true;
            const entry = { nucleus, atoms, highlightIds };
            for (const atom of atoms) {
                atomPeaks.set(atom, entry);
            }
        }
    }

    return linked;
}

/**
 * Add peak, range, and signal ids derived from `info.predictionShifts`.
 * Returns a map of 1-based atom index to the ids NMRium should highlight.
 *
 * @param {object} payload
 * @param {{ frequency?: number, lineWidth?: number, tolerance?: number }} [options]
 * @returns {Map<number, { nucleus: string, atoms: number[], highlightIds: string[] }>}
 */
export function attachPredictionPeaks(payload, options = {}) {
    const frequency = options.frequency ?? 400;
    const lineWidth = options.lineWidth ?? 1;
    const tolerance = options.tolerance ?? 0.001;
    const atomPeaks = new Map();
    const spectra = payload?.data?.spectra;

    if (!Array.isArray(spectra)) {
        return atomPeaks;
    }

    const fwhmPpm = lineWidth / frequency;

    for (const spectrum of spectra) {
        if (linkExistingSignalAtoms(spectrum, atomPeaks)) {
            continue;
        }

        const nucleus = spectrum?.info?.nucleus;
        const shifts = spectrum?.info?.predictionShifts;
        if (
            typeof nucleus !== "string" ||
            !Array.isArray(shifts) ||
            !shifts.length
        ) {
            continue;
        }

        const groups = groupEquivalentShifts(shifts, tolerance);
        if (!groups.length) {
            continue;
        }

        spectrum.peaks = {
            values: groups.map((group) => {
                const key = atomKey(group.atoms);
                const ppm = group.prediction;
                return {
                    id: `pred-${nucleus}-${key}`,
                    x: ppm,
                    y: peakHeightAt(spectrum.data, ppm),
                    width: fwhmPpm,
                    originalX: ppm,
                };
            }),
            options: {},
        };

        const ordered = [...groups].sort(
            (left, right) => left.prediction - right.prediction
        );
        const rangeValues = ordered.map((group, index) => {
            const ppm = group.prediction;
            let left = RANGE_HALF_WIDTH_PPM;
            let right = RANGE_HALF_WIDTH_PPM;
            if (index > 0) {
                left = Math.min(
                    left,
                    (ppm - ordered[index - 1].prediction) / 2
                );
            }
            if (index < ordered.length - 1) {
                right = Math.min(
                    right,
                    (ordered[index + 1].prediction - ppm) / 2
                );
            }
            const key = atomKey(group.atoms);
            return {
                id: `pred-range-${nucleus}-${key}`,
                from: ppm - left,
                to: ppm + right,
                absolute: group.count,
                integration: group.count,
                signals: [
                    {
                        id: `pred-signal-${nucleus}-${key}`,
                        kind: "signal",
                        multiplicity: "s",
                        delta: ppm,
                        js: [],
                        nbAtoms: group.count,
                    },
                ],
            };
        });
        const total = ordered.reduce((sum, group) => sum + group.count, 0);
        spectrum.ranges = {
            values: rangeValues,
            options: { sum: total, isSumConstant: true, sumAuto: false },
        };

        for (const group of groups) {
            const atoms = [...group.atoms].sort((left, right) => left - right);
            const key = atoms.join("-");
            const entry = {
                nucleus,
                atoms,
                highlightIds: [
                    `pred-signal-${nucleus}-${key}`,
                    `pred-range-${nucleus}-${key}`,
                    `pred-${nucleus}-${key}`,
                ],
            };
            for (const atom of atoms) {
                atomPeaks.set(atom, entry);
            }
        }
    }

    return atomPeaks;
}

function gradientId(element) {
    const safe = String(element).replace(/[^A-Za-z0-9]/g, "") || "default";
    return `atom-glow-${safe}`;
}

function glowDefs(elements) {
    const defs = [...elements].sort().map((element) => {
        const color = ATOM_GLOW_COLORS[element] || ATOM_GLOW_DEFAULT;
        const id = gradientId(element);
        return (
            `<radialGradient id="${id}" cx="50%" cy="50%" r="50%">` +
            `<stop offset="0%" stop-color="${color}" stop-opacity="0.95"/>` +
            `<stop offset="45%" stop-color="${color}" stop-opacity="0.55"/>` +
            `<stop offset="100%" stop-color="${color}" stop-opacity="0"/>` +
            `</radialGradient>`
        );
    });
    defs.push(
        `<radialGradient id="atom-glow-default" cx="50%" cy="50%" r="50%">` +
            `<stop offset="0%" stop-color="${ATOM_GLOW_DEFAULT}" stop-opacity="0.95"/>` +
            `<stop offset="45%" stop-color="${ATOM_GLOW_DEFAULT}" stop-opacity="0.55"/>` +
            `<stop offset="100%" stop-color="${ATOM_GLOW_DEFAULT}" stop-opacity="0"/>` +
            `</radialGradient>`
    );
    return `<defs>\n${defs.join("\n")}\n</defs>`;
}

/**
 * OpenChemLib SVG plus hit and glow circles. Atom numbers are 1-based and
 * follow `addImplicitHydrogens`, the same order NMRShiftDB uses.
 *
 * @param {string} molfile
 * @param {typeof import("openchemlib").default} OCL
 * @param {{ width?: number, height?: number }} [options]
 */
export function interactiveStructureSvg(molfile, OCL, options = {}) {
    if (!molfile || !OCL) {
        return "";
    }

    const width = options.width ?? 360;
    const height = options.height ?? 300;
    const mol = OCL.Molecule.fromMolfile(molfile);
    if (!mol || mol.getAllAtoms() < 1) {
        return "";
    }

    mol.addImplicitHydrogens();
    const svg = mol.toSVG(width, height, "predicted-structure", {
        autoCrop: true,
        autoCropMargin: 12,
    });

    const elements = [];
    for (let index = 0; index < mol.getAllAtoms(); index += 1) {
        const label = mol.getAtomLabel(index);
        elements[index] =
            typeof label === "string" && label.trim() !== ""
                ? label.trim()
                : "C";
    }

    return annotateAtomHits(svg, elements);
}

/**
 * @param {string} svg
 * @param {string[]} elements 0-based atom index to element symbol
 */
function annotateAtomHits(svg, elements) {
    const pattern = /<circle\b[^>]*\bid="[^"]*:Atom:(\d+)"[^>]*\/?>/g;
    const circles = [];
    let match = pattern.exec(svg);
    while (match) {
        const cx = /cx="([^"]+)"/.exec(match[0])?.[1];
        const cy = /cy="([^"]+)"/.exec(match[0])?.[1];
        if (cx != null && cy != null) {
            circles.push({
                index: Number(match[1]),
                cx,
                cy,
                start: match.index,
                end: match.index + match[0].length,
            });
        }
        match = pattern.exec(svg);
    }

    if (!circles.length) {
        return svg;
    }

    let annotated = svg;
    for (let index = circles.length - 1; index >= 0; index -= 1) {
        const circle = circles[index];
        const atom = circle.index + 1;
        const element = elements[circle.index] || "C";
        const hit =
            `<circle class="event atom-hit" data-atom="${atom}" data-elem="${element}" ` +
            `cx="${circle.cx}" cy="${circle.cy}" r="${ATOM_HIT_RADIUS}" fill="transparent"/>`;
        annotated =
            annotated.slice(0, circle.start) +
            hit +
            annotated.slice(circle.end);
    }

    const used = new Set(
        circles.map((circle) => elements[circle.index] || "C")
    );
    const glows = circles
        .map((circle) => {
            const atom = circle.index + 1;
            const element = elements[circle.index] || "C";
            const fill = ATOM_GLOW_COLORS[element]
                ? `url(#${gradientId(element)})`
                : "url(#atom-glow-default)";
            return (
                `<circle class="atom-glow" data-atom="${atom}" data-elem="${element}" ` +
                `cx="${circle.cx}" cy="${circle.cy}" r="${ATOM_GLOW_RADIUS}" ` +
                `fill="${fill}" opacity="0" pointer-events="none"/>`
            );
        })
        .join("\n");

    annotated = annotated.replace(/(<svg\b[^>]*>)/i, `$1\n${glowDefs(used)}`);
    if (annotated.includes("</style>")) {
        return annotated.replace("</style>", `</style>\n${glows}`);
    }
    return annotated.replace(/<\/defs>/i, `</defs>\n${glows}`);
}
