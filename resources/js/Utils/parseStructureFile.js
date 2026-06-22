import {
    extractMolBlockFromSdfRecord,
    moleculeFromMolfileOrText,
    normalizeLineEndings,
} from "@/Utils/molfileNormalize";
import { loadOpenChemLib } from "@/Utils/structureEditor";

export {
    extractMolBlockFromSdfRecord,
    normalizeMolfile,
} from "@/Utils/molfileNormalize";

const STRUCTURE_FILE_EXTENSIONS = [".mol", ".sdf", ".sd", ".cdxml"];

/**
 * @typedef {Object} StructureCandidate
 * @property {string} id
 * @property {string} label
 * @property {string} molfile
 * @property {number} index
 * @property {'mol'|'sdf'|'cdxml'|'paste'} source
 */

/**
 * @param {string} fileName
 */
export function structureFileExtension(fileName) {
    const lower = fileName.toLowerCase();

    return STRUCTURE_FILE_EXTENSIONS.find((ext) => lower.endsWith(ext)) ?? null;
}

/**
 * @param {string} fileName
 */
export function isSupportedStructureFile(fileName) {
    return structureFileExtension(fileName) !== null;
}

/**
 * @param {string} text
 * @param {string} [fileName]
 */
export function detectStructureFormat(text, fileName = "") {
    const ext = fileName ? structureFileExtension(fileName) : null;

    if (ext === ".cdxml") {
        return "cdxml";
    }

    if (ext === ".sdf" || ext === ".sd") {
        return "sdf";
    }

    if (ext === ".mol") {
        return "mol";
    }

    const trimmed = text.trim();

    if (trimmed.startsWith("<?xml") || trimmed.includes("<CDXML")) {
        return "cdxml";
    }

    if (trimmed.includes("$$$$")) {
        return "sdf";
    }

    if (
        trimmed.includes("M  END") ||
        trimmed.includes("V2000") ||
        trimmed.includes("V3000")
    ) {
        return "mol";
    }

    return null;
}

/**
 * @param {string} pAttr
 */
function parseCdxmlPoint(pAttr) {
    if (!pAttr) {
        return { x: 0, y: 0 };
    }

    const parts = pAttr.trim().split(/\s+/);

    if (parts.length >= 3 && /^[mM]$/.test(parts[0])) {
        return {
            x: Number.parseFloat(parts[1]) || 0,
            y: Number.parseFloat(parts[2]) || 0,
        };
    }

    return { x: 0, y: 0 };
}

/**
 * @param {number} value
 * @param {number} min
 * @param {number} span
 */
function normalizeCoordinate(value, min, span) {
    if (span <= 0) {
        return 0;
    }

    return ((value - min) / span) * 8;
}

/**
 * @param {Element} fragmentEl
 * @param {import('openchemlib').default} OCL
 */
function cdxmlFragmentToMolfile(fragmentEl, OCL) {
    const atomElements = Array.from(fragmentEl.getElementsByTagName("n"));

    if (atomElements.length === 0) {
        throw new Error("CDXML fragment contains no atoms.");
    }

    const bondElements = Array.from(fragmentEl.getElementsByTagName("b"));
    const mol = new OCL.Molecule(atomElements.length, bondElements.length);
    const idToIndex = new Map();
    const rawPoints = atomElements.map((node) =>
        parseCdxmlPoint(node.getAttribute("p"))
    );
    const xs = rawPoints.map((point) => point.x);
    const ys = rawPoints.map((point) => point.y);
    const minX = Math.min(...xs);
    const maxX = Math.max(...xs);
    const minY = Math.min(...ys);
    const maxY = Math.max(...ys);
    const spanX = maxX - minX;
    const spanY = maxY - minY;

    atomElements.forEach((node, index) => {
        const id = node.getAttribute("id") ?? String(index + 1);
        const atomicNo = Number.parseInt(
            node.getAttribute("Element") ?? "6",
            10
        );
        const point = rawPoints[index];
        const atomIndex = mol.addAtom(atomicNo);
        mol.setAtomX(atomIndex, normalizeCoordinate(point.x, minX, spanX));
        mol.setAtomY(atomIndex, -normalizeCoordinate(point.y, minY, spanY));
        idToIndex.set(id, atomIndex);
    });

    bondElements.forEach((bondEl) => {
        const beginId = bondEl.getAttribute("B");
        const endId = bondEl.getAttribute("E");

        if (!beginId || !endId) {
            return;
        }

        const beginIndex = idToIndex.get(beginId);
        const endIndex = idToIndex.get(endId);

        if (beginIndex === undefined || endIndex === undefined) {
            return;
        }

        const order = Number.parseInt(bondEl.getAttribute("Order") ?? "1", 10);
        const bondType = order >= 3 ? 3 : order === 2 ? 2 : 1;

        mol.addOrChangeBond(beginIndex, endIndex, bondType);
    });

    return mol.toMolfile();
}

/**
 * @param {string} text
 * @param {import('openchemlib').default} OCL
 */
function parseCdxmlStructures(text, OCL) {
    const doc = new DOMParser().parseFromString(text, "application/xml");

    if (doc.querySelector("parsererror")) {
        throw new Error("Invalid CDXML file.");
    }

    const fragments = Array.from(doc.getElementsByTagName("fragment"));

    if (fragments.length === 0) {
        const molfile = cdxmlFragmentToMolfile(doc.documentElement, OCL);

        return [
            {
                id: "cdxml-1",
                label: "Structure 1",
                molfile,
                index: 1,
                source: "cdxml",
            },
        ];
    }

    return fragments.map((fragment, index) => {
        const fragmentId = fragment.getAttribute("id");
        const label =
            fragment.getAttribute("name")?.trim() ||
            (fragmentId ? `Fragment ${fragmentId}` : `Structure ${index + 1}`);

        return {
            id: `cdxml-${index + 1}`,
            label,
            molfile: cdxmlFragmentToMolfile(fragment, OCL),
            index: index + 1,
            source: "cdxml",
        };
    });
}

/**
 * @param {string} block
 */
function molBlockTitle(block) {
    const firstLine = block.split("\n")[0]?.trim();

    return firstLine || null;
}

/**
 * @param {string} text
 */
function splitSdfRecords(text) {
    const normalized = normalizeLineEndings(text).trim();

    if (!normalized) {
        return [];
    }

    return normalized
        .split(/\n\$\$\$\$\s*\n?/)
        .map((record) => record.trim())
        .filter(
            (record) =>
                record.length > 0 &&
                (record.includes("M  END") ||
                    record.includes("V2000") ||
                    record.includes("V3000"))
        );
}

/**
 * @param {string} molfile
 * @param {import('openchemlib').default} OCL
 * @returns {string} Normalized molfile suitable for OpenChemLib
 */
function validateMolfile(molfile, OCL) {
    const { molecule, normalized } = moleculeFromMolfileOrText(molfile, OCL);

    if (!molecule || molecule.getAtoms() < 1) {
        throw new Error("Invalid structure block in file.");
    }

    return normalized;
}

/**
 * @param {string} text
 * @param {'mol'|'sdf'|'cdxml'} format
 * @param {import('openchemlib').default} OCL
 * @returns {StructureCandidate[]}
 */
function parseByFormat(text, format, OCL) {
    if (format === "cdxml") {
        return parseCdxmlStructures(text, OCL);
    }

    if (format === "sdf") {
        const records = splitSdfRecords(text);

        if (records.length === 0) {
            throw new Error("No structures found in the SDF file.");
        }

        return records.map((record, index) => {
            const molBlock = extractMolBlockFromSdfRecord(record);
            const molfile = validateMolfile(molBlock, OCL);
            const title = molBlockTitle(molBlock) || molBlockTitle(record);

            return {
                id: `sdf-${index + 1}`,
                label: title ? title : `Structure ${index + 1}`,
                molfile,
                index: index + 1,
                source: "sdf",
            };
        });
    }

    const molfile = validateMolfile(text.trim(), OCL);
    const title = molBlockTitle(molfile);

    return [
        {
            id: "mol-1",
            label: title ? title : "Structure 1",
            molfile,
            index: 1,
            source: "mol",
        },
    ];
}

/**
 * @param {string} text
 * @param {string} [fileName]
 * @returns {Promise<StructureCandidate[]>}
 */
export async function parseStructureText(text, fileName = "") {
    const format = detectStructureFormat(text, fileName);

    if (!format) {
        throw new Error(
            "Unsupported structure format. Use MOL, SDF, or CDXML."
        );
    }

    const OCL = await loadOpenChemLib();

    return parseByFormat(text, format, OCL);
}

/**
 * @param {File} file
 * @returns {Promise<StructureCandidate[]>}
 */
export async function parseStructureFile(file) {
    if (!isSupportedStructureFile(file.name)) {
        throw new Error(
            "Unsupported file type. Please upload a .mol, .sdf, or .cdxml file."
        );
    }

    const text = await file.text();

    return parseStructureText(text, file.name);
}
