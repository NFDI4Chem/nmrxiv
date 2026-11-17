import { MICHI_CONSUMED_INFO_KEYS, extractMichiRows } from './michi.js';

// Same ordering and labels as resources/js/Shared/Public/PublicDatasetBody.vue
const PRIORITY = [
    'nucleus',
    'experiment',
    'type',
    'solvent',
    'name',
    'title',
    'date',
    'localeDate',
    'baseFrequency',
    'originFrequency',
    'numberOfPoints',
    'pulseSequence',
    'creator',
    'owner',
    'dimension',
    'dataClass',
];

const LABELS = {
    nucleus: 'Nucleus',
    experiment: 'Experiment',
    type: 'Type',
    solvent: 'Solvent',
    name: 'Name',
    title: 'Title',
    date: 'Date',
    localeDate: 'Recorded',
    baseFrequency: 'Base frequency',
    originFrequency: 'Origin frequency',
    numberOfPoints: 'Points',
    pulseSequence: 'Pulse sequence',
    creator: 'Creator',
    owner: 'Owner',
    dimension: 'Dimension',
    dataClass: 'Data class',
    isFt: 'FT',
    isFid: 'FID',
    acquisitionMode: 'Acquisition mode',
    frequencyOffset: 'Frequency offset',
};

function formatCell(value) {
    if (value === null || value === undefined) {
        return '—';
    }
    if (Array.isArray(value)) {
        return value
            .map((v) =>
                v !== null && typeof v === 'object'
                    ? JSON.stringify(v)
                    : String(v),
            )
            .join(', ');
    }
    if (typeof value === 'object') {
        return JSON.stringify(value);
    }

    return String(value);
}

/**
 * Rows for the "Spectrum info" section of a dataset: MIChI fields first, then
 * the remaining NMRium info keys. When the dataset has no matching NMRium
 * spectrum the bio-schema `variableMeasured` entries are listed instead.
 *
 * @param {object|null} spectrum NMRium spectrum
 * @param {Array<{ name: string|null, value: unknown, unit?: string|null }>} [variables]
 * @returns {Array<{ key: string, label: string, value: string }>}
 */
export function buildSpectrumInfoRows(spectrum, variables = []) {
    const info = spectrum?.info;
    if (info && typeof info === 'object') {
        const consumed = new Set(MICHI_CONSUMED_INFO_KEYS);
        const raw = Object.keys(info)
            .filter((key) => !consumed.has(key))
            .map((key) => ({
                key,
                label: LABELS[key] ?? key,
                value: formatCell(info[key]),
            }));
        const prioritized = PRIORITY.map((key) =>
            raw.find((row) => row.key === key),
        ).filter(Boolean);
        const taken = new Set(prioritized.map((row) => row.key));

        return [
            ...extractMichiRows(spectrum),
            ...prioritized,
            ...raw.filter((row) => !taken.has(row.key)),
        ];
    }

    return variables
        .filter((v) => v.name && v.value != null && v.value !== '')
        .map((v, index) => ({
            key: `var:${index}`,
            label: v.name,
            value: `${formatCell(v.value)}${v.unit ? ` ${v.unit}` : ''}`,
        }));
}
