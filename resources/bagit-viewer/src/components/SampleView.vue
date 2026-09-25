<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold break-words text-gray-900">
                <span class="text-teal-700">{{ metadata.sampleName }}</span>
            </h1>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <span
                    v-if="metadata.doi"
                    class="inline-flex items-center rounded bg-yellow-300 px-2 py-0.5 text-xs font-semibold text-gray-900"
                >
                    DOI: {{ metadata.doi }}
                </span>
                <a
                    v-if="metadata.url"
                    :href="metadata.url"
                    class="text-xs font-medium text-teal-700 underline"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Original record
                </a>
            </div>
        </div>

        <ValidationPanel
            :running="validating"
            :progress="progress"
            :result="validation"
            :algorithm="validation?.algorithm"
        />

        <div
            v-if="validation && !validation.valid"
            class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"
        >
            Checksums did not all pass. Spectra are still shown below, but treat
            this archive as possibly corrupted or altered.
        </div>

        <p
            v-if="metadata.description"
            class="rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-wrap"
        >
            {{ truncate(metadata.description, 1200) }}
        </p>

        <div>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">Spectra</h2>
            <p v-if="nmriumError" class="mb-2 text-sm text-red-600">
                {{ nmriumError }}
            </p>
            <p
                v-for="(w, i) in nmriumWarnings"
                :key="'nw' + i"
                class="mb-1 text-sm text-amber-700"
            >
                {{ w }}
            </p>
            <NmriumPanel
                v-if="prepared?.state && prepared?.aggregator"
                :prepared="prepared"
                @error="onNmriumError"
            />
            <div
                v-else-if="!validating"
                class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center text-sm text-gray-500"
            >
                No spectra prepared for interactive viewing.
            </div>
        </div>

        <div v-if="metadata.molecules.length">
            <h2 class="mb-3 text-lg font-semibold text-gray-900">
                {{
                    metadata.molecules.length === 1 ? 'Molecule' : 'Molecules'
                }}
            </h2>
            <div class="space-y-4">
                <article
                    v-for="mol in metadata.molecules"
                    :key="mol.id || mol.inChIKey || mol.smiles"
                    class="flex flex-col gap-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-inset ring-gray-900/5 sm:flex-row sm:items-start"
                >
                    <div
                        v-if="moleculeSvg(mol.smiles)"
                        class="mx-auto flex h-48 w-56 shrink-0 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 p-3 sm:mx-0"
                        v-html="moleculeSvg(mol.smiles)"
                    ></div>
                    <div
                        v-else
                        class="mx-auto flex h-48 w-56 shrink-0 items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50 text-center text-xs text-gray-400 sm:mx-0"
                    >
                        Structure unavailable
                    </div>
                    <dl
                        class="min-w-0 flex-1 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2"
                    >
                        <div v-if="mol.name" class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Name
                            </dt>
                            <dd class="mt-0.5 font-medium text-gray-900">
                                {{ mol.name }}
                            </dd>
                        </div>
                        <div v-if="mol.molecularFormula">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Formula
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-900"
                                v-html="formatFormula(mol.molecularFormula)"
                            ></dd>
                        </div>
                        <div v-if="mol.molecularWeight">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Weight
                            </dt>
                            <dd class="mt-0.5 tabular-nums text-gray-900">
                                {{ mol.molecularWeight }}
                            </dd>
                        </div>
                        <div v-if="mol.inChIKey" class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                InChIKey
                            </dt>
                            <dd
                                class="mt-0.5 break-all font-mono text-xs text-gray-800"
                            >
                                {{ mol.inChIKey }}
                            </dd>
                        </div>
                        <div v-if="mol.smiles" class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                SMILES
                            </dt>
                            <dd
                                class="mt-0.5 break-all font-mono text-xs text-gray-800"
                            >
                                {{ mol.smiles }}
                            </dd>
                        </div>
                        <div v-if="mol.url" class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Reference
                            </dt>
                            <dd class="mt-0.5">
                                <a
                                    :href="mol.url"
                                    class="break-all text-teal-700 underline"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ mol.url }}
                                </a>
                            </dd>
                        </div>
                        <div v-if="mol.description" class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Note
                            </dt>
                            <dd class="mt-0.5 text-gray-700">
                                {{ mol.description }}
                            </dd>
                        </div>
                    </dl>
                </article>
            </div>
        </div>

        <div>
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">
                    Spectra datasets
                </h2>
                <span
                    class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium tabular-nums text-gray-700"
                >
                    {{ metadata.datasets.length }}
                </span>
            </div>
            <div
                v-if="!metadata.datasets.length"
                class="rounded-xl border border-dashed border-gray-200 px-6 py-10 text-center text-sm text-gray-500"
            >
                No dataset metadata in bio-schema.json
            </div>
            <div
                v-else
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3"
            >
                <article
                    v-for="dataset in metadata.datasets"
                    :key="dataset.id"
                    class="flex flex-col rounded-2xl bg-white p-4 shadow-sm ring-1 ring-inset ring-gray-900/5"
                >
                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">
                        {{ dataset.name }}
                    </h3>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <span
                            v-if="dataset.nucleus"
                            class="rounded-md bg-gray-50 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-500/10"
                        >
                            {{ dataset.nucleus }}
                        </span>
                        <span
                            v-if="dataset.solvent"
                            class="rounded-md bg-gray-50 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-500/10"
                        >
                            {{ dataset.solvent }}
                        </span>
                        <span
                            v-for="kw in dataset.keywords.slice(0, 3)"
                            :key="kw"
                            class="rounded-md bg-gray-50 px-2 py-0.5 text-[11px] font-medium text-gray-700 ring-1 ring-inset ring-gray-500/10"
                        >
                            {{ kw }}
                        </span>
                    </div>
                    <img
                        v-if="
                            dataset.spectrumId &&
                            metadata.previewObjectUrls.get(dataset.spectrumId)
                        "
                        :src="
                            metadata.previewObjectUrls.get(dataset.spectrumId)
                        "
                        :alt="dataset.name"
                        class="mt-3 w-full rounded-lg border border-gray-100 bg-white object-contain"
                    />
                    <div
                        v-if="dataset.peaks?.length"
                        class="mt-3 overflow-auto"
                    >
                        <table class="min-w-full text-left text-[11px]">
                            <thead class="text-gray-500">
                                <tr>
                                    <th class="pr-2 font-medium">δ (ppm)</th>
                                    <th class="pr-2 font-medium">Mult.</th>
                                    <th
                                        v-if="datasetHasIntensity(dataset)"
                                        class="font-medium"
                                    >
                                        Intensity
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="peak in dataset.peaks.slice(0, 12)"
                                    :key="peak.id"
                                    class="border-t border-gray-50 text-gray-700"
                                >
                                    <td class="pr-2 tabular-nums">
                                        {{ formatNum(peak.delta) }}
                                    </td>
                                    <td class="pr-2">
                                        {{ peak.multiplicity || '—' }}
                                    </td>
                                    <td
                                        v-if="datasetHasIntensity(dataset)"
                                        class="tabular-nums"
                                    >
                                        {{ formatNum(peak.intensity) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p
                            v-if="dataset.peaks.length > 12"
                            class="mt-1 text-[11px] text-gray-400"
                        >
                            +{{ dataset.peaks.length - 12 }} more
                        </p>
                    </div>
                </article>
            </div>
        </div>

        <aside class="grid gap-6 border-t border-gray-100 pt-6 sm:grid-cols-2 lg:grid-cols-3">
            <div v-if="metadata.doi">
                <h3 class="text-sm font-bold text-gray-900">Identifier</h3>
                <p class="mt-1 break-all text-sm text-gray-700">
                    {{ metadata.doi }}
                </p>
            </div>
            <div v-if="metadata.license">
                <h3 class="text-sm font-bold text-gray-900">License</h3>
                <a
                    :href="metadata.license"
                    class="mt-1 block break-all text-sm text-teal-700 underline"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    {{ metadata.license }}
                </a>
            </div>
            <div v-if="metadata.datePublished">
                <h3 class="text-sm font-bold text-gray-900">Published</h3>
                <p class="mt-1 text-sm text-gray-700">
                    {{ formatDate(metadata.datePublished) }}
                </p>
            </div>
            <div v-if="metadata.dateCreated">
                <h3 class="text-sm font-bold text-gray-900">Created</h3>
                <p class="mt-1 text-sm text-gray-700">
                    {{ formatDate(metadata.dateCreated) }}
                </p>
            </div>
            <div v-if="metadata.keywords.length" class="sm:col-span-2">
                <h3 class="text-sm font-bold text-gray-900">Tags</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="tag in metadata.keywords"
                        :key="tag"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-900"
                    >
                        {{ tag }}
                    </span>
                </div>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { markRaw, ref, shallowRef, watch } from 'vue';
import { Molecule } from 'openchemlib';
import ValidationPanel from './ValidationPanel.vue';
import NmriumPanel from './NmriumPanel.vue';
import { validateBag } from '../bag/validate.js';
import { loadBagMetadata, revokePreviewUrls } from '../bag/metadata.js';
import { prepareLocalNmriumFiles } from '../nmrium/loadLocal.js';

const props = defineProps({
    bag: { type: Object, required: true },
});

const validating = ref(false);
const progress = ref({ current: 0, total: 0, path: '' });
const validation = ref(null);
const metadata = ref({
    sampleName: '',
    doi: null,
    description: null,
    keywords: [],
    license: null,
    url: null,
    datePublished: null,
    dateCreated: null,
    molecules: [],
    datasets: [],
    previewObjectUrls: new Map(),
});
// NMRium core / FileCollection / spectrum objects use ES private fields
// and must not be wrapped by Vue's reactive Proxy.
const prepared = shallowRef(null);
const nmriumWarnings = ref([]);
const nmriumError = ref(null);

watch(
    () => props.bag,
    async (bag) => {
        if (!bag) {
            return;
        }
        await openBag(bag);
    },
    { immediate: true },
);

async function openBag(bag) {
    validating.value = true;
    validation.value = null;
    prepared.value = null;
    nmriumError.value = null;
    nmriumWarnings.value = [];
    progress.value = { current: 0, total: 0, path: '' };

    revokePreviewUrls(metadata.value.previewObjectUrls);

    try {
        const [validationResult, meta] = await Promise.all([
            validateBag(bag, {
                onProgress: (p) => {
                    progress.value = p;
                },
            }),
            loadBagMetadata(bag),
        ]);

        validation.value = validationResult;
        metadata.value = meta;

        const prep = await prepareLocalNmriumFiles(bag, meta.nmriumParsed);
        nmriumWarnings.value = prep.warnings;
        prepared.value = markRaw(prep);
    } catch (error) {
        nmriumError.value = error?.message || 'Failed to open bag';
    } finally {
        validating.value = false;
    }
}

function onNmriumError(error) {
    nmriumError.value = error?.message || 'NMRium failed to load spectra';
}

function truncate(text, max) {
    if (!text || text.length <= max) {
        return text;
    }

    return text.slice(0, max) + '…';
}

function formatDate(value) {
    try {
        return new Date(value).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return value;
    }
}

function formatNum(value) {
    if (value == null || Number.isNaN(Number(value))) {
        return '—';
    }

    const n = Number(value);
    if (Math.abs(n) >= 100) {
        return n.toFixed(1);
    }

    return n.toFixed(3);
}

function datasetHasIntensity(dataset) {
    return (dataset.peaks || []).some(
        (peak) =>
            peak.intensity != null && !Number.isNaN(Number(peak.intensity)),
    );
}

function moleculeSvg(smiles) {
    if (!smiles) {
        return '';
    }

    try {
        const mol = Molecule.fromSmiles(smiles);

        return mol.toSVG(220, 180);
    } catch {
        return '';
    }
}

/**
 * Turn C8H10N4O2 into C₈H₁₀N₄O₂ for display.
 *
 * @param {string} formula
 * @returns {string}
 */
function formatFormula(formula) {
    const subscripts = {
        0: '₀',
        1: '₁',
        2: '₂',
        3: '₃',
        4: '₄',
        5: '₅',
        6: '₆',
        7: '₇',
        8: '₈',
        9: '₉',
    };

    return String(formula).replace(/\d/g, (digit) => subscripts[digit] || digit);
}
</script>
