/**
 * @param {string|null|undefined} formula
 * @returns {{ text: string, subscript: boolean }[]}
 */
export function parseMolecularFormulaDisplayParts(formula) {
    if (formula == null) {
        return [];
    }

    const plain = String(formula)
        .replace(/<[^>]*>/g, "")
        .trim();

    if (plain === "") {
        return [];
    }

    const parts = [];
    const regex = /(\d+)/g;
    let lastIndex = 0;
    let match;

    while ((match = regex.exec(plain)) !== null) {
        if (match.index > lastIndex) {
            parts.push({
                text: plain.slice(lastIndex, match.index),
                subscript: false,
            });
        }

        parts.push({
            text: match[1],
            subscript: true,
        });
        lastIndex = regex.lastIndex;
    }

    if (lastIndex < plain.length) {
        parts.push({
            text: plain.slice(lastIndex),
            subscript: false,
        });
    }

    return parts;
}
