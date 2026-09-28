import { markRaw } from 'vue';
import { FileCollection } from 'file-collection';
import init from '@zakodium/nmrium-core-plugins';
import { Filters1DManager, Filters2DManager } from 'nmr-processing';
import { basename, normalizePath } from '../bag/sources.js';

let sharedCore = null;

/**
 * Same on-load pipeline NMRium's UI uses (workspace default): attach FT /
 * phase filters for FID spectra so initiateDatum1D / reapplyFilters can run.
 */
const ON_LOAD_PROCESSING = {
    autoProcessing: true,
};

/**
 * Shared NMRiumCore instance with default plugins (JEOL, JCAMP, etc.).
 * Marked raw so Vue never Proxy-wraps classes with private fields.
 */
export function getNmriumCore() {
    if (!sharedCore) {
        sharedCore = markRaw(init());
    }

    return sharedCore;
}

/**
 * Collect payload spectrum files and prepare an NMRium state + aggregator
 * that can be passed directly to <NMRium state aggregator />.
 *
 * @param {import('../bag/discover.js').DiscoveredBag} bag
 * @param {object|null} nmriumParsed
 * @returns {Promise<{ state: object|null, aggregator: FileCollection|null, warnings: string[], core: object }>}
 */
export async function prepareLocalNmriumFiles(bag, nmriumParsed) {
    const warnings = [];
    const core = getNmriumCore();
    const spectrumFiles = [];

    for (const entry of bag.entries) {
        const path = normalizePath(entry.path);
        if (!path.startsWith('data/')) {
            continue;
        }

        const name = basename(path).toLowerCase();
        if (
            name.endsWith('.jdf') ||
            name.endsWith('.dx') ||
            name.endsWith('.jdx') ||
            name.endsWith('.jcamp') ||
            name.endsWith('.nmr')
        ) {
            const blob = await entry.getBlob();
            spectrumFiles.push(
                new File([blob], basename(path), {
                    type: blob.type || 'application/octet-stream',
                }),
            );
        }
    }

    if (spectrumFiles.length === 0) {
        warnings.push('No raw spectrum files (.jdf/.dx) found in bag payload');

        return { state: null, aggregator: null, warnings, core };
    }

    try {
        const fileCollection = markRaw(new FileCollection());
        await fileCollection.appendFileList(spectrumFiles);
        const { state, aggregator } = await core.read(fileCollection, {
            onLoadProcessing: ON_LOAD_PROCESSING,
        });

        applyOnLoadProcessing(state);

        if (nmriumParsed) {
            mergeStoredAnnotations(state, nmriumParsed, warnings);
        }

        return {
            state: markRaw(state),
            aggregator: markRaw(aggregator),
            warnings,
            core,
        };
    } catch (error) {
        warnings.push(`Failed to parse spectra: ${error.message}`);

        return { state: null, aggregator: null, warnings, core };
    }
}

/**
 * Run the attached auto-processing filter chain (FFT, phase, …) so spectra
 * are frequency-domain before NMRium mounts. Without this, JEOL FIDs stay
 * in the time domain even though filters were registered.
 *
 * @param {object} state
 */
export function applyOnLoadProcessing(state) {
    const spectra = state?.data?.spectra;
    if (!Array.isArray(spectra)) {
        return;
    }

    for (const spectrum of spectra) {
        if (!spectrum?.filters?.length) {
            continue;
        }

        try {
            if (spectrum.info?.dimension === 2) {
                Filters2DManager.reapplyFilters(spectrum);
            } else {
                Filters1DManager.reapplyFilters(spectrum);
            }
        } catch {
            // Leave FID as-is; user can still process manually in NMRium.
        }
    }
}

/**
 * Copy ranges / peaks / integrals from the archived .nmrium onto freshly
 * parsed spectra, matching by original filename.
 *
 * @param {object} state
 * @param {object} nmriumParsed
 * @param {string[]} warnings
 */
export function mergeStoredAnnotations(state, nmriumParsed, warnings = []) {
    const loaded = state?.data?.spectra;
    const stored = nmriumParsed?.nmriumState?.data?.spectra;

    if (!Array.isArray(loaded) || !Array.isArray(stored)) {
        return;
    }

    const byName = new Map();
    for (const spectrum of stored) {
        const name = spectrum?.info?.name;
        if (name) {
            byName.set(String(name).toLowerCase(), spectrum);
        }

        const files = spectrum?.selector?.files;
        if (Array.isArray(files)) {
            for (const filePath of files) {
                byName.set(basename(String(filePath)).toLowerCase(), spectrum);
            }
        }
    }

    let merged = 0;
    for (const spectrum of loaded) {
        const candidates = [
            spectrum?.info?.name,
            spectrum?.display?.name,
            ...(Array.isArray(spectrum?.selector?.files)
                ? spectrum.selector.files.map((f) => basename(String(f)))
                : []),
        ]
            .filter(Boolean)
            .map((n) => String(n).toLowerCase());

        let match = null;
        for (const candidate of candidates) {
            match = byName.get(candidate);
            if (match) {
                break;
            }
        }

        if (!match) {
            continue;
        }

        if (match.ranges) {
            spectrum.ranges = structuredClone(match.ranges);
        }
        if (match.peaks) {
            spectrum.peaks = structuredClone(match.peaks);
        }
        if (match.integrals) {
            spectrum.integrals = structuredClone(match.integrals);
        }
        merged += 1;
    }

    if (merged === 0 && stored.length > 0) {
        warnings.push(
            'Could not match stored ranges/peaks onto loaded spectra by filename',
        );
    }
}

/**
 * Rewrite helper kept for unit tests / documentation of the offline contract.
 *
 * @param {object} nmrium
 * @param {File[]} spectrumFiles
 * @returns {object}
 */
export function rewriteNmriumForLocal(nmrium, spectrumFiles) {
    const clone = structuredClone(nmrium);
    const fileByBasename = new Map(
        spectrumFiles.map((f) => [f.name.toLowerCase(), f.name]),
    );

    const spectra = clone?.nmriumState?.data?.spectra;
    if (Array.isArray(spectra)) {
        for (const spectrum of spectra) {
            const files = spectrum?.selector?.files;
            if (!Array.isArray(files)) {
                continue;
            }

            spectrum.selector.files = files.map((filePath) => {
                const name = basename(String(filePath));
                const local = fileByBasename.get(name.toLowerCase());

                return local || name;
            });

            if (spectrum.selector) {
                delete spectrum.selector.root;
            }
        }
    }

    if (clone?.nmriumState?.data) {
        clone.nmriumState.data.sources = [];
    }

    return clone;
}
