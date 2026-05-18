<template>
    <!-- Page header with study title -->
    <Head :title="study.data.name" />

    <!-- Main layout wrapper -->
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <!-- Main content container -->
            <div
                class="pb-10 mb-10 py-6"
            >
                <!-- DOI Citation section (only for public studies with DOI) -->
                <div
                    v-if="study.data.is_public && study.data.doi != null"
                    class="-mx-4"
                >
                    <Citation
                        :model="'sample'"
                        :doi="study.data.doi"
                    ></Citation>
                </div>

                <!-- Study header section -->
                <div class="mt-2">
                    <!-- Study title -->
                    <h1 class="text-2xl font-bold break-words text-gray-900">
                        <div class="text-blue-500 break-all">
                            {{ study.data.name }}
                        </div>
                    </h1>

                    <!-- Header controls section -->
                    <div class="mt-3">
                        <!-- DOI Badge (left aligned) -->
                        <div class="float-left">
                            <DOIBadge
                                :doi="study.data.doi"
                                color="bg-yellow-300"
                            ></DOIBadge>
                        </div>

                        <!-- Desktop layout controls (right aligned) -->
                        <div class="hidden sm:block float-right">
                            <!-- Share button (desktop) -->
                            <div class="float-right">
                                <!-- Share dropdown menu -->
                                <Menu
                                    v-if="
                                        selectedDataset && study.data.is_public
                                    "
                                    as="div"
                                    class="relative text-left"
                                >
                                    <!-- Share button trigger -->
                                    <div>
                                        <MenuButton
                                            class="bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600 border border-gray-200 px-3 py-1"
                                        >
                                            <ShareIcon
                                                class="h-4 w-4 text-gray-800 flex-shrink-0 mr-2"
                                                aria-hidden="true"
                                            ></ShareIcon
                                            >Share
                                        </MenuButton>
                                    </div>
                                    <!-- Share dropdown transition -->
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <!-- Share dropdown menu items -->
                                        <MenuItems
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                        >
                                            <div class="py-1">
                                                <!-- Share URL input and copy button -->
                                                <MenuItem v-slot="{ active }">
                                                    <div
                                                        :class="[
                                                            active
                                                                ? 'bg-gray-100 text-gray-900'
                                                                : 'text-gray-700',
                                                            'block px-4 py-2 text-sm flex',
                                                        ]"
                                                    >
                                                        <!-- URL input field -->
                                                        <div class="flex-grow">
                                                            <input
                                                                id="datasetPublicURLCopyDesktop"
                                                                readonly
                                                                type="text"
                                                                :value="
                                                                    shareURL
                                                                "
                                                                class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                                @focus="
                                                                    $event.target.select()
                                                                "
                                                            />
                                                        </div>

                                                        <!-- Copy to clipboard button -->
                                                        <button
                                                            type="button"
                                                            class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                                                            @click="
                                                                copyToClipboard(
                                                                    shareURL,
                                                                    'datasetPublicURLCopyDesktop'
                                                                )
                                                            "
                                                        >
                                                            <span
                                                                ><ClipboardDocumentIcon
                                                                    class="h-5 w-5"
                                                                    aria-hidden="true"
                                                            /></span>
                                                        </button>
                                                    </div>
                                                </MenuItem>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>

                            <!-- Study identifier (desktop) -->
                            <div class="text-sm float-right">
                                <div
                                    class="hover:text-blue-600 hover:cursor-pointer text-gray-500 mx-2 my-1"
                                >
                                    <p class="inline m-0 p-0">
                                        #{{ study.data.identifier }}
                                    </p>
                                </div>
                            </div>

                            <!-- Clear floats -->
                            <div class="clear-both"></div>
                        </div>
                    </div>
                    <div class="clear-both"></div>

                    <!-- Mobile layout section -->
                    <div class="mt-4">
                        <!-- Mobile controls (stacked vertically) -->
                        <div class="flex flex-col gap-3 sm:hidden">
                            <!-- Share button (mobile) -->
                            <div>
                                <!-- Share dropdown menu (mobile) -->
                                <Menu
                                    v-if="
                                        selectedDataset && study.data.is_public
                                    "
                                    as="div"
                                    class="relative text-left"
                                >
                                    <!-- Share button trigger (mobile) -->
                                    <div>
                                        <MenuButton
                                            class="bg-white text-sm rounded-full flex items-center text-gray-400 hover:text-gray-600 border border-gray-200 px-3 py-1"
                                        >
                                            <ShareIcon
                                                class="h-4 w-4 text-gray-800 flex-shrink-0 mr-2"
                                                aria-hidden="true"
                                            ></ShareIcon
                                            >Share
                                        </MenuButton>
                                    </div>
                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <MenuItems
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                        >
                                            <div class="py-1">
                                                <MenuItem v-slot="{ active }">
                                                    <div
                                                        :class="[
                                                            active
                                                                ? 'bg-gray-100 text-gray-900'
                                                                : 'text-gray-700',
                                                            'block px-4 py-2 text-sm flex',
                                                        ]"
                                                    >
                                                        <div class="flex-grow">
                                                            <input
                                                                id="datasetPublicURLCopy"
                                                                readonly
                                                                type="text"
                                                                :value="
                                                                    shareURL
                                                                "
                                                                class="rounded-l-md focus:ring-gray-500 focus:border-gray-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                                                @focus="
                                                                    $event.target.select()
                                                                "
                                                            />
                                                        </div>
                                                        <button
                                                            type="button"
                                                            class="-ml-px relative inline-flex items-center space-x-2 px-2 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-500 focus:border-gray-500"
                                                            @click="
                                                                copyToClipboard(
                                                                    shareURL,
                                                                    'datasetPublicURLCopy'
                                                                )
                                                            "
                                                        >
                                                            <span
                                                                ><ClipboardDocumentIcon
                                                                    class="h-5 w-5"
                                                                    aria-hidden="true"
                                                            /></span>
                                                        </button>
                                                    </div>
                                                </MenuItem>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </div>
                            <!-- Study identifier (mobile) -->
                            <div class="text-sm">
                                <div
                                    class="inline hover:text-blue-600 hover:cursor-pointer text-gray-500"
                                >
                                    #{{ study.data.identifier }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="
                        study.data.description &&
                        study.data.description.length > 0
                    "
                    class="mt-4"
                >
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="pr-3 text-md bg-white font-medium text-gray-400"
                            >
                                Description
                            </span>
                        </div>
                    </div>
                    <div>
                        <div
                            :class="[
                                'mt-1 px-0 text-sm text-blue-gray-500 prose prose-sm max-w-none transition-all duration-300',
                                isDescriptionExpanded
                                    ? 'max-h-none'
                                    : 'max-h-32 overflow-hidden',
                            ]"
                            v-html="md(study.data.description)"
                        ></div>
                        <button
                            v-if="isDescriptionLong"
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium focus:outline-none"
                            @click="toggleDescription"
                        >
                            {{
                                isDescriptionExpanded
                                    ? "Show less"
                                    : "Show more"
                            }}
                        </button>
                    </div>
                </div>
                <div v-if="study.data.tags.length > 0" class="mt-4">
                    <div class="relative">
                        <div
                            class="absolute inset-0 flex items-center"
                            aria-hidden="true"
                        >
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span
                                class="pr-3 text-md bg-white font-medium text-gray-400"
                            >
                                Keywords
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <dd class="mt-1 text-md text-gray-900 space-y-5">
                            <p>
                                <span
                                    v-for="tag in study.data.tags"
                                    :key="tag.id"
                                    class="mr-2"
                                >
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-indigo-100 text-indigo-800"
                                    >
                                        <svg
                                            class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400"
                                            fill="currentColor"
                                            viewBox="0 0 8 8"
                                        >
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ tag.name["en"] }}
                                    </span>
                                </span>
                            </p>
                        </dd>
                    </div>
                </div>

                <div
                    class="mt-4 md:grid md:grid-cols-12 md:gap-x-4 md:gap-y-6 lg:gap-x-5 xl:gap-x-6"
                >
                        <div
                            :class="[
                            'min-w-0 space-y-6',
                            hasMolecularCompositionSidebar
                                ? 'md:col-span-9'
                                : 'md:col-span-12',
                            ]"
                        >
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

                        <div class="mt-6">
                            <SpectraViewer
                                ref="spectraViewerREF"
                                :project="project.data"
                                :study="study.data"
                            ></SpectraViewer>
                        </div>

                        <div class="my-6">
                            <div>
                                <div
                                    class="mb-5 flex flex-wrap items-center justify-between gap-3"
                                >
                                    <h2
                                        class="text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100"
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
                                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3"
                                >
                                    <div
                                        v-for="dataset in study.data.datasets.sort(
                                            (a, b) => (a.name > b.name ? 1 : -1)
                                        )"
                                        :key="dataset.slug"
                                        class="group relative"
                                    >
                                        <a
                                            :href="
                                                '/' +
                                                dataset.identifier.replace(
                                                    'NMRXIV:',
                                                    ''
                                                )
                                            "
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="relative flex h-full flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-900/[0.06] transition duration-300 ease-out hover:-translate-y-0.5 hover:shadow-lg hover:ring-gray-900/[0.1] focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:bg-gray-950 dark:ring-white/[0.08] dark:hover:ring-white/[0.14] dark:focus-visible:ring-offset-gray-950"
                                        >
                                            <div
                                                class="pointer-events-none absolute inset-x-0 top-0 h-1 rounded-t-2xl bg-gradient-to-r from-teal-500/0 via-teal-500/70 to-teal-500/0 opacity-0 transition duration-300 group-hover:opacity-100"
                                            ></div>

                                            <div
                                                class="flex flex-1 flex-col gap-3"
                                            >
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
                                                            dataset.type.replace(
                                                                /,\s*$/,
                                                                ""
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
                            </div>
                        </div>
                        </div>

                        <aside
                            v-if="hasMolecularCompositionSidebar"
                            class="mt-8 min-w-0 md:mt-0 md:col-span-3"
                        >
                            <div
                                class="space-y-6 bg-white p-6 dark:bg-gray-900/80 md:sticky md:top-6"
                            >
                                <ul role="list" class="space-y-8">
                                    <li
                                        v-for="molecule in compositionMolecules"
                                        :key="
                                            molecule.standard_inchi ||
                                            molecule.id
                                        "
                                        class="min-w-0"
                                    >
                                        <p
                                            class="text-sm font-medium break-all text-gray-900 dark:text-gray-100"
                                        >
                                            {{ molecule.standard_inchi }}
                                        </p>
                                        <div
                                            v-if="molecule.canonical_smiles"
                                            class="mt-3 flex justify-center"
                                        >
                                            <Depictor2D
                                                class="max-h-52 max-w-full"
                                                :molecule="
                                                    molecule.canonical_smiles
                                                "
                                            ></Depictor2D>
                                        </div>
                                        <p
                                            v-else
                                            class="mt-2 text-sm text-gray-500"
                                        >
                                            No structure available
                                        </p>
                                    </li>
                                </ul>
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
import Depictor2D from "@/Shared/Depictor2D.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import { Head } from "@inertiajs/vue3";
import Citation from "@/Shared/Citation.vue";

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
        Depictor2D,
        DOIBadge,
        Head,
        Citation,
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
        };
    },

    computed: {
        /**
         * Get the public URL for sharing the selected dataset
         * @returns {string} Public URL of the selected dataset
         */
        shareURL() {
            return this.selectedDataset.public_url;
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

        hasMolecularCompositionSidebar() {
            return this.compositionMolecules.length > 0;
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

        // Fetch bioschemas structured data for SEO
        axios
            .get(route("bioschemas.id", this.study.data.identifier))
            .then((response) => {
                this.schema = response.data;
            });

        // Check if description needs expansion functionality
        this.$nextTick(() => {
            this.checkDescriptionLength();
        });
    },

    methods: {
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
