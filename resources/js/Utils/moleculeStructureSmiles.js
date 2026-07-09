import OCL from "openchemlib";

/**
 * Resolve a SMILES string suitable for 2D depiction from a molecule record.
 *
 * @param {{ canonical_smiles?: string, smiles?: string, absolute_smiles?: string, sdf?: string }|null|undefined} molecule
 */
export function moleculeStructureSmiles(molecule) {
    if (!molecule) {
        return "";
    }

    const canonical = molecule.canonical_smiles?.trim();

    if (canonical) {
        return canonical;
    }

    const smiles = molecule.smiles?.trim();

    if (smiles) {
        return smiles;
    }

    const absolute = molecule.absolute_smiles?.trim();

    if (absolute) {
        return absolute;
    }

    const sdf = molecule.sdf?.trim();

    if (!sdf) {
        return "";
    }

    try {
        return OCL.Molecule.fromMolfile(sdf).toSmiles();
    } catch {
        try {
            const parsed = OCL.Molecule.fromText(sdf);

            return parsed?.toSmiles() ?? "";
        } catch {
            return "";
        }
    }
}
