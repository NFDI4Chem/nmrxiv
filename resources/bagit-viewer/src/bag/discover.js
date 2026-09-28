import { basename, dirname, indexEntries, normalizePath } from './sources.js';

/**
 * @typedef {object} DiscoveredBag
 * @property {string} id Stable id for UI keys
 * @property {string} label Display name
 * @property {string} rootPath Bag root relative to the source ('' for zip root)
 * @property {'folder'|'zip'} kind
 * @property {string|null} zipName
 * @property {import('./sources.js').BagEntry[]} entries All entries belonging to this bag (paths relative to bag root)
 * @property {Map<string, import('./sources.js').BagEntry>} byPath
 */

/**
 * Discover BagIt bags in a flat entry list.
 *
 * A bag root is any directory (including '') that contains bagit.txt.
 * Additionally, any *.zip entry is treated as a potential bag container;
 * callers should expand those separately via entriesFromZipBlob and re-run
 * discoverBags on the expanded entries.
 *
 * @param {import('./sources.js').BagEntry[]} entries
 * @param {{ zipName?: string|null, kind?: 'folder'|'zip' }} [options]
 * @returns {DiscoveredBag[]}
 */
export function discoverBags(entries, options = {}) {
    const bagRoots = new Set();

    for (const entry of entries) {
        const path = normalizePath(entry.path);
        const name = basename(path).toLowerCase();

        if (name === 'bagit.txt') {
            bagRoots.add(dirname(path));
        }
    }

    const bags = [];

    for (const root of [...bagRoots].sort()) {
        const prefix = root === '' ? '' : `${root}/`;
        const bagEntries = [];

        for (const entry of entries) {
            const path = normalizePath(entry.path);

            if (root === '') {
                // Only include entries that are not under a nested bag root
                // other than this one — for root bags, take everything that
                // doesn't start with another bag's exclusive prefix? Simpler:
                // include every entry whose path is under this root.
                if (!isUnderOtherRoot(path, root, bagRoots)) {
                    bagEntries.push(rebaseEntry(entry, root));
                }
            } else if (path === root || path.startsWith(prefix)) {
                if (!isUnderOtherRoot(path, root, bagRoots)) {
                    bagEntries.push(rebaseEntry(entry, root));
                }
            }
        }

        const label =
            root !== ''
                ? basename(root)
                : options.zipName
                  ? stripZipExtension(options.zipName)
                  : 'Bag';

        bags.push({
            id: `${options.kind || 'folder'}:${options.zipName || ''}:${root || '/'}`,
            label,
            rootPath: root,
            kind: options.kind || (options.zipName ? 'zip' : 'folder'),
            zipName: options.zipName || null,
            entries: bagEntries,
            byPath: indexEntries(bagEntries),
        });
    }

    return bags.sort((a, b) => a.label.localeCompare(b.label));
}

/**
 * Find zip files among entries that look like they might be bags
 * (we cannot peek without opening; return all .zip files).
 *
 * @param {import('./sources.js').BagEntry[]} entries
 * @returns {import('./sources.js').BagEntry[]}
 */
export function findZipEntries(entries) {
    return entries.filter((entry) =>
        normalizePath(entry.path).toLowerCase().endsWith('.zip'),
    );
}

function rebaseEntry(entry, root) {
    const path = normalizePath(entry.path);
    let relative = path;

    if (root !== '') {
        const prefix = `${root}/`;
        relative =
            path === root ? '' : path.startsWith(prefix) ? path.slice(prefix.length) : path;
    }

    return {
        path: relative,
        size: entry.size,
        getBlob: entry.getBlob,
    };
}

function isUnderOtherRoot(path, thisRoot, allRoots) {
    for (const other of allRoots) {
        if (other === thisRoot || other === '') {
            continue;
        }

        // other is a nested bag only if it is strictly under thisRoot
        const thisPrefix = thisRoot === '' ? '' : `${thisRoot}/`;
        if (thisRoot !== '' && !other.startsWith(thisPrefix) && other !== thisRoot) {
            continue;
        }

        if (thisRoot === '' || other.startsWith(thisPrefix)) {
            const otherPrefix = `${other}/`;
            if (path === other || path.startsWith(otherPrefix)) {
                // Prefer the deepest matching root: skip if other is deeper than thisRoot
                if (other.length > thisRoot.length) {
                    return true;
                }
            }
        }
    }

    return false;
}

function stripZipExtension(name) {
    return name.replace(/\.zip$/i, '');
}
