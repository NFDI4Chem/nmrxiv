<template>
    <div>
        <DatasetView
            v-if="activeDataset"
            :key="activeDataset.id"
            :bag="bag"
            :metadata="metadata"
            :dataset="activeDataset"
            @back="closeDataset"
        />
        <div v-else class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold break-words text-gray-900">
                    <span class="text-teal-700">{{ metadata.sampleName }}</span>
                </h1>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span
                        v-if="metadata.doi"
                        class="inline-flex items-stretch overflow-hidden rounded text-xs font-semibold"
                    >
                        <span class="bg-gray-900 px-2 py-1 text-white"
                            >DOI:</span
                        >
                        <span class="bg-teal-50 px-2 py-1 text-gray-900">
                            {{ metadata.doi }}
                        </span>
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
                Checksums did not all pass. Spectra are still shown below, but
                treat this archive as possibly corrupted or altered.
            </div>

            <p
                v-if="metadata.description"
                class="rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-wrap"
            >
                {{ truncate(metadata.description, 1200) }}
            </p>

            <div
                class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]"
            >
                <div class="min-w-0 space-y-8">
                    <div>
                        <h2 class="mb-4 text-2xl font-bold text-gray-900">
                            Spectra
                        </h2>
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
                        <h2 class="mb-4 text-2xl font-bold text-gray-900">
                            {{
                                metadata.molecules.length === 1
                                    ? 'Molecular info'
                                    : 'Molecular infos'
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
                                    class="mx-auto flex h-64 w-80 shrink-0 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 p-3 sm:mx-0"
                                    v-html="moleculeSvg(mol.smiles)"
                                ></div>
                                <div
                                    v-else
                                    class="mx-auto flex h-64 w-80 shrink-0 items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50 text-center text-xs text-gray-400 sm:mx-0"
                                >
                                    Structure unavailable
                                </div>
                                <dl
                                    class="min-w-0 flex-1 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2"
                                >
                                    <div v-if="mol.name" class="sm:col-span-2">
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            Name
                                        </dt>
                                        <dd
                                            class="mt-0.5 font-medium text-gray-900"
                                        >
                                            {{ mol.name }}
                                        </dd>
                                    </div>
                                    <div
                                        v-if="mol.smiles"
                                        class="sm:col-span-2"
                                    >
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            SMILES
                                        </dt>
                                        <dd
                                            class="mt-0.5 break-all font-mono text-xs text-gray-800"
                                        >
                                            {{ mol.smiles }}
                                        </dd>
                                    </div>
                                    <div v-if="mol.molecularFormula">
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            Molecular formula
                                        </dt>
                                        <dd
                                            class="mt-0.5 text-gray-900"
                                            v-html="
                                                formatFormula(
                                                    mol.molecularFormula,
                                                )
                                            "
                                        ></dd>
                                    </div>
                                    <div v-if="mol.molecularWeight">
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            Molecular weight
                                        </dt>
                                        <dd
                                            class="mt-0.5 tabular-nums text-gray-900"
                                        >
                                            {{ mol.molecularWeight }} g/mol
                                        </dd>
                                    </div>
                                    <div v-if="mol.inChI" class="sm:col-span-2">
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            InChI
                                        </dt>
                                        <dd
                                            class="mt-0.5 break-all font-mono text-xs text-gray-800"
                                        >
                                            {{ mol.inChI }}
                                        </dd>
                                    </div>
                                    <div
                                        v-if="mol.inChIKey"
                                        class="sm:col-span-2"
                                    >
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            InChIKey
                                        </dt>
                                        <dd
                                            class="mt-0.5 break-all font-mono text-xs text-gray-800"
                                        >
                                            {{ mol.inChIKey }}
                                        </dd>
                                    </div>
                                    <div v-if="mol.url" class="sm:col-span-2">
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
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
                                    <div
                                        v-if="mol.description"
                                        class="sm:col-span-2"
                                    >
                                        <dt
                                            class="text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
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
                        <div
                            class="mb-3 flex items-center justify-between gap-3"
                        >
                            <h2 class="mb-4 text-2xl font-bold text-gray-900">
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
                            class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"
                        >
                            <article
                                v-for="dataset in metadata.datasets"
                                :key="dataset.id"
                                class="flex flex-col rounded-2xl bg-white p-4 shadow-sm ring-1 ring-inset ring-gray-900/5"
                            >
                                <h3
                                    class="text-lg font-semibold text-gray-900 line-clamp-2"
                                >
                                    {{ dataset.title }}
                                </h3>
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    <span
                                        v-if="dataset.techniqueLabel"
                                        class="rounded-md bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-500/10"
                                    >
                                        {{ dataset.techniqueLabel }}
                                    </span>
                                </div>
                                <img
                                    v-if="
                                        dataset.spectrumId &&
                                        metadata.previewObjectUrls.get(
                                            dataset.spectrumId,
                                        )
                                    "
                                    :src="
                                        metadata.previewObjectUrls.get(
                                            dataset.spectrumId,
                                        )
                                    "
                                    :alt="dataset.title"
                                    class="mt-3 w-full rounded-lg border border-gray-100 bg-white object-contain"
                                />
                                <div
                                    class="mt-auto border-t border-gray-100 pt-4"
                                >
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between text-sm text-gray-500 hover:text-gray-900"
                                        @click="openDataset(dataset)"
                                    >
                                        Open dataset
                                        <span aria-hidden="true">→</span>
                                    </button>
                                </div>
                            </article>
                        </div>
                    </div>

                    <section v-if="metadata.authors.length">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900">
                            Authors
                        </h2>
                        <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                            <div
                                v-for="(author, i) in metadata.authors"
                                :key="'au' + i"
                                class="rounded-lg bg-white px-6 py-5 shadow-md ring-1 ring-inset ring-gray-200"
                            >
                                <div class="flex flex-wrap items-center gap-3">
                                    <span
                                        class="text-base font-bold text-gray-900"
                                    >
                                        {{ author.name }}
                                    </span>
                                    <span
                                        v-if="author.role"
                                        class="rounded-full bg-white px-3 py-0.5 text-sm text-gray-700 shadow ring-1 ring-inset ring-gray-100"
                                    >
                                        {{ author.role }}
                                    </span>
                                </div>
                                <p
                                    v-if="author.affiliation"
                                    class="mt-2 text-sm leading-relaxed text-gray-500"
                                >
                                    {{ author.affiliation }}
                                </p>
                                <p
                                    v-if="author.orcid"
                                    class="mt-2 text-sm text-gray-500"
                                >
                                    ORCID iD:
                                    <a
                                        :href="author.orcid"
                                        class="text-teal-700 underline"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ orcidId(author.orcid) }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </section>

                    <section v-if="metadata.citations.length">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900">
                            Citation
                        </h2>
                        <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                            <div
                                v-for="(citation, i) in metadata.citations"
                                :key="'ci' + i"
                                class="rounded-lg bg-white px-6 py-5 shadow-md ring-1 ring-inset ring-gray-200"
                            >
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <h3
                                        class="text-base font-bold leading-snug text-gray-900"
                                    >
                                        {{ citation.title }}
                                    </h3>
                                    <a
                                        v-if="citation.url"
                                        :href="citation.url"
                                        class="mt-0.5 shrink-0 text-gray-600 hover:text-gray-900"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        title="Open publication"
                                        aria-label="Open publication"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                                            />
                                        </svg>
                                    </a>
                                </div>
                                <p
                                    v-if="citation.authors"
                                    class="mt-2 text-sm text-gray-800"
                                >
                                    {{ citation.authors }}
                                </p>
                                <p
                                    v-if="citation.doi"
                                    class="mt-2 text-sm text-gray-500"
                                >
                                    DOI:
                                    <a
                                        :href="
                                            'https://doi.org/' + citation.doi
                                        "
                                        class="text-teal-700 hover:underline"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ citation.doi }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="space-y-5 lg:sticky lg:top-4 lg:self-start">
                    <IdentifierPill :value="metadata.doi" />

                    <CitationCard
                        heading="Cite this sample"
                        :title="metadata.sampleName"
                        :authors="metadata.authors"
                        :doi="metadata.doi"
                        :url="metadata.url"
                        :date="metadata.releaseDate"
                        :publisher="metadata.publisher"
                    />

                    <LicenseInfo
                        :name="metadata.licenseName"
                        :url="metadata.license"
                    />
                    <div v-if="metadata.datePublished">
                        <h3 class="text-sm font-bold text-gray-900">
                            Published
                        </h3>
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
                    <div v-if="metadata.keywords.length">
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
        </div>
    </div>
</template>

<script setup>
import { markRaw, ref, shallowRef, watch } from 'vue';
import ValidationPanel from './ValidationPanel.vue';
import NmriumPanel from './NmriumPanel.vue';
import CitationCard from './CitationCard.vue';
import IdentifierPill from './IdentifierPill.vue';
import LicenseInfo from './LicenseInfo.vue';
import DatasetView from './DatasetView.vue';
import { formatDate, formatFormula, moleculeSvg } from '../bag/chem.js';
import { validateBag } from '../bag/validate.js';
import { loadBagMetadata, revokePreviewUrls } from '../bag/metadata.js';
import { prepareLocalNmriumFiles } from '../nmrium/loadLocal.js';

const props = defineProps({
    bag: { type: Object, required: true },
});

const activeDataset = ref(null);
const validating = ref(false);
const progress = ref({ current: 0, total: 0, path: '' });
const validation = ref(null);
const metadata = ref({
    sampleName: '',
    doi: null,
    description: null,
    keywords: [],
    license: null,
    licenseName: null,
    url: null,
    datePublished: null,
    dateCreated: null,
    authors: [],
    citations: [],
    publisher: null,
    releaseDate: null,
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

function openDataset(dataset) {
    activeDataset.value = dataset;
    window.scrollTo({ top: 0 });
}

function closeDataset() {
    activeDataset.value = null;
    window.scrollTo({ top: 0 });
}

async function openBag(bag) {
    activeDataset.value = null;
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

function orcidId(url) {
    return String(url).replace(/^https?:\/\/orcid\.org\//i, '');
}

function truncate(text, max) {
    if (!text || text.length <= max) {
        return text;
    }

    return text.slice(0, max) + '…';
}
</script>
