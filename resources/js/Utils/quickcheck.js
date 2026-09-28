import OCL from "openchemlib";

const MAX_SMILES_LENGTH = 5000;

/**
 * openchemlib reorders atoms when a molfile lists explicit hydrogens between
 * heavy atoms (Mnova exports do), so 1-based molfile numbers are mapped to
 * 0-based OCL indices by their coordinates, which OCL keeps with y inverted.
 *
 * @param {string} molfile
 * @param {import("openchemlib").Molecule} molecule parsed from the same molfile
 * @returns {Map<number, number>}
 */
export function molfileToOclIndex(molfile, molecule) {
    const lines = molfile.replace(/\r\n?/g, "\n").split("\n");
    const atomCount = parseInt((lines[3] || "").slice(0, 3), 10) || 0;
    const key = (x, y) => `${Math.round(x * 1000)}:${Math.round(y * 1000)}`;

    const byPosition = new Map();
    for (let i = 0; i < molecule.getAllAtoms(); i++) {
        const position = key(molecule.getAtomX(i), -molecule.getAtomY(i));
        byPosition.set(position, [...(byPosition.get(position) || []), i]);
    }

    const map = new Map();
    for (let atom = 1; atom <= atomCount; atom++) {
        const line = lines[3 + atom] || "";
        const candidates = byPosition.get(
            key(parseFloat(line.slice(0, 10)), parseFloat(line.slice(10, 20)))
        );
        const index = candidates?.find(
            (candidate) =>
                molecule.getAtomLabel(candidate) === line.slice(31, 34).trim()
        );
        map.set(atom, index ?? candidates?.[0] ?? atom - 1);
    }

    return map;
}

function safeLabel(value) {
    return String(value ?? "")
        .replace(/[^\w'′./+\-()]/g, "")
        .slice(0, 16);
}

/**
 * SMILES of the heavy atoms, each in brackets with its hydrogen count,
 * written in a known order so atom values and highlights can be attached.
 * Atoms listed in `explicitHydrogens` get their hydrogens as `[H]` branches.
 *
 * @param {import("openchemlib").Molecule} molecule
 * @param {Set<number>} explicitHydrogens OCL indices of heavy atoms
 * @returns {{ smiles: string, size: number, positions: Map<number, number>, hydrogens: Map<number, number[]> }}
 *   size: atoms written, positions: SMILES position per heavy OCL index,
 *   hydrogens: SMILES positions of the explicit hydrogens per OCL index
 */
function orderedSmiles(molecule, explicitHydrogens = new Set()) {
    const atomCount = molecule.getAllAtoms();
    const isHydrogen = (atom) => molecule.getAtomicNo(atom) === 1;
    const neighbours = (atom) => {
        const list = [];
        for (let i = 0; i < molecule.getAllConnAtoms(atom); i++) {
            const next = molecule.getConnAtom(atom, i);
            if (!isHydrogen(next)) {
                list.push({ atom: next, bond: molecule.getConnBond(atom, i) });
            }
        }
        return list;
    };

    const visited = new Array(atomCount).fill(false);
    const treeBonds = new Set();
    const ringBonds = new Set();
    const explore = (atom, parentBond) => {
        visited[atom] = true;
        for (const next of neighbours(atom)) {
            if (next.bond === parentBond || treeBonds.has(next.bond)) {
                continue;
            }
            if (visited[next.atom]) {
                ringBonds.add(next.bond);
            } else {
                treeBonds.add(next.bond);
                explore(next.atom, next.bond);
            }
        }
    };

    const bondSymbol = (bond) =>
        ({ 2: "=", 3: "#" }[molecule.getBondOrder(bond)] || "");
    const atomToken = (atom) => {
        const mass = molecule.getAtomMass(atom);
        const charge = molecule.getAtomCharge(atom);
        const chargeText =
            charge === 0
                ? ""
                : `${charge > 0 ? "+" : "-"}${
                      Math.abs(charge) > 1 ? Math.abs(charge) : ""
                  }`;
        const hydrogens = explicitHydrogens.has(atom)
            ? 0
            : molecule.getAllHydrogens(atom);
        const hydrogenText =
            hydrogens === 0 ? "" : `H${hydrogens > 1 ? hydrogens : ""}`;
        return `[${mass || ""}${molecule.getAtomLabel(
            atom
        )}${hydrogenText}${chargeText}]`;
    };

    const positions = new Map();
    const hydrogens = new Map();
    let position = 0;
    const written = new Array(atomCount).fill(false);
    const openRings = new Map();
    const freeDigits = [];
    let nextDigit = 1;
    const digitText = (digit) => (digit < 10 ? `${digit}` : `%${digit}`);

    const write = (atom, parentBond) => {
        written[atom] = true;
        positions.set(atom, position++);
        let text = atomToken(atom);

        for (const next of neighbours(atom)) {
            if (!ringBonds.has(next.bond)) {
                continue;
            }
            if (openRings.has(next.bond)) {
                const digit = openRings.get(next.bond);
                openRings.delete(next.bond);
                freeDigits.push(digit);
                text += digitText(digit);
            } else if (!written[next.atom]) {
                const digit = freeDigits.length
                    ? freeDigits.sort((a, b) => a - b).shift()
                    : nextDigit++;
                openRings.set(next.bond, digit);
                text += bondSymbol(next.bond) + digitText(digit);
            }
        }

        if (explicitHydrogens.has(atom)) {
            const positions = [];
            for (let i = 0; i < molecule.getAllHydrogens(atom); i++) {
                positions.push(position++);
                text += "([H])";
            }
            hydrogens.set(atom, positions);
        }

        const children = neighbours(atom).filter(
            (next) => next.bond !== parentBond && treeBonds.has(next.bond)
        );
        children.forEach((child, i) => {
            const branch =
                bondSymbol(child.bond) + write(child.atom, child.bond);
            text += i < children.length - 1 ? `(${branch})` : branch;
        });

        return text;
    };

    const components = [];
    for (let atom = 0; atom < atomCount; atom++) {
        if (!visited[atom] && !isHydrogen(atom)) {
            explore(atom, -1);
            components.push(write(atom, -1));
        }
    }

    return {
        smiles: components.join("."),
        size: position,
        positions,
        hydrogens,
    };
}

/**
 * CXSMILES of the author's structure with the author's carbon and proton
 * labels as atom values, for the Cheminformatics Microservice depiction.
 * Protons with a label are written as explicit hydrogens on their carrier.
 * Report atom numbers refer to the author's molfile; `atomIds` maps them to
 * SMILES positions.
 *
 * @param {string} molfile
 * @param {object} report NMRKit validation report
 * @returns {{ cxsmiles: string, atomIds: (atoms: number[]) => number[], flagged: number[] }|null}
 */
export function quickcheckCxsmiles(molfile, report) {
    if (!molfile) {
        return null;
    }

    try {
        const molecule = OCL.Molecule.fromMolfile(molfile);
        molecule.removeAtomCustomLabels();
        molecule.ensureHelperArrays(OCL.Molecule.cHelperNeighbours);
        const toOcl = molfileToOclIndex(molfile, molecule);

        const protonLabels = new Map();
        for (const row of report?.reports?.["1H"]?.atoms || []) {
            const carrier = toOcl.get(row.atoms?.[0]);
            if (row.atoms?.length !== 1 || carrier === undefined) {
                continue;
            }
            const label = safeLabel(row.label);
            const labels = protonLabels.get(carrier) || [];
            if (label && !labels.includes(label)) {
                labels.push(label);
            }
            protonLabels.set(carrier, labels);
        }
        const withProtons = new Set(
            [...protonLabels.keys()].filter(
                (atom) => molecule.getAllHydrogens(atom) > 0
            )
        );

        const { smiles, size, positions, hydrogens } = orderedSmiles(
            molecule,
            withProtons
        );

        const atomIds = (atoms) =>
            (atoms || [])
                .map((atom) => positions.get(toOcl.get(atom)))
                .filter((index) => index !== undefined);
        const hydrogenIds = (atoms) =>
            (atoms || []).flatMap(
                (atom) => hydrogens.get(toOcl.get(atom)) || []
            );

        const values = new Array(size).fill("");
        for (const row of report?.reports?.["13C"]?.atoms || []) {
            const [index] = atomIds(row.atoms?.length === 1 ? row.atoms : []);
            if (index !== undefined && row.label) {
                values[index] = safeLabel(row.label);
            }
        }
        for (const [carrier, labels] of protonLabels) {
            (hydrogens.get(carrier) || []).forEach((index, i) => {
                values[index] = labels[i] || "";
            });
        }

        const flagged = new Set();
        for (const row of report?.assignment_check?.rows || []) {
            if (row.status === "fail" || row.status === "review") {
                const ids =
                    row.nucleus === "1H"
                        ? hydrogenIds(row.atoms)
                        : atomIds(row.atoms);
                ids.forEach((index) => flagged.add(index));
            }
        }

        const cxsmiles = values.some(Boolean)
            ? `${smiles} |$_AV:${values.join(";")}$|`
            : smiles;
        if (cxsmiles.length > MAX_SMILES_LENGTH) {
            return null;
        }

        return { cxsmiles, atomIds, flagged: [...flagged] };
    } catch (error) {
        console.error("Unable to prepare the Quickcheck structure:", error);
        return null;
    }
}

/**
 * Cheminformatics Microservice URL of the 2D depiction.
 *
 * @param {string} cmApi base URL, e.g. https://api.naturalproducts.net/latest/
 * @param {string} cxsmiles
 * @param {number[]} atomIds 0-based SMILES positions to highlight
 * @returns {string}
 */
export function quickcheckDepictionUrl(cmApi, cxsmiles, atomIds = []) {
    const params = new URLSearchParams({
        smiles: cxsmiles,
        width: "640",
        height: "480",
        annotate: "atomvalue",
        hydrogen_display: "Provided",
        CIP: "false",
    });
    if (atomIds.length) {
        params.set("atomIds", atomIds.join(","));
    }

    return `${cmApi.replace(/\/?$/, "/")}depict/2D_enhanced?${params
        .toString()
        .replace(/\+/g, "%20")}`;
}

/**
 * Molfile of an editor molecule without the display-only atom-number labels.
 *
 * @param {import("openchemlib").Molecule} molecule
 * @returns {string}
 */
export function plainMolfile(molecule) {
    const copy = molecule.getCompactCopy();
    copy.removeAtomCustomLabels();

    return copy.toMolfile();
}
