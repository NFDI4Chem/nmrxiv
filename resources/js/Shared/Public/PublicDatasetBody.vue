<template>
    <div class="lg:grid lg:grid-cols-12 lg:gap-x-6 lg:gap-y-6">
        <div
            :class="[
                'min-w-0 space-y-6',
                hasInfoSidebar ? 'lg:col-span-9' : 'lg:col-span-12',
            ]"
        >
            <header class="space-y-3">
                <nav
                    class="text-sm text-gray-500 dark:text-gray-400"
                    aria-label="Breadcrumb"
                >
                    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <li class="min-w-0">
                            <Link
                                v-if="studyPublicHref"
                                :href="studyPublicHref"
                                class="font-medium text-gray-600 transition-colors hover:text-teal-600 dark:text-gray-300 dark:hover:text-teal-400"
                            >
                                {{ study.data.name }}
                            </Link>
                            <span
                                v-else
                                class="font-medium text-gray-600 dark:text-gray-300"
                            >
                                {{ study.data.name }}
                            </span>
                        </li>
                        <li
                            aria-hidden="true"
                            class="text-gray-300 dark:text-gray-600"
                        >
                            /
                        </li>
                        <li
                            class="truncate font-medium text-gray-700 dark:text-gray-200"
                            aria-current="page"
                        >
                            {{ dataset.data.name }}
                        </li>
                    </ol>
                </nav>

                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div class="min-w-0 flex-1">
                        <h1
                            class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100 sm:text-2xl lg:text-3xl"
                        >
                            <span class="break-words">{{
                                dataset.data.name
                            }}</span>
                        </h1>
                        <p
                            v-if="datasetTypeLabel"
                            class="mt-1.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            {{ datasetTypeLabel }}
                        </p>
                    </div>
                    <div
                        v-if="dataset && study.data.is_public"
                        class="flex shrink-0 flex-wrap items-center gap-3"
                    >
                        <Menu as="div" class="relative text-left">
                            <MenuButton
                                type="button"
                                class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-600 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                <ShareIcon
                                    class="mr-2 h-4 w-4 shrink-0 text-gray-700 dark:text-gray-300"
                                    aria-hidden="true"
                                />
                                Share
                            </MenuButton>
                            <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0"
                            >
                                <MenuItems
                                    class="absolute right-0 z-50 mt-2 w-72 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-gray-800 dark:ring-white/10"
                                >
                                    <MenuItem v-slot="{ active }">
                                        <div
                                            :class="[
                                                active
                                                    ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-gray-100'
                                                    : 'text-gray-700 dark:text-gray-200',
                                                'flex px-3 py-2 text-sm',
                                            ]"
                                        >
                                            <div class="min-w-0 flex-1">
                                                <input
                                                    id="datasetPublicURLCopy"
                                                    readonly
                                                    type="text"
                                                    :value="shareURL"
                                                    class="block w-full rounded-l-md border border-gray-300 bg-white text-sm focus:border-gray-500 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                                    @focus="
                                                        $event.target.select()
                                                    "
                                                />
                                            </div>
                                            <button
                                                type="button"
                                                class="-ml-px inline-flex shrink-0 items-center rounded-r-md border border-gray-300 bg-gray-50 px-2.5 py-2 text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                                @click="
                                                    copyToClipboard(
                                                        shareURL,
                                                        'datasetPublicURLCopy'
                                                    )
                                                "
                                            >
                                                <ClipboardDocumentIcon
                                                    class="h-5 w-5"
                                                    aria-hidden="true"
                                                />
                                            </button>
                                        </div>
                                    </MenuItem>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </header>

            <div
                v-if="
                    study.data.sample?.description &&
                    study.data.sample.description.length > 0
                "
                class="rounded-lg border border-gray-100 bg-gray-50/80 p-4 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-300"
            >
                <p class="whitespace-pre-wrap">
                    {{ study.data.sample.description }}
                </p>
            </div>

            <SpectraViewer
                ref="spectraViewerREF"
                :dataset="dataset.data"
                :project="project?.data ?? null"
                :study="study.data"
            />

            <HifsaPanel
                :hifsa-data="study.data.hifsa_data"
                :molecules="compositionMolecules"
                id-prefix="public-dataset-hifsa"
            />

            <section
                v-if="orderedSpectrumInfoRows.length > 0"
                aria-labelledby="spectrum-info-heading"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2
                        id="spectrum-info-heading"
                        class="text-xl font-extrabold text-blue-gray-900 dark:text-gray-100"
                    >
                        Spectrum info
                        <span
                            v-if="michiStandardsUrl"
                            class="ml-2 text-sm font-medium normal-case"
                        >
                            (
                            <a
                                :href="michiStandardsUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-teal-700 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300"
                            >
                                MIChI
                            </a>
                            )
                        </span>
                    </h2>
                    <button
                        v-if="hasMoreSpectrumInfo"
                        type="button"
                        class="text-xs font-medium text-teal-700 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300"
                        @click="showAllSpectrumInfo = !showAllSpectrumInfo"
                    >
                        {{
                            showAllSpectrumInfo
                                ? "Show less"
                                : `Show all (${orderedSpectrumInfoRows.length})`
                        }}
                    </button>
                </div>
                <dl class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                    <div
                        v-for="row in visibleSpectrumInfoRows"
                        :key="row.key"
                        class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3 sm:gap-4"
                    >
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400"
                        >
                            {{ row.label }}
                        </dt>
                        <dd
                            class="break-words text-sm text-gray-800 dark:text-gray-200 sm:col-span-2"
                        >
                            {{ row.value }}
                        </dd>
                    </div>
                </dl>
            </section>
        </div>

        <aside
            v-if="hasInfoSidebar"
            class="mt-10 min-w-0 lg:mt-0 lg:col-span-3"
        >
            <div
                class="space-y-8 bg-white py-6 pl-0 pr-0 dark:bg-gray-900/80 lg:sticky lg:top-6"
            >
                <div v-if="datasetIdentifier">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        Identifier
                    </h3>
                    <div class="mt-2">
                        <Tag :identifier="datasetIdentifier" />
                    </div>
                </div>

                <div v-if="showDoiCitation" class="w-full min-w-0">
                    <Citation model="dataset" :doi="dataset.data.doi" />
                </div>

                <div v-if="hasLicense">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        License
                    </h3>
                    <div class="mt-2 text-sm">
                        <a
                            v-if="licenseUrl"
                            :href="licenseUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-gray-900 underline decoration-gray-400 underline-offset-2 hover:text-teal-700 dark:text-gray-200 dark:hover:text-teal-400"
                        >
                            {{ licenseTitle }}
                        </a>
                        <span
                            v-else
                            class="font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ licenseTitle }}
                        </span>
                    </div>
                </div>

                <div v-if="publishedDate">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        Published
                    </h3>
                    <p
                        class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200"
                    >
                        {{ formatDate(publishedDate) }}
                    </p>
                </div>

                <div v-if="dataset.data.created_at">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        Created
                    </h3>
                    <p
                        class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200"
                    >
                        {{ formatDate(dataset.data.created_at) }}
                    </p>
                </div>

                <div v-if="hasKeywords">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        Tags
                    </h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a
                            v-for="tag in study.data.tags"
                            :key="tag.id"
                            :href="'/projects?tag=' + tag.name.en"
                            class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-900 shadow-sm transition-colors hover:border-gray-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-950 dark:text-gray-100 dark:hover:bg-gray-800"
                        >
                            {{ tag.name.en }}
                        </a>
                    </div>
                </div>

                <div v-if="hasMolecularCompositionSidebar">
                    <h3
                        class="text-sm font-bold text-gray-900 dark:text-gray-100"
                    >
                        Molecular info
                    </h3>
                    <MolecularInfoPanel :molecules="compositionMolecules" />
                </div>
            </div>
        </aside>
    </div>
</template>

<script>
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import HifsaPanel from "@/Shared/HifsaPanel.vue";
import Citation from "@/Shared/Citation.vue";
import MolecularInfoPanel from "@/Shared/MolecularInfoPanel.vue";
import Tag from "@/Shared/Tag.vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    extractMichiRows,
    MICHI_CONSUMED_INFO_KEYS,
} from "@/Utils/michiSpectrumInfo.js";

const SPECTRUM_INFO_PRIORITY = [
    "nucleus",
    "experiment",
    "type",
    "solvent",
    "name",
    "title",
    "date",
    "localeDate",
    "baseFrequency",
    "originFrequency",
    "numberOfPoints",
    "pulseSequence",
    "creator",
    "owner",
    "dimension",
    "dataClass",
];

const SPECTRUM_INFO_LABELS = {
    nucleus: "Nucleus",
    experiment: "Experiment",
    type: "Type",
    solvent: "Solvent",
    name: "Name",
    title: "Title",
    date: "Date",
    localeDate: "Recorded",
    baseFrequency: "Base frequency",
    originFrequency: "Origin frequency",
    numberOfPoints: "Points",
    pulseSequence: "Pulse sequence",
    creator: "Creator",
    owner: "Owner",
    dimension: "Dimension",
    dataClass: "Data class",
    isFt: "FT",
    isFid: "FID",
    acquisitionMode: "Acquisition mode",
    frequencyOffset: "Frequency offset",
};

export default {
    components: {
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraViewer,
        HifsaPanel,
        Citation,
        MolecularInfoPanel,
        Tag,
        Link,
    },
    props: {
        study: {
            type: Object,
            required: true,
        },
        dataset: {
            type: Object,
            required: true,
        },
        project: {
            type: Object,
            default: null,
        },
    },
    setup() {
        const page = usePage();

        return {
            page,
        };
    },
    data() {
        return {
            showAllSpectrumInfo: false,
        };
    },
    computed: {
        michiStandardsUrl() {
            return this.page?.props?.michiStandardsUrl ?? null;
        },
        shareURL() {
            return this.dataset.data.public_url;
        },
        studyPublicHref() {
            return this.study?.data?.public_url ?? null;
        },
        datasetTypeLabel() {
            const raw = this.dataset?.data?.type;
            if (!raw || typeof raw !== "string") {
                return "";
            }

            return raw.replace(/,\s*$/, "");
        },
        datasetIdentifier() {
            const identifier = this.dataset?.data?.identifier;

            return identifier != null && String(identifier).length > 0
                ? identifier
                : null;
        },
        licenseTitle() {
            const license =
                this.study?.data?.license ?? this.project?.data?.license;

            return license?.title ?? null;
        },
        licenseUrl() {
            const license =
                this.study?.data?.license ?? this.project?.data?.license;

            return license?.url ?? null;
        },
        hasLicense() {
            return Boolean(this.licenseTitle);
        },
        publishedDate() {
            return (
                this.study?.data?.release_date ??
                this.project?.data?.release_date ??
                null
            );
        },
        hasKeywords() {
            return (this.study?.data?.tags?.length ?? 0) > 0;
        },
        showDoiCitation() {
            return (
                this.dataset?.data?.is_public && this.dataset?.data?.doi != null
            );
        },
        firstSpectrum() {
            const spectra = this.dataset?.data?.nmrium_info?.data?.spectra;
            if (!Array.isArray(spectra) || spectra.length === 0) {
                return null;
            }

            return spectra[0] ?? null;
        },
        michiSpectrumInfoRows() {
            return extractMichiRows(this.firstSpectrum);
        },
        rawSpectrumInfoRows() {
            const info = this.firstSpectrum?.info;
            if (info == null || typeof info !== "object") {
                return [];
            }

            const consumedKeys = new Set(MICHI_CONSUMED_INFO_KEYS);

            return Object.keys(info)
                .filter((key) => !consumedKeys.has(key))
                .map((key) => ({
                    key,
                    label: SPECTRUM_INFO_LABELS[key] ?? key,
                    value: this.formatSpectrumInfoCell(info[key]),
                    source: "nmrium",
                }));
        },
        orderedSpectrumInfoRows() {
            const michiRows = this.michiSpectrumInfoRows;
            const rawRows = this.rawSpectrumInfoRows;
            const prioritized = SPECTRUM_INFO_PRIORITY.map((key) =>
                rawRows.find((row) => row.key === key)
            ).filter(Boolean);
            const prioritizedKeys = new Set(prioritized.map((row) => row.key));
            const remainingRawRows = rawRows.filter(
                (row) => !prioritizedKeys.has(row.key)
            );

            return [...michiRows, ...prioritized, ...remainingRawRows];
        },
        visibleSpectrumInfoRows() {
            if (this.showAllSpectrumInfo) {
                return this.orderedSpectrumInfoRows;
            }

            return this.orderedSpectrumInfoRows.slice(0, 8);
        },
        hasMoreSpectrumInfo() {
            return this.orderedSpectrumInfoRows.length > 8;
        },
        compositionMolecules() {
            const fromSample = this.study?.data?.sample?.molecules;
            if (Array.isArray(fromSample) && fromSample.length > 0) {
                return fromSample;
            }
            const top = this.study?.data?.molecules;
            if (Array.isArray(top) && top.length > 0) {
                return top;
            }

            return [];
        },
        hasMolecularCompositionSidebar() {
            return this.compositionMolecules.length > 0;
        },
        hasInfoSidebar() {
            return (
                Boolean(this.datasetIdentifier) ||
                this.showDoiCitation ||
                this.hasLicense ||
                Boolean(this.publishedDate) ||
                Boolean(this.dataset?.data?.created_at) ||
                this.hasKeywords ||
                this.hasMolecularCompositionSidebar
            );
        },
    },
    methods: {
        formatSpectrumInfoCell(val) {
            if (val === null || val === undefined) {
                return "—";
            }
            if (Array.isArray(val)) {
                return val
                    .map((v) =>
                        v !== null && typeof v === "object"
                            ? JSON.stringify(v)
                            : String(v)
                    )
                    .join(", ");
            }
            if (typeof val === "object") {
                return JSON.stringify(val);
            }

            return String(val);
        },
    },
};
</script>
