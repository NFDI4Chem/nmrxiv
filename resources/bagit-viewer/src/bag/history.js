const STORAGE_KEY = 'nmrxiv-bagit-viewer:recent-zips';
const IDB_NAME = 'nmrxiv-bagit-viewer';
const IDB_STORE = 'zip-blobs';
const IDB_VERSION = 1;
const MAX_ENTRIES = 20;

/**
 * @typedef {object} RecentZip
 * @property {string} name Display name (usually zip basename without .zip)
 * @property {string} path Best-known path or filename
 * @property {string} openedAt ISO timestamp
 */

/**
 * @returns {RecentZip[]}
 */
export function loadRecentZips() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
            return [];
        }

        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) {
            return [];
        }

        return parsed
            .filter(
                (item) =>
                    item &&
                    typeof item.name === 'string' &&
                    typeof item.path === 'string',
            )
            .slice(0, MAX_ENTRIES);
    } catch {
        return [];
    }
}

/**
 * Remember a successfully opened zip and cache its bytes for re-open.
 * Newest first; deduped by path.
 *
 * @param {{ name?: string, path?: string, file?: Blob|File }} input
 * @returns {Promise<RecentZip[]>}
 */
export async function rememberZip(input) {
    const path = resolvePath(input);
    if (!path) {
        return loadRecentZips();
    }

    const name =
        (input.name && String(input.name).trim()) ||
        displayNameFromPath(path);

    const entry = {
        name,
        path,
        openedAt: new Date().toISOString(),
    };

    const existing = loadRecentZips().filter(
        (item) => item.path.toLowerCase() !== path.toLowerCase(),
    );
    const next = [entry, ...existing].slice(0, MAX_ENTRIES);
    const keptPaths = new Set(next.map((item) => item.path.toLowerCase()));
    const dropped = existing.filter(
        (item) => !keptPaths.has(item.path.toLowerCase()),
    );

    saveRecentZips(next);

    if (input.file) {
        await putZipBlob(path, name, input.file);
    }

    for (const item of dropped) {
        await deleteZipBlob(item.path);
    }

    return next;
}

/**
 * Load a previously cached zip as a File for re-opening.
 *
 * @param {string} path
 * @returns {Promise<File|null>}
 */
export async function loadRecentZipFile(path) {
    const record = await getZipBlob(path);
    if (!record?.blob) {
        return null;
    }

    const filename =
        displayNameFromPath(path).endsWith('.zip') ||
        String(path).toLowerCase().endsWith('.zip')
            ? basename(path)
            : `${displayNameFromPath(path)}.zip`;

    return new File([record.blob], filename, {
        type: record.blob.type || 'application/zip',
        lastModified: Date.parse(record.savedAt) || Date.now(),
    });
}

/**
 * @returns {Promise<RecentZip[]>}
 */
export async function clearRecentZips() {
    saveRecentZips([]);
    await clearZipBlobs();

    return [];
}

/**
 * @param {RecentZip[]} entries
 */
function saveRecentZips(entries) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(entries));
    } catch {
        // Quota / private mode — ignore.
    }
}

/**
 * @param {{ name?: string, path?: string, file?: Blob|File }} input
 * @returns {string}
 */
function resolvePath(input) {
    if (input.path && String(input.path).trim()) {
        return String(input.path).trim();
    }

    const file = input.file;
    if (!file) {
        return '';
    }

    if (file instanceof File) {
        if (typeof file.path === 'string' && file.path.length > 0) {
            return file.path;
        }

        if (
            typeof file.webkitRelativePath === 'string' &&
            file.webkitRelativePath.length > 0
        ) {
            return file.webkitRelativePath;
        }

        return file.name || '';
    }

    return '';
}

/**
 * @param {string} path
 * @returns {string}
 */
export function displayNameFromPath(path) {
    const base = basename(path);

    return base.replace(/\.zip$/i, '') || base;
}

/**
 * @param {string} path
 * @returns {string}
 */
function basename(path) {
    return String(path).replace(/\\/g, '/').split('/').pop() || path;
}

/**
 * @returns {Promise<IDBDatabase>}
 */
function openDb() {
    return new Promise((resolve, reject) => {
        if (typeof indexedDB === 'undefined') {
            reject(new Error('IndexedDB is not available'));

            return;
        }

        const request = indexedDB.open(IDB_NAME, IDB_VERSION);

        request.onerror = () =>
            reject(request.error || new Error('Failed to open IndexedDB'));
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains(IDB_STORE)) {
                db.createObjectStore(IDB_STORE, { keyPath: 'path' });
            }
        };
    });
}

/**
 * @param {string} path
 * @param {string} name
 * @param {Blob|File} blob
 */
async function putZipBlob(path, name, blob) {
    try {
        const db = await openDb();
        await new Promise((resolve, reject) => {
            const tx = db.transaction(IDB_STORE, 'readwrite');
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
            tx.objectStore(IDB_STORE).put({
                path,
                name,
                blob,
                savedAt: new Date().toISOString(),
            });
        });
        db.close();
    } catch {
        // Storage full / private mode — history list still works, reopen won't.
    }
}

/**
 * @param {string} path
 * @returns {Promise<{ path: string, name: string, blob: Blob, savedAt: string }|null>}
 */
async function getZipBlob(path) {
    try {
        const db = await openDb();
        const record = await new Promise((resolve, reject) => {
            const tx = db.transaction(IDB_STORE, 'readonly');
            const request = tx.objectStore(IDB_STORE).get(path);
            request.onsuccess = () => resolve(request.result || null);
            request.onerror = () => reject(request.error);
        });
        db.close();

        return record;
    } catch {
        return null;
    }
}

/**
 * @param {string} path
 */
async function deleteZipBlob(path) {
    try {
        const db = await openDb();
        await new Promise((resolve, reject) => {
            const tx = db.transaction(IDB_STORE, 'readwrite');
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
            tx.objectStore(IDB_STORE).delete(path);
        });
        db.close();
    } catch {
        // ignore
    }
}

async function clearZipBlobs() {
    try {
        const db = await openDb();
        await new Promise((resolve, reject) => {
            const tx = db.transaction(IDB_STORE, 'readwrite');
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
            tx.objectStore(IDB_STORE).clear();
        });
        db.close();
    } catch {
        // ignore
    }
}
