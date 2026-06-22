/**
 * @param {string} text
 */
export function normalizeLineEndings(text) {
    return text.replace(/\r\n/g, "\n").replace(/\r/g, "\n");
}

/**
 * Strip SDF data fields and return only the mol block (through M END).
 *
 * @param {string} record
 */
export function extractMolBlockFromSdfRecord(record) {
    const lines = normalizeLineEndings(record).split("\n");
    let endLineIndex = -1;

    for (let i = 0; i < lines.length; i++) {
        if (/^M\s+END/i.test(lines[i].trim())) {
            endLineIndex = i;
            break;
        }
    }

    if (endLineIndex === -1) {
        const dataStart = lines.findIndex((line) => /^>\s*</.test(line));

        if (dataStart > 0) {
            return lines.slice(0, dataStart).join("\n").trim();
        }

        return record.trim();
    }

    let countsLineIndex = -1;

    for (let i = 0; i <= endLineIndex; i++) {
        if (/V2000|V3000/.test(lines[i])) {
            countsLineIndex = i;
            break;
        }
    }

    const startIndex = countsLineIndex >= 3 ? countsLineIndex - 3 : 0;

    return lines
        .slice(startIndex, endLineIndex + 1)
        .join("\n")
        .trim();
}

/**
 * Rebuild header line widths so OpenChemLib can parse ChemDraw/RDKit exports.
 *
 * @param {string} molblock
 */
export function normalizeMolfile(molblock) {
    const lines = normalizeLineEndings(molblock).split("\n");
    const countsIdx = lines.findIndex((line) => /V2000|V3000/.test(line));

    if (countsIdx === -1) {
        return molblock.trim();
    }

    const endIdx = lines.findIndex((line) => /^M\s+END/i.test(line.trim()));
    const end = endIdx >= 0 ? endIdx : lines.length - 1;
    const countsParts = lines[countsIdx].trim().split(/\s+/);
    const atomCount = Number.parseInt(countsParts[0] ?? "0", 10);
    const bondCount = Number.parseInt(countsParts[1] ?? "0", 10);

    if (!Number.isFinite(atomCount) || atomCount < 0) {
        return molblock.trim();
    }

    const nonEmptyBeforeCounts = lines
        .slice(0, countsIdx)
        .map((line) => line.trim())
        .filter((line) => line.length > 0);
    const title = nonEmptyBeforeCounts[0] ?? "Molecule";
    const programLine =
        nonEmptyBeforeCounts[1] ?? nonEmptyBeforeCounts[0] ?? "OpenChemLib";

    const rebuilt = [
        title.slice(0, 80),
        programLine.slice(0, 80).padEnd(80),
        "",
        lines[countsIdx].padEnd(80),
    ];

    for (let i = 0; i < atomCount; i++) {
        const line = lines[countsIdx + 1 + i] ?? "";
        rebuilt.push(line.length >= 69 ? line.slice(0, 69) : line.padEnd(69));
    }

    for (let i = 0; i < bondCount; i++) {
        const line = lines[countsIdx + 1 + atomCount + i] ?? "";
        rebuilt.push(line.length >= 21 ? line.slice(0, 21) : line.padEnd(21));
    }

    for (let i = countsIdx + 1 + atomCount + bondCount; i <= end; i++) {
        if (/^M\s+ZZC/i.test(lines[i])) {
            continue;
        }

        rebuilt.push(lines[i]);
    }

    return rebuilt.join("\n").trim();
}

/**
 * @param {string} molfile
 * @param {import('openchemlib').default} OCL
 */
export function moleculeFromMolfileOrText(molfile, OCL) {
    const normalized = normalizeMolfile(molfile);
    let molecule;

    try {
        molecule = OCL.Molecule.fromMolfile(normalized);
    } catch {
        molecule = null;
    }

    if (!molecule || molecule.getAtoms() < 1) {
        try {
            molecule = OCL.Molecule.fromText(normalized);
        } catch {
            molecule = null;
        }
    }

    return { molecule, normalized };
}
