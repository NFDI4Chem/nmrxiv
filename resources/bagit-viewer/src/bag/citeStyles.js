import { Cite, plugins } from '@citation-js/core';
import '@citation-js/plugin-csl';
import apa from './csl/apa.csl?raw';
import harvard from './csl/harvard-cite-them-right.csl?raw';
import mla from './csl/modern-language-association.csl?raw';
import chicago from './csl/chicago-notes-bibliography.csl?raw';
import ieee from './csl/ieee.csl?raw';
import acs from './csl/american-chemical-society.csl?raw';
import rsc from './csl/royal-society-of-chemistry.csl?raw';
import wiley from './csl/wiley-was.csl?raw';

/**
 * Same style list as resources/js/Shared/Citation.vue. The website asks the
 * DataCite API for each style; offline we render the same CSL styles locally
 * and apply the same per-style clean-up. Vancouver ships with citation-js.
 */
export const CITATION_STYLES = [
    { key: 'APA', label: 'APA', template: 'apa' },
    { key: 'Harvard', label: 'Harvard', template: 'harvard-cite-them-right' },
    { key: 'MLA', label: 'MLA', template: 'modern-language-association' },
    { key: 'Vancouver', label: 'Vancouver', template: 'vancouver' },
    {
        key: 'Chicago',
        label: 'Chicago',
        template: 'chicago-notes-bibliography',
    },
    { key: 'IEEE', label: 'IEEE', template: 'ieee' },
    { key: 'ACS', label: 'ACS', template: 'american-chemical-society' },
    { key: 'RSC', label: 'RSC', template: 'royal-society-of-chemistry' },
    { key: 'Wiley', label: 'Wiley', template: 'wiley-was' },
    { key: 'Springer', label: 'Springer Nature', template: 'apa' },
];

const TEMPLATES = {
    apa,
    'harvard-cite-them-right': harvard,
    'modern-language-association': mla,
    'chicago-notes-bibliography': chicago,
    ieee,
    'american-chemical-society': acs,
    'royal-society-of-chemistry': rsc,
    'wiley-was': wiley,
};

let registered = false;

function registerTemplates() {
    if (registered) {
        return;
    }
    const templates = plugins.config.get('@csl').templates;
    for (const [name, xml] of Object.entries(TEMPLATES)) {
        templates.add(name, xml);
    }
    registered = true;
}

/**
 * @param {string} styleKey one of CITATION_STYLES[].key
 * @param {object} input
 * @param {string} input.title
 * @param {Array<{ name: string, givenName?: string|null, familyName?: string|null }>} input.authors
 * @param {string|null} input.doi
 * @param {string|null} input.url
 * @param {string|null} input.date ISO date
 * @param {string|null} input.publisher
 * @returns {string}
 */
export function formatCitation(styleKey, input) {
    registerTemplates();
    const style = CITATION_STYLES.find((s) => s.key === styleKey);
    if (!style) {
        return '';
    }

    const year = /^(\d{4})/.exec(String(input.date || ''))?.[1];
    const doi = input.doi || null;
    const item = {
        id: 'sample',
        type: 'dataset',
        title: input.title,
        author: input.authors.map((a) =>
            a.familyName
                ? {
                      family: a.familyName,
                      // Unicode hyphens (U+2010…2013) are not split into initials
                      given: (a.givenName || '').replace(
                          /[\u2010-\u2013]/g,
                          ' ',
                      ),
                  }
                : { literal: a.name },
        ),
        publisher: input.publisher || 'nmrXiv',
        ...(year ? { issued: { 'date-parts': [[Number(year)]] } } : {}),
        ...(doi ? { DOI: doi } : {}),
        ...(doi || input.url
            ? { URL: doi ? `https://doi.org/${doi}` : input.url }
            : {}),
    };

    let text = new Cite(item)
        .format('bibliography', {
            format: 'text',
            template: style.template,
            lang: 'en-US',
        })
        .trim()
        // numbered styles prefix "[1]", "(1)", "1." or "1" — DataCite omits it
        .replace(/^(\[\d+\]|\(\d+\)|\d+\.?)\s+/, '');

    return postProcess(styleKey, text, doi);
}

/** Mirrors the per-style adjustments in Citation.vue. */
function postProcess(styleKey, text, doi) {
    if (styleKey === 'ACS') {
        const index = text.search(/20(\d{2})(?=\.)/);
        if (index > 1) {
            return `${text.substring(0, index - 2)}. nmrXiv. ${text.substring(
                index,
            )}`;
        }
    } else if (styleKey === 'RSC' && doi) {
        const id = doi.substring(doi.lastIndexOf('.') + 1);

        return `${text.slice(0, -1)}, nmrXiv Data set:${id}, DOI:${doi}`;
    } else if (styleKey === 'Wiley') {
        return `[dataset] ${text.replace(' [Data set]', '')}`;
    } else if (styleKey === 'Springer') {
        const pattern = /\(20(\d{2})\)/;
        const match = text.match(pattern);
        if (match) {
            const index = text.search(pattern);

            return `${text.substring(0, index)}${text
                .replace(' [Data set]', '')
                .substring(index + 8)} ${match[0]}`;
        }
    }

    return text;
}
