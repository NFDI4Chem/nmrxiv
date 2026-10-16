/**
 * Authors and literature citations, built from
 * the bag's bio-schema.json with CITATION.cff as a fallback for the release date.
 */

/**
 * @typedef {object} BagAuthor
 * @property {string} name
 * @property {string|null} givenName
 * @property {string|null} familyName
 * @property {string|null} role
 * @property {string|null} orcid
 * @property {string|null} affiliation
 *
 * @typedef {object} BagCitation
 * @property {string} title
 * @property {string|null} authors
 * @property {string|null} doi
 * @property {string|null} url
 */

/**
 * @param {object|null} schema
 * @returns {BagAuthor[]}
 */
export function extractAuthors(schema) {
    const raw = schema?.author;
    const list = Array.isArray(raw) ? raw : raw ? [raw] : [];

    return list
        .map((entry) => {
            const person = entry?.['@type'] === 'Role' ? entry.author : entry;
            if (!person || typeof person !== 'object') {
                return null;
            }

            const name =
                person.name ||
                [person.givenName, person.familyName].filter(Boolean).join(' ');
            if (!name) {
                return null;
            }

            const orcid = person.sameAs || person['@id'] || null;

            return {
                name,
                givenName: person.givenName || null,
                familyName: person.familyName || null,
                role: entry?.roleName || null,
                orcid:
                    typeof orcid === 'string' && orcid.includes('orcid.org')
                        ? orcid
                        : null,
                affiliation: person.affiliation || null,
            };
        })
        .filter(Boolean);
}

/**
 * @param {object|null} schema
 * @returns {BagCitation[]}
 */
export function extractCitations(schema) {
    const raw = schema?.citation;
    const list = Array.isArray(raw) ? raw : raw ? [raw] : [];

    return list
        .filter((entry) => entry && typeof entry === 'object')
        .map((entry) => {
            const doi = normalizeDoi(entry['@id'] || entry.sameAs);
            const url =
                (typeof entry.sameAs === 'string' &&
                    /^https?:\/\//.test(entry.sameAs) &&
                    entry.sameAs) ||
                (doi ? `https://doi.org/${doi}` : null);

            return {
                title: entry.headline || entry.name || '',
                authors: typeof entry.author === 'string' ? entry.author : null,
                doi,
                url,
            };
        })
        .filter((citation) => citation.title);
}

/**
 * Read top-level `key: value` scalars from a CITATION.cff. Nested structures
 * (authors, references) are ignored; they come from bio-schema.json.
 *
 * @param {string|null} text
 * @returns {Record<string, string>}
 */
export function parseCffScalars(text) {
    const result = {};
    for (const line of String(text || '').split(/\r?\n/)) {
        const match = /^([A-Za-z][\w-]*):\s*(.+?)\s*$/.exec(line);
        if (!match) {
            continue;
        }

        let value = match[2];
        if (value.startsWith('"')) {
            try {
                value = JSON.parse(value);
            } catch {
                value = value.replace(/^"|"$/g, '');
            }
        }
        result[match[1]] = value;
    }

    return result;
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
export function normalizeDoi(value) {
    if (typeof value !== 'string') {
        return null;
    }
    const doi = value
        .trim()
        .replace(/^https?:\/\/(dx\.)?doi\.org\//i, '')
        .replace(/^doi:/i, '');

    return /^10\.\d{4,9}\//.test(doi) ? doi : null;
}
