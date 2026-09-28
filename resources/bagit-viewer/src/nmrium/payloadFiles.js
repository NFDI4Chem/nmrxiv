import { basename, normalizePath } from '../bag/sources.js';

/**
 * Payload files that are never spectra. Bruker trees (fid, 1r, acqus, …)
 * have no extension and must stay included.
 */
const SKIP_EXTENSIONS = new Set([
    'png',
    'jpg',
    'jpeg',
    'gif',
    'webp',
    'svg',
    'pdf',
    'json',
    'html',
    'htm',
    'csv',
    'tsv',
    'md',
    'txt',
    'mol',
    'sdf',
    'cdxml',
    'doc',
    'docx',
    'xls',
    'xlsx',
    'xml',
    'log',
]);

/**
 * Relative path inside the BagIt payload for a file NMRium should see.
 * Keeps directory structure (`10/pdata/1/1r`) so Bruker experiments stay intact.
 *
 * @param {string} path
 * @returns {string|null}
 */
export function payloadFileRelativePath(path) {
    const normalized = normalizePath(path);

    if (!normalized.startsWith('data/') || normalized.endsWith('/')) {
        return null;
    }

    if (
        normalized.includes('/nmrxiv-meta/') ||
        normalized.includes('/__MACOSX/')
    ) {
        return null;
    }

    const name = basename(normalized);
    const lower = name.toLowerCase();

    if (
        lower === 'readme' ||
        lower === 'readme.txt' ||
        lower === 'thumbs.db' ||
        lower === '.ds_store'
    ) {
        return null;
    }

    const dot = lower.lastIndexOf('.');
    if (dot > 0 && SKIP_EXTENSIONS.has(lower.slice(dot + 1))) {
        return null;
    }

    return normalized.slice('data/'.length);
}

/**
 * File whose relative path survives FileCollection.appendFileList.
 * Basename-only File names collapse Bruker folders into one directory.
 *
 * @param {Blob} blob
 * @param {string} relativePath
 * @returns {File}
 */
export function fileFromPayloadEntry(blob, relativePath) {
    const file = new File([blob], basename(relativePath), {
        type: blob.type || 'application/octet-stream',
    });

    defineFilePath(file, 'webkitRelativePath', relativePath);
    defineFilePath(file, 'path', relativePath);

    return file;
}

/**
 * @param {File} file
 * @param {'webkitRelativePath'|'path'} key
 * @param {string} relativePath
 */
function defineFilePath(file, key, relativePath) {
    try {
        Object.defineProperty(file, key, {
            value: relativePath,
            configurable: true,
        });
    } catch {
        // Some hosts expose File.path as a non-configurable filesystem path.
    }
}
