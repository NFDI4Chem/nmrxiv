<template>
    <div class="space-y-6">
        <nav class="text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <li class="min-w-0">
                    <button
                        type="button"
                        class="font-medium text-gray-600 transition-colors hover:text-teal-600"
                        @click="$emit('back')"
                    >
                        {{ metadata.sampleName }}
                    </button>
                </li>
                <li aria-hidden="true" class="text-gray-300">/</li>
                <li
                    class="truncate font-medium text-gray-700"
                    aria-current="page"
                >
                    {{ dataset.name }}
                </li>
            </ol>
        </nav>

        <div
            class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]"
        >
            <div class="min-w-0 space-y-6">
                <header>
                    <h1
                        class="break-words text-xl font-semibold leading-tight text-gray-900 sm:text-2xl lg:text-3xl"
                    >
                        {{ dataset.name }}
                    </h1>
                    <p
                        v-if="dataset.techniqueLabel"
                        class="mt-1.5 text-sm text-gray-500"
                    >
                        {{ dataset.techniqueLabel }}
                    </p>
                </header>

                <p
                    v-if="metadata.description"
                    class="whitespace-pre-wrap rounded-lg border border-gray-100 bg-gray-50/80 p-4 text-sm text-gray-700"
                >
                    {{ metadata.description }}
                </p>

                <div>
                    <p v-if="error" class="mb-2 text-sm text-red-600">
                        {{ error }}
                    </p>
                    <p
                        v-for="(w, i) in warnings"
                        :key="'w' + i"
                        class="mb-1 text-sm text-amber-700"
                    >
                        {{ w }}
                    </p>
                    <NmriumPanel
                        v-if="prepared?.state && prepared?.aggregator"
                        :prepared="prepared"
                        @error="onError"
                    />
                    <div
                        v-else
                        class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center text-sm text-gray-500"
                    >
                        {{
                            loading
                                ? 'Loading spectrum from BagIt archive…'
                                : 'No spectrum available for this dataset.'
                        }}
                    </div>
                </div>

                <section v-if="rows.length" aria-labelledby="spectrum-info">
                    <div
                        class="flex flex-wrap items-center justify-between gap-2"
                    >
                        <h2
                            id="spectrum-info"
                            class="text-xl font-extrabold text-gray-900"
                        >
                            Spectrum info
                        </h2>
                        <button
                            v-if="rows.length > 8"
                            type="button"
                            class="text-xs font-medium text-teal-700 hover:text-teal-900"
                            @click="showAll = !showAll"
                        >
                            {{
                                showAll
                                    ? 'Show less'
                                    : `Show all (${rows.length})`
                            }}
                        </button>
                    </div>
                    <dl class="mt-4 divide-y divide-gray-100">
                        <div
                            v-for="row in visibleRows"
                            :key="row.key"
                            class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3 sm:gap-4"
                        >
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                {{ row.label }}
                            </dt>
                            <dd
                                class="break-words text-sm text-gray-800 sm:col-span-2"
                            >
                                {{ row.value }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>

            <aside class="space-y-5 lg:sticky lg:top-4 lg:self-start">
                <IdentifierPill :value="dataset.doi" />

                <CitationCard
                    heading="Cite this dataset"
                    :title="dataset.name"
                    :authors="metadata.authors"
                    :doi="dataset.doi"
                    :url="dataset.url"
                    :date="
                        dataset.datePublished ||
                        metadata.releaseDate ||
                        dataset.dateCreated
                    "
                    :publisher="metadata.publisher"
                />

                <LicenseInfo
                    :name="metadata.licenseName"
                    :url="metadata.license"
                />
                <div v-if="published">
                    <h3 class="text-sm font-bold text-gray-900">Published</h3>
                    <p class="mt-1 text-sm text-gray-700">
                        {{ formatDate(published) }}
                    </p>
                </div>
                <div v-if="dataset.dateCreated">
                    <h3 class="text-sm font-bold text-gray-900">Created</h3>
                    <p class="mt-1 text-sm text-gray-700">
                        {{ formatDate(dataset.dateCreated) }}
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

                <div v-if="metadata.molecules.length">
                    <h3 class="text-sm font-bold text-gray-900">
                        Molecular info
                    </h3>
                    <div
                        v-for="mol in metadata.molecules"
                        :key="mol.id || mol.inChIKey || mol.smiles"
                        class="mt-3 space-y-3 text-sm"
                    >
                        <div
                            v-if="moleculeSvg(mol.smiles, 260, 200)"
                            class="flex justify-center"
                            v-html="moleculeSvg(mol.smiles, 260, 200)"
                        ></div>
                        <div v-if="mol.smiles">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                SMILES
                            </p>
                            <p
                                class="break-all font-mono text-xs text-gray-800"
                            >
                                {{ mol.smiles }}
                            </p>
                        </div>
                        <div v-if="mol.molecularFormula">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Molecular formula
                            </p>
                            <p
                                class="text-gray-900"
                                v-html="formatFormula(mol.molecularFormula)"
                            ></p>
                        </div>
                        <div v-if="mol.molecularWeight">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                Molecular weight
                            </p>
                            <p class="text-gray-900">
                                {{ mol.molecularWeight }} g/mol
                            </p>
                        </div>
                        <div v-if="mol.inChI">
                            <p
                                class="text-xs font-medium uppercase tracking-wide text-gray-500"
                            >
                                InChI
                            </p>
                            <p
                                class="break-all font-mono text-xs text-gray-800"
                            >
                                {{ mol.inChI }}
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</template>

<script setup>
import { computed, markRaw, onMounted, ref, shallowRef } from 'vue';
import NmriumPanel from './NmriumPanel.vue';
import CitationCard from './CitationCard.vue';
import IdentifierPill from './IdentifierPill.vue';
import LicenseInfo from './LicenseInfo.vue';
import { prepareLocalNmriumFiles } from '../nmrium/loadLocal.js';
import { buildSpectrumInfoRows } from '../bag/spectrumInfo.js';
import { formatDate, formatFormula, moleculeSvg } from '../bag/chem.js';

const props = defineProps({
    bag: { type: Object, required: true },
    metadata: { type: Object, required: true },
    dataset: { type: Object, required: true },
});

defineEmits(['back']);

const prepared = shallowRef(null);
const warnings = ref([]);
const error = ref(null);
const loading = ref(true);
const showAll = ref(false);

const rows = computed(() =>
    buildSpectrumInfoRows(props.dataset.spectrum, props.dataset.variables),
);
const visibleRows = computed(() =>
    showAll.value ? rows.value : rows.value.slice(0, 8),
);
const published = computed(
    () => props.dataset.datePublished || props.metadata.datePublished,
);

onMounted(async () => {
    try {
        const prep = await prepareLocalNmriumFiles(
            props.bag,
            props.metadata.nmriumParsed,
            { folder: props.dataset.folder },
        );
        warnings.value = prep.warnings;
        prepared.value = markRaw(prep);
    } catch (e) {
        error.value = e?.message || 'Failed to open dataset';
    } finally {
        loading.value = false;
    }
});

function onError(e) {
    error.value = e?.message || 'NMRium failed to load the spectrum';
}
</script>
