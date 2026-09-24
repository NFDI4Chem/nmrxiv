import { describe, expect, it } from 'vitest';
import { discoverBags, findZipEntries } from '../src/bag/discover.js';
import {
    parseManifest,
    parseBagInfo,
    parseBagitTxt,
    parsePayloadOxum,
    validateBag,
    hashBlob,
} from '../src/bag/validate.js';
import { rewriteNmriumForLocal, applyOnLoadProcessing } from '../src/nmrium/loadLocal.js';
import {
    clearRecentZips,
    displayNameFromPath,
    loadRecentZips,
    rememberZip,
} from '../src/bag/history.js';

const MEMORY_STORE = new Map();

function installLocalStorageMock() {
    MEMORY_STORE.clear();
    globalThis.localStorage = {
        getItem: (key) =>
            MEMORY_STORE.has(key) ? MEMORY_STORE.get(key) : null,
        setItem: (key, value) => {
            MEMORY_STORE.set(key, String(value));
        },
        removeItem: (key) => {
            MEMORY_STORE.delete(key);
        },
        clear: () => MEMORY_STORE.clear(),
    };
}

function entry(path, content) {
    const blob =
        content instanceof Blob
            ? content
            : new Blob([content], { type: 'text/plain' });

    return {
        path,
        size: blob.size,
        getBlob: async () => blob,
    };
}

async function sha256Hex(text) {
    return hashBlob(new Blob([text]), 'SHA-256');
}

describe('parse helpers', () => {
    it('parses bagit.txt', () => {
        const fields = parseBagitTxt(
            'BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n',
        );
        expect(fields['BagIt-Version']).toBe('1.0');
    });

    it('parses bag-info with Payload-Oxum', () => {
        const info = parseBagInfo(
            'Payload-Oxum: 100.2\nBagging-Date: 2026-07-29\n',
        );
        expect(info['Payload-Oxum']).toBe('100.2');
        expect(parsePayloadOxum(info['Payload-Oxum'])).toEqual({
            octetCount: 100,
            streamCount: 2,
        });
    });

    it('parses manifest lines', () => {
        const rows = parseManifest(
            'abc123  data/foo.txt\ndef456\tdata/bar.txt\n',
        );
        expect(rows).toEqual([
            { checksum: 'abc123', path: 'data/foo.txt' },
            { checksum: 'def456', path: 'data/bar.txt' },
        ]);
    });
});

describe('discoverBags', () => {
    it('finds a bag at the root', () => {
        const bags = discoverBags([
            entry('bagit.txt', 'BagIt-Version: 1.0\n'),
            entry('data/a.txt', 'hello'),
        ]);
        expect(bags).toHaveLength(1);
        expect(bags[0].rootPath).toBe('');
        expect(bags[0].byPath.has('bagit.txt')).toBe(true);
        expect(bags[0].byPath.has('data/a.txt')).toBe(true);
    });

    it('finds nested bag roots and rebases paths', () => {
        const bags = discoverBags([
            entry('project/S1/bagit.txt', 'BagIt-Version: 1.0\n'),
            entry('project/S1/data/x.jdf', 'x'),
            entry('project/S2/bagit.txt', 'BagIt-Version: 1.0\n'),
            entry('project/S2/data/y.jdf', 'y'),
        ]);
        expect(bags.map((b) => b.label).sort()).toEqual(['S1', 'S2']);
        expect(bags.find((b) => b.label === 'S1').byPath.has('data/x.jdf')).toBe(
            true,
        );
    });

    it('lists zip entries', () => {
        const zips = findZipEntries([
            entry('S1930.zip', 'x'),
            entry('bagit.txt', 'BagIt-Version: 1.0\n'),
        ]);
        expect(zips).toHaveLength(1);
        expect(zips[0].path).toBe('S1930.zip');
    });
});

describe('validateBag', () => {
    it('passes a valid sha256 bag', async () => {
        const payload = 'spectrum-bytes';
        const checksum = await sha256Hex(payload);
        const bagit = 'BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n';
        const bagInfo = `Payload-Oxum: ${payload.length}.1\nBagging-Date: 2026-01-01\n`;
        const manifest = `${checksum}  data/a.jdf\n`;

        const bagitHash = await sha256Hex(bagit);
        const bagInfoHash = await sha256Hex(bagInfo);
        const manifestHash = await sha256Hex(manifest);
        const tagmanifest = `${bagitHash}  bagit.txt\n${bagInfoHash}  bag-info.txt\n${manifestHash}  manifest-sha256.txt\n`;

        const entries = [
            entry('bagit.txt', bagit),
            entry('bag-info.txt', bagInfo),
            entry('manifest-sha256.txt', manifest),
            entry('tagmanifest-sha256.txt', tagmanifest),
            entry('data/a.jdf', payload),
        ];

        const bags = discoverBags(entries);
        const result = await validateBag(bags[0]);
        expect(result.valid).toBe(true);
        expect(result.errors).toEqual([]);
        expect(result.files.filter((f) => f.status === 'pass').length).toBeGreaterThan(0);
    });

    it('fails on tampered payload', async () => {
        const payload = 'spectrum-bytes';
        const checksum = await sha256Hex(payload);
        const bagit = 'BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n';
        const bagInfo = `Payload-Oxum: ${payload.length}.1\n`;
        const manifest = `${checksum}  data/a.jdf\n`;

        const entries = [
            entry('bagit.txt', bagit),
            entry('bag-info.txt', bagInfo),
            entry('manifest-sha256.txt', manifest),
            entry('data/a.jdf', 'TAMPERED'),
        ];

        const bags = discoverBags(entries);
        const result = await validateBag(bags[0]);
        expect(result.valid).toBe(false);
        expect(result.files.some((f) => f.status === 'fail')).toBe(true);
    });

    it('fails on missing payload file', async () => {
        const checksum = await sha256Hex('x');
        const entries = [
            entry(
                'bagit.txt',
                'BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n',
            ),
            entry('manifest-sha256.txt', `${checksum}  data/missing.jdf\n`),
        ];
        const bags = discoverBags(entries);
        const result = await validateBag(bags[0]);
        expect(result.valid).toBe(false);
        expect(result.files.some((f) => f.status === 'missing')).toBe(true);
    });

    it('fails on extra payload file', async () => {
        const payload = 'ok';
        const checksum = await sha256Hex(payload);
        const entries = [
            entry(
                'bagit.txt',
                'BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n',
            ),
            entry('manifest-sha256.txt', `${checksum}  data/a.jdf\n`),
            entry('data/a.jdf', payload),
            entry('data/extra.jdf', 'extra'),
        ];
        const bags = discoverBags(entries);
        const result = await validateBag(bags[0]);
        expect(result.valid).toBe(false);
        expect(result.files.some((f) => f.status === 'extra')).toBe(true);
    });

    it('fails on wrong Payload-Oxum', async () => {
        const payload = 'spectrum-bytes';
        const checksum = await sha256Hex(payload);
        const entries = [
            entry(
                'bagit.txt',
                'BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n',
            ),
            entry('bag-info.txt', 'Payload-Oxum: 1.1\n'),
            entry('manifest-sha256.txt', `${checksum}  data/a.jdf\n`),
            entry('data/a.jdf', payload),
        ];
        const bags = discoverBags(entries);
        const result = await validateBag(bags[0]);
        expect(result.valid).toBe(false);
        expect(result.errors.some((e) => e.includes('Payload-Oxum'))).toBe(
            true,
        );
    });
});

describe('rewriteNmriumForLocal', () => {
    it('strips sources and remaps selector files to basenames', () => {
        const files = [new File([new Uint8Array([1])], 'sample.jdf')];
        const rewritten = rewriteNmriumForLocal(
            {
                nmriumState: {
                    data: {
                        sources: [
                            {
                                id: 'abc',
                                baseURL: 'https://s3.example.com',
                                entries: [{ relativePath: '/remote/a.zip' }],
                            },
                        ],
                        spectra: [
                            {
                                id: '1',
                                selector: {
                                    root: 'abc',
                                    files: [
                                        '/nmrxiv/production/archive/x/sample.jdf',
                                    ],
                                },
                            },
                        ],
                    },
                },
            },
            files,
        );

        expect(rewritten.nmriumState.data.sources).toEqual([]);
        expect(rewritten.nmriumState.data.spectra[0].selector.files).toEqual([
            'sample.jdf',
        ]);
        expect(
            rewritten.nmriumState.data.spectra[0].selector.root,
        ).toBeUndefined();
    });
});

describe('applyOnLoadProcessing', () => {
    it('is a no-op for empty or missing spectra', () => {
        expect(() => applyOnLoadProcessing(null)).not.toThrow();
        expect(() => applyOnLoadProcessing({})).not.toThrow();
        expect(() => applyOnLoadProcessing({ data: { spectra: [] } })).not.toThrow();
    });
});

describe('extractPeaks', () => {
    it('flattens range signals into δ / multiplicity rows', async () => {
        const { extractPeaks } = await import('../src/bag/metadata.js');
        const peaks = extractPeaks({
            ranges: {
                values: [
                    {
                        id: 'r1',
                        signals: [
                            {
                                id: 's1',
                                delta: 7.26,
                                multiplicity: 's',
                            },
                            {
                                id: 's2',
                                originalDelta: 3.5,
                                multiplicity: 't',
                            },
                        ],
                    },
                ],
            },
        });

        expect(peaks).toEqual([
            {
                id: 's1',
                delta: 7.26,
                multiplicity: 's',
                intensity: null,
            },
            {
                id: 's2',
                delta: 3.5,
                multiplicity: 't',
                intensity: null,
            },
        ]);
    });

    it('prefers explicit peaks.values when present', async () => {
        const { extractPeaks } = await import('../src/bag/metadata.js');
        const peaks = extractPeaks({
            peaks: {
                values: [{ id: 'p1', x: 1.2, y: 100, multiplicity: 'd' }],
            },
            ranges: {
                values: [{ signals: [{ id: 's1', delta: 9 }] }],
            },
        });

        expect(peaks).toHaveLength(1);
        expect(peaks[0]).toMatchObject({
            id: 'p1',
            delta: 1.2,
            intensity: 100,
            multiplicity: 'd',
        });
    });
});

describe('molecule smiles extraction', () => {
    it('skips null entries in smiles arrays', async () => {
        const { firstSmiles } = await import('../src/bag/metadata.js');

        expect(firstSmiles([null, null, 'CN1C=NC2=C1C(=O)N(C)C(=O)N2C'])).toBe(
            'CN1C=NC2=C1C(=O)N(C)C(=O)N2C',
        );
        expect(firstSmiles('CCO')).toBe('CCO');
        expect(firstSmiles([null, ''])).toBeNull();
        expect(firstSmiles(null)).toBeNull();
    });
});

describe('extractDatasets null safety', () => {
    it('skips null hasPart entries without throwing', async () => {
        const { extractDatasets } = await import('../src/bag/metadata.js');
        const datasets = extractDatasets(
            {
                hasPart: [
                    null,
                    {
                        '@id': 'd1',
                        name: 'Seeds[sample.jdf]',
                        variableMeasured: [null, { name: 'Nucleus', value: '1H' }],
                        keywords: [null, 'D'],
                    },
                ],
            },
            {
                nmriumState: {
                    data: {
                        spectra: [
                            null,
                            { id: 'sp1', info: { name: 'sample.jdf', nucleus: '1H' } },
                        ],
                    },
                },
            },
        );

        expect(datasets).toHaveLength(1);
        expect(datasets[0].name).toBe('Seeds[sample.jdf]');
        expect(datasets[0].spectrumId).toBe('sp1');
        expect(datasets[0].variables).toEqual([
            { name: 'Nucleus', value: '1H', unit: null },
        ]);
        expect(datasets[0].keywords).toEqual(['D']);
    });
});

describe('recent zip history', () => {
    it('stores zip opens newest-first and dedupes by path', async () => {
        installLocalStorageMock();

        await rememberZip({ name: 'S1930', path: '/Downloads/S1930.zip' });
        await rememberZip({ name: 'other', path: '/tmp/other.zip' });
        await rememberZip({ name: 'S1930', path: '/Downloads/S1930.zip' });

        const recent = loadRecentZips();
        expect(recent).toHaveLength(2);
        expect(recent[0]).toMatchObject({
            name: 'S1930',
            path: '/Downloads/S1930.zip',
        });
        expect(recent[1].path).toBe('/tmp/other.zip');
    });

    it('derives display names from zip filenames', () => {
        expect(displayNameFromPath('/foo/S1930.zip')).toBe('S1930');
        expect(displayNameFromPath('bag.ZIP')).toBe('bag');
    });

    it('clears history', async () => {
        installLocalStorageMock();
        await rememberZip({ path: 'a.zip' });
        expect(loadRecentZips()).toHaveLength(1);
        expect(await clearRecentZips()).toEqual([]);
        expect(loadRecentZips()).toEqual([]);
    });
});
