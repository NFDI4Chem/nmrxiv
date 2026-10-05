/**
 * Read dropped or picked files and folders into a flat list of entries with
 * their relative paths. Ported from resources/bagit-viewer/src/bag/sources.js.
 */

/**
 * @typedef {object} DroppedEntry
 * @property {string} path Path relative to the dropped item (unix separators)
 * @property {number} size Byte size
 * @property {() => Promise<File>} getFile
 */

export function normalizePath(path) {
    return String(path || "")
        .replace(/\\/g, "/")
        .replace(/^\.\//, "")
        .replace(/^\/+/, "");
}

/**
 * Entries from an <input type="file"> (with or without webkitdirectory).
 *
 * @param {FileList|File[]} fileList
 * @returns {DroppedEntry[]}
 */
export function entriesFromFileList(fileList) {
    return Array.from(fileList || []).map((file) => ({
        path: normalizePath(
            file.webkitRelativePath && file.webkitRelativePath.length > 0
                ? file.webkitRelativePath
                : file.name
        ),
        size: file.size,
        getFile: async () => file,
    }));
}

/**
 * Entries from a drop event, walking dropped folders recursively.
 *
 * @param {DataTransfer} dataTransfer
 * @returns {Promise<DroppedEntry[]>}
 */
export async function entriesFromDataTransfer(dataTransfer) {
    const items = Array.from(dataTransfer.items || []);
    const roots = items
        .map((item) =>
            typeof item.webkitGetAsEntry === "function"
                ? item.webkitGetAsEntry()
                : null
        )
        .filter(Boolean);

    if (roots.length === 0) {
        return entriesFromFileList(dataTransfer.files);
    }

    const entries = [];
    for (const root of roots) {
        await walkEntry(root, "", entries);
    }

    return entries;
}

async function walkEntry(entry, parentPath, out) {
    const path = parentPath ? `${parentPath}/${entry.name}` : entry.name;

    if (entry.isFile) {
        const file = await new Promise((resolve, reject) =>
            entry.file(resolve, reject)
        );
        out.push({
            path: normalizePath(path),
            size: file.size,
            getFile: async () => file,
        });

        return;
    }

    if (entry.isDirectory) {
        for (const child of await readAllDirectoryEntries(
            entry.createReader()
        )) {
            await walkEntry(child, path, out);
        }
    }
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
