import { createSHA256, createSHA512, createMD5, createSHA1 } from 'hash-wasm';
import { basename, normalizePath, readEntryText } from './sources.js';

const ALGORITHM_BY_MANIFEST = {
    'manifest-sha256.txt': 'SHA-256',
    'manifest-sha512.txt': 'SHA-512',
    'manifest-sha1.txt': 'SHA-1',
    'manifest-md5.txt': 'MD5',
};

const ALGORITHM_BY_TAGMANIFEST = {
    'tagmanifest-sha256.txt': 'SHA-256',
    'tagmanifest-sha512.txt': 'SHA-512',
    'tagmanifest-sha1.txt': 'SHA-1',
    'tagmanifest-md5.txt': 'MD5',
};

/**
 * @typedef {'pass'|'fail'|'missing'|'extra'} FileStatus
 * @typedef {object} FileCheck
 * @property {string} path
 * @property {FileStatus} status
 * @property {string} [expected]
 * @property {string} [actual]
 * @property {string} [message]
 *
 * @typedef {object} ValidationProgress
 * @property {number} current
 * @property {number} total
 * @property {string} path
 *
 * @typedef {object} ValidationResult
 * @property {boolean} valid
 * @property {string[]} errors
 * @property {string[]} warnings
 * @property {FileCheck[]} files
 * @property {object} bagInfo
 * @property {string|null} bagitVersion
 * @property {string|null} algorithm
 */

/**
 * Validate a BagIt 1.0 bag from discovered bag entries.
 *
 * @param {import('./discover.js').DiscoveredBag} bag
 * @param {{ onProgress?: (p: ValidationProgress) => void }} [options]
 * @returns {Promise<ValidationResult>}
 */
export async function validateBag(bag, options = {}) {
    const { onProgress } = options;
    const errors = [];
    const warnings = [];
    const files = [];
    let bagitVersion = null;
    let algorithm = null;

    const bagitEntry = bag.byPath.get('bagit.txt');
    if (!bagitEntry) {
        return {
            valid: false,
            errors: ['Missing required bagit.txt'],
            warnings,
            files,
            bagInfo: {},
            bagitVersion,
            algorithm,
        };
    }

    const bagitText = await readEntryText(bagitEntry);
    const bagitFields = parseBagitTxt(bagitText);
    bagitVersion = bagitFields['BagIt-Version'] || null;

    if (!bagitVersion) {
        errors.push('bagit.txt is missing BagIt-Version');
    } else if (!/^1\./.test(bagitVersion) && bagitVersion !== '0.97') {
        warnings.push(`Unusual BagIt-Version: ${bagitVersion}`);
    }

    const bagInfoEntry = bag.byPath.get('bag-info.txt');
    const bagInfo = bagInfoEntry
        ? parseBagInfo(await readEntryText(bagInfoEntry))
        : {};

    const manifestName = Object.keys(ALGORITHM_BY_MANIFEST).find((name) =>
        bag.byPath.has(name),
    );

    if (!manifestName) {
        errors.push('No payload manifest found (manifest-sha256.txt etc.)');

        return {
            valid: false,
            errors,
            warnings,
            files,
            bagInfo,
            bagitVersion,
            algorithm,
        };
    }

    algorithm = ALGORITHM_BY_MANIFEST[manifestName];
    const manifestText = await readEntryText(bag.byPath.get(manifestName));
    const manifestEntries = parseManifest(manifestText);

    const payloadPaths = new Set();
    for (const entry of bag.entries) {
        const path = normalizePath(entry.path);
        if (path === 'data' || path.startsWith('data/')) {
            if (path !== 'data') {
                payloadPaths.add(path);
            }
        }
    }

    const manifestPaths = new Set(manifestEntries.map((m) => m.path));
    const toCheck = [...manifestEntries];
    const total = toCheck.length + countTagFiles(bag);
    let current = 0;

    for (const item of toCheck) {
        current += 1;
        onProgress?.({ current, total, path: item.path });

        const entry = bag.byPath.get(item.path);
        if (!entry) {
            files.push({
                path: item.path,
                status: 'missing',
                expected: item.checksum,
                message: 'Listed in manifest but not present in bag',
            });
            errors.push(`Missing payload file: ${item.path}`);
            continue;
        }

        const actual = await hashBlob(await entry.getBlob(), algorithm);
        const expected = item.checksum.toLowerCase();
        if (actual === expected) {
            files.push({
                path: item.path,
                status: 'pass',
                expected,
                actual,
            });
        } else {
            files.push({
                path: item.path,
                status: 'fail',
                expected,
                actual,
                message: 'Checksum mismatch',
            });
            errors.push(`Checksum mismatch: ${item.path}`);
        }
    }

    for (const path of payloadPaths) {
        if (!manifestPaths.has(path)) {
            files.push({
                path,
                status: 'extra',
                message: 'Present under data/ but not listed in manifest',
            });
            errors.push(`Extra payload file not in manifest: ${path}`);
        }
    }

    // Tag manifests
    const tagManifestName = Object.keys(ALGORITHM_BY_TAGMANIFEST).find((name) =>
        bag.byPath.has(name),
    );

    if (tagManifestName) {
        const tagAlgo = ALGORITHM_BY_TAGMANIFEST[tagManifestName];
        const tagText = await readEntryText(bag.byPath.get(tagManifestName));
        const tagEntries = parseManifest(tagText);

        for (const item of tagEntries) {
            current += 1;
            onProgress?.({ current, total, path: item.path });

            // tagmanifest must not list itself
            if (basename(item.path).toLowerCase() === tagManifestName) {
                warnings.push('tagmanifest lists itself; skipping self-check');
                continue;
            }

            const entry = bag.byPath.get(item.path);
            if (!entry) {
                files.push({
                    path: item.path,
                    status: 'missing',
                    expected: item.checksum,
                    message: 'Listed in tagmanifest but not present',
                });
                errors.push(`Missing tag file: ${item.path}`);
                continue;
            }

            const actual = await hashBlob(await entry.getBlob(), tagAlgo);
            const expected = item.checksum.toLowerCase();
            if (actual === expected) {
                files.push({
                    path: item.path,
                    status: 'pass',
                    expected,
                    actual,
                });
            } else {
                files.push({
                    path: item.path,
                    status: 'fail',
                    expected,
                    actual,
                    message: 'Tag checksum mismatch',
                });
                errors.push(`Tag checksum mismatch: ${item.path}`);
            }
        }
    } else {
        warnings.push('No tagmanifest found');
    }

    // Payload-Oxum
    if (bagInfo['Payload-Oxum']) {
        const oxum = parsePayloadOxum(bagInfo['Payload-Oxum']);
        if (oxum) {
            let octetCount = 0;
            let streamCount = 0;
            for (const entry of bag.entries) {
                const path = normalizePath(entry.path);
                if (path.startsWith('data/') && path !== 'data') {
                    streamCount += 1;
                    const size =
                        entry.size || (await entry.getBlob()).size || 0;
                    octetCount += size;
                }
            }

            if (
                oxum.octetCount !== octetCount ||
                oxum.streamCount !== streamCount
            ) {
                errors.push(
                    `Payload-Oxum mismatch: expected ${oxum.octetCount}.${oxum.streamCount}, got ${octetCount}.${streamCount}`,
                );
            }
        } else {
            warnings.push(`Could not parse Payload-Oxum: ${bagInfo['Payload-Oxum']}`);
        }
    }

    return {
        valid: errors.length === 0,
        errors,
        warnings,
        files,
        bagInfo,
        bagitVersion,
        algorithm,
    };
}

function countTagFiles(bag) {
    const tagManifestName = Object.keys(ALGORITHM_BY_TAGMANIFEST).find((name) =>
        bag.byPath.has(name),
    );
    if (!tagManifestName) {
        return 0;
    }

    // Approximate; exact count read later
    return 3;
}

/**
 * Parse bagit.txt key: value lines.
 *
 * @param {string} text
 * @returns {Record<string, string>}
 */
export function parseBagitTxt(text) {
    return parseColonKeyValues(text);
}

/**
 * Parse bag-info.txt (supports folded lines per BagIt).
 *
 * @param {string} text
 * @returns {Record<string, string>}
 */
export function parseBagInfo(text) {
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

function parseColonKeyValues(text) {
    const result = {};
    for (const line of String(text).replace(/\r\n/g, '\n').split('\n')) {
        if (!line.trim()) {
            continue;
        }
        const idx = line.indexOf(':');
        if (idx === -1) {
            continue;
        }
        result[line.slice(0, idx).trim()] = line.slice(idx + 1).trim();
    }

    return result;
}

/**
 * Parse a BagIt manifest file.
 *
 * @param {string} text
 * @returns {{checksum: string, path: string}[]}
 */
export function parseManifest(text) {
    const entries = [];
    for (const raw of String(text).replace(/\r\n/g, '\n').split('\n')) {
        const line = raw.trim();
        if (!line || line.startsWith('#')) {
            continue;
        }

        // checksum then one or more spaces/tabs, then filepath
        const match = line.match(/^([a-fA-F0-9]+)\s+(.+)$/);
        if (!match) {
            continue;
        }

        entries.push({
            checksum: match[1].toLowerCase(),
            path: normalizePath(match[2]),
        });
    }

    return entries;
}

/**
 * @param {string} value
 * @returns {{octetCount: number, streamCount: number}|null}
 */
export function parsePayloadOxum(value) {
    const match = String(value)
        .trim()
        .match(/^(\d+)\.(\d+)$/);
    if (!match) {
        return null;
    }

    return {
        octetCount: Number(match[1]),
        streamCount: Number(match[2]),
    };
}

/**
 * Stream-hash a Blob with the given algorithm name.
 *
 * @param {Blob} blob
 * @param {string} algorithm
 * @returns {Promise<string>} hex digest
 */
export async function hashBlob(blob, algorithm) {
    const hasher = await createHasher(algorithm);
    hasher.init();

    const chunkSize = 1024 * 1024;
    let offset = 0;

    while (offset < blob.size) {
        const chunk = blob.slice(offset, offset + chunkSize);
        const buffer = new Uint8Array(await chunk.arrayBuffer());
        hasher.update(buffer);
        offset += chunkSize;
    }

    return hasher.digest('hex');
}

async function createHasher(algorithm) {
    switch (algorithm) {
        case 'SHA-256':
            return createSHA256();
        case 'SHA-512':
            return createSHA512();
        case 'SHA-1':
            return createSHA1();
        case 'MD5':
            return createMD5();
        default:
            throw new Error(`Unsupported algorithm: ${algorithm}`);
    }
}
