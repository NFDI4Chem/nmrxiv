/**
 * CAS Registry Number pattern: 2–7 digits, hyphen, 2 digits, hyphen, 1 check digit.
 * Must not be adjacent to other digits (avoids matching fragments of longer numbers).
 */
const CAS_REGISTRY_NUMBER_PATTERN = /(?:^|[^\d])(\d{2,7}-\d{2}-\d)(?!\d)/g;

/**
 * @param {string} cas
 */
export function normalizeCasRegistryNumber(cas) {
    return cas.trim();
}

/**
 * @param {string} text
 * @returns {string[]}
 */
export function extractCasRegistryNumbersFromText(text) {
    if (!text || typeof text !== "string") {
        return [];
    }

    const found = new Set();

    for (const match of text.matchAll(CAS_REGISTRY_NUMBER_PATTERN)) {
        const cas = normalizeCasRegistryNumber(match[1] ?? "");

        if (cas) {
            found.add(cas);
        }
    }

    return [...found];
}

/**
 * @param {string} text
 * @returns {string|null}
 */
export function extractCasRegistryNumberFromText(text) {
    const matches = extractCasRegistryNumbersFromText(text);

    return matches[0] ?? null;
}
