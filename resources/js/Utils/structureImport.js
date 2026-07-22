import OCL from "openchemlib";
import {
    extractMolBlockFromSdfRecord,
    moleculeFromMolfileOrText,
} from "@/Utils/molfileNormalize";

/**
 * @typedef {'SMILES' | 'MOL/SDF' | 'CAS' | ''} StructureInputType
 */

/**
 * @typedef {Object} ResolvedStructure
 * @property {'smiles' | 'molfile'} type
 * @property {string} value
 */

/**
 * @param {string} input
 */
export function detectStructureInputFormat(input) {
    if (!input || input.trim() === "") {
        return "";
    }

    const trimmed = input.trim();
    const lines = trimmed.split("\n");

    if (
        trimmed.includes("M  END") ||
        trimmed.includes("M END") ||
        trimmed.includes("$$$$") ||
        trimmed.includes("V2000") ||
        trimmed.includes("V3000") ||
        (lines.length > 3 && lines[3] && lines[3].includes(" 0  0  0"))
    ) {
        return "MOL/SDF";
    }

    if (lines.length <= 2 && trimmed.length < 500) {
        const smilesPattern = /^[A-Za-z0-9@+\-\[\]()=#\\/\\.:]+$/;
        if (smilesPattern.test(trimmed.replace(/\s/g, ""))) {
            return "SMILES";
        }
    }

    if (lines.length > 2) {
        return "MOL/SDF";
    }

    return "SMILES";
}

/**
 * @param {string} input
 * @param {string} [fileName]
 * @returns {StructureInputType}
 */
export function detectStructureInputType(input, fileName = "") {
    const trimmed = input.trim();

    if (!trimmed) {
        return "";
    }

    const lowerName = fileName.toLowerCase();

    if (
        lowerName.endsWith(".mol") ||
        lowerName.endsWith(".sdf") ||
        lowerName.endsWith(".sd")
    ) {
        return "MOL/SDF";
    }

    if (!trimmed.includes("\n") && /^\d{2,7}-\d{2}-\d$/.test(trimmed)) {
        return "CAS";
    }

    return detectStructureInputFormat(input);
}

/**
 * @param {string} input
 * @param {string} [fileName]
 * @returns {Promise<ResolvedStructure>}
 */
export async function resolveStructureForEditor(input, fileName = "") {
    let text = input.trim();
    const lowerName = fileName.toLowerCase();

    if (
        text.includes("$$$$") ||
        lowerName.endsWith(".sdf") ||
        lowerName.endsWith(".sd")
    ) {
        const firstRecord = text.split(/\n\$\$\$\$\s*\n?/)[0]?.trim() ?? text;
        text = extractMolBlockFromSdfRecord(firstRecord);
    } else {
        text = extractMolBlockFromSdfRecord(text);
    }

    const { molecule, normalized } = moleculeFromMolfileOrText(text, OCL);

    if (molecule && molecule.getAtoms() >= 1) {
        return { type: "molfile", value: normalized };
    }

    throw new Error("Client-side structure parsing failed");
}

/**
 * Resolve MOL/SDF content using the chemistry standardize service when OpenChemLib
 * cannot parse the source file (e.g. ChemDraw V3000 exports).
 *
 * @param {string} input
 * @param {string} [fileName]
 * @param {(molfile: string) => Promise<{ data?: { canonical_smiles?: string, standardized_mol?: string } }>} standardize
 * @returns {Promise<ResolvedStructure>}
 */
export async function resolveStructureForEditorWithStandardize(
    input,
    fileName = "",
    standardize
) {
    try {
        return await resolveStructureForEditor(input, fileName);
    } catch {
        // Fall back to standardize service below.
    }

    let text = input.trim();
    const lowerName = fileName.toLowerCase();

    if (
        text.includes("$$$$") ||
        lowerName.endsWith(".sdf") ||
        lowerName.endsWith(".sd")
    ) {
        const firstRecord = text.split(/\n\$\$\$\$\s*\n?/)[0]?.trim() ?? text;
        text = extractMolBlockFromSdfRecord(firstRecord);
    } else {
        text = extractMolBlockFromSdfRecord(text);
    }

    const response = await standardize(text);
    const data = response.data ?? {};

    if (data.canonical_smiles?.trim()) {
        const smiles = data.canonical_smiles.trim();
        const smilesMolecule = OCL.Molecule.fromSmiles(smiles);

        if (smilesMolecule.getAtoms() >= 1) {
            return { type: "smiles", value: smiles };
        }
    }

    if (data.standardized_mol?.trim()) {
        const {
            molecule: standardizedMolecule,
            normalized: standardizedMolfile,
        } = moleculeFromMolfileOrText(data.standardized_mol, OCL);

        if (standardizedMolecule && standardizedMolecule.getAtoms() >= 1) {
            return { type: "molfile", value: standardizedMolfile };
        }
    }

    throw new Error("Invalid structure");
}

/**
 * @param {{ getMolecule?: () => { getAtoms?: () => number } } | null | undefined} editor
 */
export function editorHasStructureContent(editor) {
    try {
        const molecule = editor?.getMolecule();

        return Boolean(molecule && molecule.getAtoms() > 0);
    } catch {
        return false;
    }
}
