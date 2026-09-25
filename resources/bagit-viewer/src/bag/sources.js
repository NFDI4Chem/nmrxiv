/**
 * Normalize path separators and strip leading ./ or /
 */
export function normalizePath(path) {
    return String(path || '')
        .replace(/\\/g, '/')
        .replace(/^\.\//, '')
        .replace(/^\/+/, '');
}

/**
 * @typedef {object} BagEntry
 * @property {string} path Absolute path within the source (unix separators)
 * @property {number} size Byte size when known, otherwise 0
 * @property {() => Promise<Blob>} getBlob
 */

/**
 * Build entries from an HTML file input with webkitdirectory, or a FileList
 * of loose files that already carry webkitRelativePath / name.
 *
 * @param {FileList|File[]} fileList
 * @returns {BagEntry[]}
 */
export function entriesFromFileList(fileList) {
    const files = Array.from(fileList || []);

    return files.map((file) => {
        const relative =
            file.webkitRelativePath && file.webkitRelativePath.length > 0
                ? file.webkitRelativePath
                : file.name;

        return {
            path: normalizePath(relative),
            size: file.size,
            getBlob: async () => file,
        };
    });
}

/**
 * Recursively read a dropped DirectoryEntry / FileEntry tree via File System API.
 *
 * @param {DataTransfer} dataTransfer
 * @returns {Promise<BagEntry[]>}
 */
export async function entriesFromDataTransfer(dataTransfer) {
    const items = Array.from(dataTransfer.items || []);
    const entries = [];

    for (const item of items) {
        const entry =
            typeof item.webkitGetAsEntry === 'function'
                ? item.webkitGetAsEntry()
                : null;

        if (entry) {
            await walkEntry(entry, '', entries);
            continue;
        }

        const file = item.getAsFile?.();
        if (file) {
            entries.push({
                path: normalizePath(file.name),
                size: file.size,
                getBlob: async () => file,
            });
        }
    }

    if (entries.length === 0 && dataTransfer.files?.length) {
        return entriesFromFileList(dataTransfer.files);
    }

    return entries;
}

async function walkEntry(entry, parentPath, out) {
    if (entry.isFile) {
        const file = await readFileEntry(entry);
        const path = normalizePath(
            parentPath ? `${parentPath}/${entry.name}` : entry.name,
        );
        out.push({
            path,
            size: file.size,
            getBlob: async () => file,
        });

        return;
    }

    if (entry.isDirectory) {
        const dirPath = parentPath
            ? `${parentPath}/${entry.name}`
            : entry.name;
        const reader = entry.createReader();
        const children = await readAllDirectoryEntries(reader);

        for (const child of children) {
            await walkEntry(child, dirPath, out);
        }
    }
}

function readFileEntry(fileEntry) {
    return new Promise((resolve, reject) => {
        fileEntry.file(resolve, reject);
    });
}

function readAllDirectoryEntries(reader) {
    return new Promise((resolve, reject) => {
        const entries = [];

        const readBatch = () => {
            reader.readEntries((batch) => {
                if (!batch.length) {
                    resolve(entries);

                    return;
                }

                entries.push(...batch);
                readBatch();
            }, reject);
        };

        readBatch();
    });
}

/**
 * Build entries from a zip Blob using @zip.js/zip.js (lazy per-entry getBlob).
 *
 * @param {Blob} zipBlob
 * @param {string} [zipName='archive.zip']
 * @returns {Promise<{entries: BagEntry[], zipName: string}>}
 */
export async function entriesFromZipBlob(zipBlob, zipName = 'archive.zip') {
    const { ZipReader, BlobReader, BlobWriter } = await import('@zip.js/zip.js');
    const reader = new ZipReader(new BlobReader(zipBlob));
    const zipEntries = await reader.getEntries();
    const entries = [];

    for (const zipEntry of zipEntries) {
        if (zipEntry.directory) {
            continue;
        }

        const path = normalizePath(zipEntry.filename);
        entries.push({
            path,
            size: zipEntry.uncompressedSize ?? 0,
            getBlob: async () => {
                const data = await zipEntry.getData(new BlobWriter());

                return data;
            },
        });
    }

    // Keep the ZipReader open for lazy reads; caller should not close early.
    // Attach a dispose helper for when the bag is discarded.
    entries._dispose = async () => {
        try {
            await reader.close();
        } catch {
            // ignore
        }
    };
    entries._zipName = zipName;

    return { entries, zipName, dispose: entries._dispose };
}

/**
 * Index entries by normalized path for O(1) lookup.
 *
 * @param {BagEntry[]} entries
 * @returns {Map<string, BagEntry>}
 */
export function indexEntries(entries) {
    const map = new Map();
    for (const entry of entries) {
        map.set(normalizePath(entry.path), entry);
    }

    return map;
}

/**
 * Read an entry as UTF-8 text.
 *
 * @param {BagEntry} entry
 * @returns {Promise<string>}
 */
export async function readEntryText(entry) {
    const blob = await entry.getBlob();

    return blob.text();
}

/**
 * Read an entry as JSON.
 *
 * @param {BagEntry} entry
 * @returns {Promise<any>}
 */
export async function readEntryJson(entry) {
    const text = await readEntryText(entry);

    return JSON.parse(text);
}

/**
 * Basename of a path.
 *
 * @param {string} path
 * @returns {string}
 */
export function basename(path) {
    const normalized = normalizePath(path);
    const parts = normalized.split('/');

    return parts[parts.length - 1] || normalized;
}

/**
 * Directory name of a path (no trailing slash).
 *
 * @param {string} path
 * @returns {string}
 */
export function dirname(path) {
    const normalized = normalizePath(path);
    const idx = normalized.lastIndexOf('/');

    return idx === -1 ? '' : normalized.slice(0, idx);
}
