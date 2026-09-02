<template>
    <!-- Page header with study title -->
    <Head :title="study.data.name" />

    <!-- Main layout wrapper -->
    <project-layout
        :project="project"
        :selected-tab="tab"
        :current-study="study"
    >
        <template #project-content>
            <!-- Main content container -->
            <div class="pb-10 mb-10 pt-4 pb-6">
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-6 lg:gap-y-6">
                    <div
                        :class="[
                            'min-w-0 space-y-6',
                            hasInfoSidebar ? 'lg:col-span-9' : 'lg:col-span-12',
                        ]"
                    >
                        <header class="space-y-3">
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <h1
                                        class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100 sm:text-2xl lg:text-3xl"
                                    >
                                        <span class="break-words">{{
                                            study.data.name
                                        }}</span>
                                    </h1>
                                </div>
                                <div
                                    v-if="study.data.is_public"
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
                                                        <div
                                                            class="min-w-0 flex-1"
                                                        >
                                                            <input
                                                                id="studyPublicURLCopy"
                                                                readonly
                                                                type="text"
                                                                :value="
                                                                    shareURL
                                                                "
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
                                                                    'studyPublicURLCopy'
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

                        <section
                            v-if="
                                study.data.description &&
                                study.data.description.length > 0
                            "
                        >
                            <h2
                                class="text-xl font-extrabold text-blue-gray-900 dark:text-gray-100"
                            >
                                Description
                            </h2>
                            <div
                                :class="[
                                    'mt-3 text-sm leading-relaxed text-blue-gray-500 prose prose-sm max-w-none dark:text-blue-gray-400 transition-all duration-300',
                                    isDescriptionExpanded
                                        ? 'max-h-none'
                                        : 'max-h-32 overflow-hidden',
                                ]"
                                v-html="md(study.data.description)"
                            ></div>
                            <button
                                v-if="isDescriptionLong"
                                type="button"
                                class="mt-2 text-xs font-medium text-teal-700 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300"
                                @click="toggleDescription"
                            >
                                {{
                                    isDescriptionExpanded
                                        ? "Show less"
                                        : "Show more"
                                }}
                            </button>
                        </section>

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
                            :project="project.data"
                            :study="study.data"
                        />

                        <HifsaPanel
                            :hifsa-data="study.data.hifsa_data"
                            :molecules="compositionMolecules"
                            id-prefix="public-study-hifsa"
                        />

                        <section class="overflow-visible">
                            <div
                                class="mb-5 flex flex-wrap items-center justify-between gap-3"
                            >
                                <h2
                                    class="text-xl font-extrabold text-blue-gray-900 dark:text-gray-100"
                                >
                                    Spectra Datasets
                                </h2>
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium tabular-nums text-gray-700 ring-1 ring-inset ring-gray-900/5 dark:bg-gray-800/80 dark:text-gray-300 dark:ring-white/10"
                                >
                                    {{ study.data.datasets.length }}
                                    {{
                                        study.data.datasets.length === 1
                                            ? "dataset"
                                            : "datasets"
                                    }}
                                </span>
                            </div>

                            <div
                                v-if="study.data.datasets.length === 0"
                                class="rounded-2xl border border-dashed border-gray-200 bg-gradient-to-b from-gray-50 to-white px-6 py-14 text-center dark:border-gray-700 dark:from-gray-900/40 dark:to-gray-950/20"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                <h3
                                    class="mt-3 text-sm font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    No datasets available
                                </h3>
                                <p
                                    class="mt-1.5 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    There are no spectra datasets associated
                                    with this study yet.
                                </p>
                            </div>

                            <div
                                v-else
                                class="grid grid-cols-1 gap-5 p-0.5 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3"
                            >
                                <div
                                    v-for="dataset in study.data.datasets.sort(
                                        (a, b) => (a.name > b.name ? 1 : -1)
                                    )"
                                    :key="dataset.slug"
                                    class="group relative"
                                >
                                    <a
                                        :href="datasetHref(dataset)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="relative flex h-full flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-inset ring-gray-900/[0.08] transition duration-300 ease-out hover:shadow-md hover:ring-gray-900/[0.12] focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:bg-gray-950 dark:ring-white/[0.1] dark:hover:ring-white/[0.16] dark:focus-visible:ring-offset-gray-950"
                                    >
                                        <div
                                            class="pointer-events-none absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-teal-500/0 via-teal-500/70 to-teal-500/0 opacity-0 transition duration-300 group-hover:opacity-100"
                                        ></div>

                                        <div class="flex flex-1 flex-col gap-3">
                                            <h3
                                                class="line-clamp-2 text-[0.9375rem] font-semibold leading-snug text-gray-900 transition-colors group-hover:text-teal-700 dark:text-gray-100 dark:group-hover:text-teal-400"
                                            >
                                                {{ dataset.name }}
                                            </h3>

                                            <div
                                                v-if="dataset.type"
                                                class="flex flex-wrap gap-2"
                                            >
                                                <span
                                                    class="inline-flex max-w-full items-center rounded-md bg-gray-50 px-2 py-1 text-[11px] font-medium leading-tight text-gray-700 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-900 dark:text-gray-300 dark:ring-white/10"
                                                >
                                                    {{
                                                        formatDatasetType(
                                                            dataset.type
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800"
                                        >
                                            <span
                                                class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                            >
                                                Open dataset
                                            </span>
                                            <svg
                                                class="h-4 w-4 shrink-0 text-gray-400 transition duration-300 group-hover:translate-x-0.5 group-hover:text-teal-600 dark:group-hover:text-teal-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                                                />
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside
                        v-if="hasInfoSidebar"
                        class="mt-10 min-w-0 lg:mt-0 lg:col-span-3"
                    >
                        <div
                            class="space-y-8 bg-white py-6 pl-0 pr-0 dark:bg-gray-900/80 lg:sticky lg:top-6"
                        >
                            <div v-if="studyIdentifier">
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Identifier
                                </h3>
                                <div class="mt-2">
                                    <Tag :identifier="studyIdentifier" />
                                </div>
                            </div>

                            <div v-if="showDoiCitation" class="w-full min-w-0">
                                <Citation
                                    :model="'sample'"
                                    :doi="study.data.doi"
                                />
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

                            <div v-if="study.data.release_date">
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Published
                                </h3>
                                <p
                                    class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{ formatDate(study.data.release_date) }}
                                </p>
                            </div>

                            <div v-if="study.data.created_at">
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    Created
                                </h3>
                                <p
                                    class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{ formatDate(study.data.created_at) }}
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
                                <MolecularInfoPanel
                                    :molecules="compositionMolecules"
                                />
                            </div>

                            <div v-if="hasStudyCitations">
                                <h3
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100"
                                >
                                    {{ citationsHeading }}
                                </h3>
                                <div class="mt-2 space-y-4">
                                    <citation-card
                                        :citations="study.data.citations"
                                    />
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </template>
    </project-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
/**
 * Study Detail Page Component
 *
 * Displays comprehensive information about a study including description,
 * tags, molecular composition, spectra viewer, and associated datasets.
 * Features expandable description, share functionality, and responsive layout.
 */

import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import HifsaPanel from "@/Shared/HifsaPanel.vue";
import MolecularInfoPanel from "@/Shared/MolecularInfoPanel.vue";
import Tag from "@/Shared/Tag.vue";
import { Head, router } from "@inertiajs/vue3";
import Citation from "@/Shared/Citation.vue";
import CitationCard from "@/Shared/CitationCard.vue";

export default {
    name: "StudyDetail",

    components: {
        ProjectLayout,
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraViewer,
        HifsaPanel,
        MolecularInfoPanel,
        Tag,
        Head,
        Citation,
        CitationCard,
    },

    props: {
        /** Project data object */
        project: {
            type: Object,
            required: true,
        },
        /** Active tab identifier */
        tab: {
            type: String,
            required: true,
        },
        /** Study data object */
        study: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            /** Currently selected dataset for sharing */
            selectedDataset: null,
            /** Selected spectra data (unused but kept for compatibility) */
            selectedSpectraData: null,
            /** JSON-LD schema data for structured markup */
            schema: {},
            /** Whether the description is currently expanded */
            isDescriptionExpanded: false,
            /** Whether the description is long enough to need expansion */
            isDescriptionLong: false,
            bagitStatusPolling: null,
        };
    },

    computed: {
        /**
         * Get the public URL for sharing the selected dataset
         * @returns {string} Public URL of the selected dataset
         */
        shareURL() {
            return this.study?.data?.public_url ?? "";
        },

        bagitJobStatus() {
            return (
                this.study?.data?.metadata_bagit_generation_status ||
                (this.study?.data?.bagit_archive_link ? "completed" : "pending")
            );
        },

        /**
         * Get the current page URL
         * @returns {string} Current page URL
         */
        url() {
            return String(this.$page.props.url);
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

        studyIdentifier() {
            const identifier = this.study?.data?.identifier;

            return identifier != null && String(identifier).length > 0
                ? identifier
                : null;
        },
        hasMolecularCompositionSidebar() {
            return this.compositionMolecules.length > 0;
        },
        hasKeywords() {
            return (this.study?.data?.tags?.length ?? 0) > 0;
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
        hasStudyCitations() {
            return (this.study?.data?.citations?.length ?? 0) > 0;
        },
        citationsHeading() {
            const count = this.study?.data?.citations?.length ?? 0;

            return count === 1 ? "Citation" : "Citations";
        },
        showDoiCitation() {
            return this.study?.data?.is_public && this.study?.data?.doi != null;
        },
        reviewerPreview() {
            return this.$page.props.reviewerPreview ?? null;
        },
        hasInfoSidebar() {
            return (
                Boolean(this.studyIdentifier) ||
                this.showDoiCitation ||
                this.hasLicense ||
                this.hasMolecularCompositionSidebar ||
                this.hasKeywords ||
                Boolean(this.study?.data?.release_date) ||
                Boolean(this.study?.data?.created_at) ||
                this.hasStudyCitations
            );
        },
    },

    watch: {
        bagitJobStatus() {
            this.startBagitStatusPolling();
        },
    },

    mounted() {
        // Parse URL parameters to set initial dataset selection
        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        let dsId = params["dsid"];

        // Find and set the dataset based on URL parameter
        this.study.data.datasets.forEach((ds) => {
            if (ds.slug == dsId) {
                this.selectedDataset = ds;
            }
        });

        // Default to first dataset if none specified or found
        if (!this.selectedDataset) {
            this.selectedDataset = this.study.data.datasets[0];
        }

        if (this.study.data.identifier) {
            axios
                .get(route("bioschemas.id", this.study.data.identifier))
                .then((response) => {
                    this.schema = response.data;
                });
        }

        this.startBagitStatusPolling();

        // Check if description needs expansion functionality
        this.$nextTick(() => {
            this.checkDescriptionLength();
        });
    },

    beforeUnmount() {
        this.stopBagitStatusPolling();
    },

    methods: {
        startBagitStatusPolling() {
            if (!["pending", "processing"].includes(this.bagitJobStatus)) {
                this.stopBagitStatusPolling();

                return;
            }

            if (this.bagitStatusPolling) {
                return;
            }

            this.bagitStatusPolling = window.setInterval(() => {
                router.reload({ only: ["study"] });
            }, 15000);
        },

        stopBagitStatusPolling() {
            if (this.bagitStatusPolling) {
                window.clearInterval(this.bagitStatusPolling);
                this.bagitStatusPolling = null;
            }
        },

        datasetHref(dataset) {
            if (
                this.reviewerPreview?.obfuscationcode &&
                dataset?.id &&
                this.study?.data?.id
            ) {
                return (
                    route("project.preview", [
                        this.reviewerPreview.obfuscationcode,
                    ]) +
                    "?tab=dataset&study=" +
                    encodeURIComponent(this.study.data.id) +
                    "&dataset=" +
                    encodeURIComponent(dataset.id)
                );
            }

            if (!dataset?.identifier) {
                return "#";
            }

            return "/" + String(dataset.identifier).replace(/^NMRXIV:/i, "");
        },
        formatDatasetType(type) {
            if (type == null || type === "") {
                return "";
            }

            return String(type).replace(/,\s*$/, "");
        },
        /**
         * Generate SVG string from molecule MOL data (legacy method)
         * @param {Object} molecule - Molecule object with MOL data
         * @returns {string|undefined} SVG string representation
         */
        getSVGString(molecule) {
            if (molecule.MOL) {
                let mol = OCL.Molecule.fromMolfile(
                    "\n  " + molecule.MOL.replaceAll('"', "")
                );
                return mol.toSVG(200, 200);
            }
        },

        /**
         * Generate molecule image URL from SMILES string
         * @param {string} smiles - SMILES representation of molecule
         * @returns {string} URL for molecule image or empty string if unavailable
         */
        getMoleculeImageUrl(smiles) {
            if (!smiles || !this.$page.props.CM_API) {
                return "";
            }
            const encodedSmiles = encodeURIComponent(smiles);
            return `${this.$page.props.CM_API}depict/2D?smiles=${encodedSmiles}&height=200&width=200&CIP=false&toolkit=cdk`;
        },

        /**
         * Handle image loading errors by hiding image and showing placeholder
         * @param {Event} event - Image error event
         */
        handleImageError(event) {
            // Hide the failed image
            event.target.style.display = "none";

            // Create and show placeholder text
            const placeholder = document.createElement("div");
            placeholder.className = "text-gray-500 text-sm text-center";
            placeholder.textContent = "Structure unavailable";
            event.target.parentNode.appendChild(placeholder);
        },

        /**
         * Toggle the expanded state of the description
         */
        toggleDescription() {
            this.isDescriptionExpanded = !this.isDescriptionExpanded;
        },

        /**
         * Check if description is long enough to require expansion functionality
         * Sets isDescriptionLong based on character count approximation
         */
        checkDescriptionLength() {
            if (this.study.data.description) {
                // Approximate 5 lines at ~80 characters per line
                const descriptionLength = this.study.data.description.length;
                this.isDescriptionLong = descriptionLength > 400;
            }
        },
    },
};
</script>
