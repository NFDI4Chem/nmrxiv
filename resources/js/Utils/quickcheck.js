import OCL from "openchemlib";

const STATUS_RANK = { not_assessable: 0, ok: 1, review: 2, fail: 3 };

const STATUS_COLOR = {
    ok: OCL.Molecule.cAtomColorDarkGreen,
    review: OCL.Molecule.cAtomColorOrange,
    fail: OCL.Molecule.cAtomColorRed,
};

/**
 * openchemlib moves explicit hydrogens behind the heavy atoms, so a 1-based
 * molfile index maps to a different 0-based OCL index when the molfile
 * lists H atoms in between (Mnova exports do).
 *
 * @param {string} molfile
 * @returns {Map<number, number>}
 */
export function molfileToOclIndex(molfile) {
    const lines = molfile.replace(/\r\n?/g, "\n").split("\n");
    const counts = lines[3] || "";
    const atomCount = parseInt(counts.slice(0, 3), 10) || 0;
    const symbols = [];
    for (let i = 0; i < atomCount; i++) {
        symbols.push((lines[4 + i] || "").slice(31, 34).trim());
    }

    const heavy = symbols.filter((symbol) => symbol !== "H").length;
    const map = new Map();
    let heavyIndex = 0;
    let hydrogenIndex = heavy;
    symbols.forEach((symbol, index) => {
        map.set(index + 1, symbol === "H" ? hydrogenIndex++ : heavyIndex++);
    });

    return map;
}

/**
 * Structure image with atoms coloured by their worst assignment status and
 * annotated with the author's carbon labels.
 *
 * @param {string} molfile
 * @param {object} report NMRKit validation report
 * @param {number} size
 * @returns {string}
 */
export function quickcheckStructureSvg(molfile, report, size = 320) {
    if (!molfile) {
        return "";
    }

    try {
        const molecule = OCL.Molecule.fromMolfile(molfile);
        molecule.removeAtomCustomLabels();
        const toOcl = molfileToOclIndex(molfile);

        const worst = new Map();
        for (const row of report?.assignment_check?.rows || []) {
            for (const atom of row.atoms || []) {
                const current = worst.get(atom);
                if (
                    !current ||
                    STATUS_RANK[row.status] > STATUS_RANK[current]
                ) {
                    worst.set(atom, row.status);
                }
            }
        }

        worst.forEach((status, atom) => {
            const index = toOcl.get(atom);
            if (index !== undefined && STATUS_COLOR[status] !== undefined) {
                molecule.setAtomColor(index, STATUS_COLOR[status]);
            }
        });

        for (const row of report?.reports?.["13C"]?.atoms || []) {
            const index = toOcl.get(row.atoms?.[0]);
            const label = String(row.label || "")
                .replace(/^C-?/, "")
                .replace(/[^\w'′".,/+\-() ]/g, "")
                .slice(0, 12);
            if (index !== undefined && label) {
                molecule.setAtomCustomLabel(index, `]${label}`);
            }
        }

        return molecule.toSVG(size, size, undefined, { autoCropMargin: 12 });
    } catch (error) {
        console.error("Unable to render Quickcheck structure:", error);
        return "";
    }
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
