import { basename, normalizePath, readEntryJson, readEntryText } from './sources.js';

/**
 * @typedef {object} BagMetadata
 * @property {object|null} schema bio-schema.json contents
 * @property {object} bagInfo
 * @property {string|null} nmriumPath
 * @property {object|null} nmriumParsed
 * @property {string} sampleName
 * @property {string|null} doi
 * @property {string|null} description
 * @property {string[]} keywords
 * @property {string|null} license
 * @property {string|null} url
 * @property {string|null} datePublished
 * @property {string|null} dateCreated
 * @property {object[]} molecules
 * @property {object[]} datasets
 * @property {Map<string, string>} previewObjectUrls spectrumId -> object URL
 */

/**
 * Extract display metadata from a discovered bag.
 *
 * @param {import('./discover.js').DiscoveredBag} bag
 * @returns {Promise<BagMetadata>}
 */
export async function loadBagMetadata(bag) {
    const bagInfoEntry = bag.byPath.get('bag-info.txt');
    const bagInfo = bagInfoEntry
        ? parseBagInfoLocal(await readEntryText(bagInfoEntry))
        : {};

    const schemaEntry = findBioSchema(bag);
    const schema = schemaEntry ? await readEntryJson(schemaEntry) : null;

    const nmriumEntry = findNmrium(bag);
    let nmriumParsed = null;
    if (nmriumEntry) {
        try {
            nmriumParsed = await readEntryJson(nmriumEntry);
        } catch {
            nmriumParsed = null;
        }
    }

    const molecules = extractMolecules(schema);
    const datasets = extractDatasets(schema, nmriumParsed);
    const previewObjectUrls = await loadPreviewUrls(bag, nmriumParsed);

    return {
        schema,
        bagInfo,
        nmriumPath: nmriumEntry?.path || null,
        nmriumParsed,
        sampleName: schema?.name || bag.label,
        doi: schema?.['@id'] || null,
        description: schema?.description || null,
        keywords: Array.isArray(schema?.keywords)
            ? schema.keywords.filter(
                  (kw) => typeof kw === 'string' && kw.length > 0,
              )
            : [],
        license: schema?.license || null,
        url: schema?.url || null,
        datePublished: schema?.datePublished || null,
        dateCreated: schema?.dateCreated || null,
        molecules,
        datasets,
        previewObjectUrls,
    };
}

function findBioSchema(bag) {
    for (const entry of bag.entries) {
        const path = normalizePath(entry.path).toLowerCase();
        if (path.endsWith('/nmrxiv-meta/bio-schema.json') || path === 'nmrxiv-meta/bio-schema.json') {
            return entry;
        }
        if (basename(path) === 'bio-schema.json') {
            return entry;
        }
    }

    return null;
}

function findNmrium(bag) {
    const matches = bag.entries.filter((entry) => {
        const path = normalizePath(entry.path).toLowerCase();

        return path.endsWith('.nmrium') && path.includes('/nmrxiv-meta/');
    });

    return matches[0] || null;
}

function extractMolecules(schema) {
    const parts = schema?.about?.hasBioChemEntityPart;
    if (!Array.isArray(parts)) {
        return [];
    }

    return parts
        .filter((part) => part && typeof part === 'object')
        .map((part) => ({
            id: part['@id'] || part.inChIKey || null,
            name: part.iupacName || part.name || null,
            inChI: part.inChI || null,
            inChIKey: part.inChIKey || null,
            smiles: firstSmiles(part.smiles),
            molecularFormula: part.molecularFormula || null,
            molecularWeight: part.molecularWeight || null,
            description: part.description || null,
            url: part.url || null,
        }));
}

/**
 * @param {unknown} value
 * @returns {string|null}
 */
export function firstSmiles(value) {
    if (typeof value === 'string' && value.trim()) {
        return value.trim();
    }

    if (Array.isArray(value)) {
        for (const entry of value) {
            if (typeof entry === 'string' && entry.trim()) {
                return entry.trim();
            }
        }
    }

    return null;
}

export function extractDatasets(schema, nmriumParsed) {
    const spectra = nmriumParsed?.nmriumState?.data?.spectra || [];
    const spectrumByName = new Map();
    for (const sp of spectra) {
        if (!sp || typeof sp !== 'object') {
            continue;
        }

        const name = sp?.info?.name || '';
        if (name) {
            spectrumByName.set(name, sp);
        }
    }

    const parts = Array.isArray(schema?.hasPart) ? schema.hasPart : [];

    return parts
        .filter((part) => part && typeof part === 'object')
        .map((part) => {
            const name = part.name || part.description || 'Dataset';
            const variables = Array.isArray(part.variableMeasured)
                ? part.variableMeasured
                      .filter((v) => v && typeof v === 'object')
                      .map((v) => ({
                          name: v.name || null,
                          value: v.value,
                          unit: v.unitText || v.unitCode || null,
                      }))
                : [];

            // Try match spectrum by jdf filename in description / name
            let spectrumId = null;
            let matched = null;
            for (const [spName, sp] of spectrumByName) {
                if (
                    name.includes(spName) ||
                    (part.description && part.description.includes(spName))
                ) {
                    matched = sp;
                    spectrumId = sp.id;
                    break;
                }
            }

            return {
                id: part['@id'] || name,
                name,
                description: part.description || null,
                url: part.url || null,
                keywords: Array.isArray(part.keywords)
                    ? part.keywords.filter(
                          (kw) => typeof kw === 'string' && kw.length > 0,
                      )
                    : [],
                variables,
                spectrumId,
                nucleus: matched?.info?.nucleus || null,
                solvent: matched?.info?.solvent || null,
                peaks: extractPeaks(matched),
            };
        });
}

/**
 * Prefer explicit peaks.values; otherwise flatten range signals (δ / multiplicity).
 *
 * @param {object|null} spectrum
 * @returns {Array<{ id: string, delta: number|null, multiplicity: string|null, intensity: number|null }>}
 */
export function extractPeaks(spectrum) {
    if (!spectrum) {
        return [];
    }

    const explicit = spectrum.peaks?.values;
    if (Array.isArray(explicit) && explicit.length > 0) {
        return explicit
            .filter((peak) => peak && typeof peak === 'object')
            .map((peak, index) => ({
                id: peak.id || `peak-${index}`,
                delta:
                    typeof peak.x === 'number'
                        ? peak.x
                        : typeof peak.delta === 'number'
                          ? peak.delta
                          : null,
                multiplicity: peak.multiplicity || null,
                intensity: typeof peak.y === 'number' ? peak.y : null,
            }));
    }

    const ranges = spectrum.ranges?.values;
    if (!Array.isArray(ranges)) {
        return [];
    }

    const peaks = [];
    for (const range of ranges) {
        if (!range || typeof range !== 'object') {
            continue;
        }

        const signals = Array.isArray(range.signals) ? range.signals : [];
        for (const signal of signals) {
            if (!signal || typeof signal !== 'object') {
                continue;
            }

            peaks.push({
                id: signal.id || `${range.id}-${peaks.length}`,
                delta:
                    typeof signal.delta === 'number'
                        ? signal.delta
                        : typeof signal.originalDelta === 'number'
                          ? signal.originalDelta
                          : null,
                multiplicity: signal.multiplicity || null,
                intensity: null,
            });
        }
    }

    return peaks;
}

async function loadPreviewUrls(bag, nmriumParsed) {
    const urls = new Map();
    const spectra = nmriumParsed?.nmriumState?.data?.spectra || [];

    for (const sp of spectra) {
        if (!sp?.id) {
            continue;
        }

        const imagePath = findImagePath(bag, sp.id);
        if (!imagePath) {
            continue;
        }

        const entry = bag.byPath.get(imagePath);
        if (!entry) {
            continue;
        }

        try {
            const blob = await entry.getBlob();
            urls.set(sp.id, URL.createObjectURL(blob));
        } catch {
            // ignore
        }
    }

    // Also map embedded base64 images from nmrium if present
    if (Array.isArray(nmriumParsed?.images)) {
        for (const img of nmriumParsed.images) {
            if (img?.id != null && typeof img.image === 'string' && img.image.startsWith('data:')) {
                // spectrum id may differ from image id; skip unless we can map
            }
        }
    }

    return urls;
}

function findImagePath(bag, spectrumId) {
    const needle = `/images/${spectrumId}.png`;
    for (const entry of bag.entries) {
        const path = normalizePath(entry.path);
        if (path.endsWith(needle) || path.endsWith(`images/${spectrumId}.png`)) {
            return path;
        }
    }

    return null;
}

function parseBagInfoLocal(text) {
    const result = {};
    const lines = String(text).replace(/\r\n/g, '\n').split('\n');
    let currentKey = null;

    for (const line of lines) {
        if (line === '') {
            continue;
        }
        if (/^[ \t]/.test(line) && currentKey) {
            result[currentKey] += ' ' + line.trim();
            continue;
        }
        const idx = line.indexOf(':');
        if (idx === -1) {
            continue;
        }
        currentKey = line.slice(0, idx).trim();
        result[currentKey] = line.slice(idx + 1).trim();
    }

    return result;
}

/**
 * Revoke object URLs created for previews.
 *
 * @param {Map<string, string>} previewObjectUrls
 */
export function revokePreviewUrls(previewObjectUrls) {
    if (!previewObjectUrls) {
        return;
    }
    for (const url of previewObjectUrls.values()) {
        try {
            URL.revokeObjectURL(url);
        } catch {
            // ignore
        }
    }
}
