import { entriesFromZipBlob } from './sources.js';
import { discoverBags } from './discover.js';

/**
 * Open one or more zip Files/Blobs and return discovered BagIt bags.
 *
 * @param {Array<File|Blob>} files
 * @returns {Promise<import('./discover.js').DiscoveredBag[]>}
 */
export async function bagsFromZipFiles(files) {
    const bags = [];

    for (const file of files) {
        const zipName =
            file instanceof File && file.name
                ? file.name
                : 'archive.zip';
        const { entries, zipName: resolvedName, dispose } =
            await entriesFromZipBlob(file, zipName);
        const zipBags = discoverBags(entries, {
            kind: 'zip',
            zipName: resolvedName,
        });

        for (const bag of zipBags) {
            bag._dispose = dispose;
            if (file instanceof File) {
                bag._sourceFile = file;
            } else {
                bag._sourceFile = new File([file], zipName, {
                    type: file.type || 'application/zip',
                });
            }
        }

        bags.push(...zipBags);
    }

    return bags;
}
