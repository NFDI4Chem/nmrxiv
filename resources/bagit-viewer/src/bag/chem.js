import { Molecule } from 'openchemlib';

/**
 * @param {string|null} smiles
 * @param {number} [width]
 * @param {number} [height]
 * @returns {string} SVG markup, or '' when the SMILES cannot be drawn
 */
export function moleculeSvg(smiles, width = 290, height = 240) {
    if (!smiles) {
        return '';
    }

    try {
        return Molecule.fromSmiles(smiles).toSVG(width, height);
    } catch {
        return '';
    }
}

/**
 * Turn C8H10N4O2 into C₈H₁₀N₄O₂ for display.
 *
 * @param {string} formula
 * @returns {string}
 */
export function formatFormula(formula) {
    const subscripts = '₀₁₂₃₄₅₆₇₈₉';

    return String(formula).replace(
        /\d/g,
        (digit) => subscripts[digit] || digit,
    );
}

/**
 * @param {string|null} value
 * @returns {string}
 */
export function formatDate(value) {
    try {
        return new Date(value).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return value;
    }
}
