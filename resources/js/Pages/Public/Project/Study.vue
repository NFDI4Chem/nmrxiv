<template>
    <!-- Page header with study title -->
    <Head :title="study.data.name" />
    
    <!-- Main layout wrapper -->
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <!-- Main content container -->
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <!-- DOI Citation section (only for public studies with DOI) -->
                <div
                    class="-mx-4"
                    v-if="study.data.is_public && study.data.doi != null"
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
                            @click="toggleDescription"
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium focus:outline-none"
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
                    v-if="
                        study.data.sample.molecules.length > 0 ||
                        study.data.sample.description == ''
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
                                Sample
                            </span>
                        </div>
                    </div>
                    <div
                        v-if="study.data.sample.molecules.length > 0"
                        class="mt-3"
                    >
                        <label>Molecular Composition</label>
                        <div class="grid md:grid-cols-2 gap-2 mt-2">
                            <div class="pr-2">
                                <div
                                    v-if="
                                        study.data.sample.molecules.length > 0
                                    "
                                    class="flow-root"
                                >
                                    <ul role="list" class="-mb-8">
                                        <li
                                            v-for="molecule in study.data.sample
                                                .molecules"
                                            :key="molecule.standard_inchi"
                                        >
                                            <div class="relative pb-8">
                                                <span
                                                    class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"
                                                ></span>
                                                <div
                                                    class="relative flex items-start space-x-3"
                                                >
                                                    <div
                                                        v-if="
                                                            molecule &&
                                                            molecule.pivot
                                                        "
                                                        class="relative"
                                                    >
                                                        <div
                                                            class="rounded-full border p-2 z-10 bg-gray-100 text-sm"
                                                        >
                                                            {{
                                                                molecule.pivot
                                                                    .percentage_composition
                                                            }}%
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div>
                                                            <div
                                                                class="text-sm"
                                                            >
                                                                <a
                                                                    class="font-medium text-gray-900"
                                                                    >{{
                                                                        molecule.standard_inchi
                                                                    }}</a
                                                                >
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="text-sm text-gray-700"
                                                        >
                                                            <div
                                                                class="rounded-md border flex justify-center items-center mx-auto p-4"
                                                                style="
                                                                    min-width: 220px;
                                                                    min-height: 220px;
                                                                "
                                                            >
                                                                <div
                                                                    v-if="
                                                                        molecule.canonical_smiles
                                                                    "
                                                                    class="w-[200px] h-[200px] flex items-center justify-center"
                                                                >
                                                                    <img
                                                                        class="max-w-full max-h-full object-contain"
                                                                        :src="
                                                                            getMoleculeImageUrl(
                                                                                molecule.canonical_smiles
                                                                            )
                                                                        "
                                                                        :alt="
                                                                            'Molecular structure of ' +
                                                                            molecule.standard_inchi
                                                                        "
                                                                        @error="
                                                                            handleImageError
                                                                        "
                                                                    />
                                                                </div>
                                                                <div
                                                                    v-else
                                                                    class="text-gray-500 text-sm"
                                                                >
                                                                    No structure
                                                                    available
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <div
                                        class="rounded-full border p-2 z-10 bg-gray-100 text-sm mt-14 text-center"
                                    >
                                        Sample chemical composition
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="text-center my-10 py-10">
                                        <h3
                                            class="mt-2 text-sm font-medium text-gray-900"
                                        >
                                            No structures associated with the
                                            sample yet!
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Get started by adding a new
                                            molecule.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
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
                                Spectra
                            </span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <SpectraViewer
                            ref="spectraViewerREF"
                            :project="project.data"
                            :study="study.data"
                        ></SpectraViewer>
                    </div>

                    <div class="my-6">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Spectra Datasets
                                </h2>
                                <span
                                    class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full"
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
                                class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                            >
                                <svg
                                    class="mx-auto h-12 w-12 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                <h3
                                    class="mt-2 text-sm font-medium text-gray-900"
                                >
                                    No datasets available
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    There are no spectra datasets associated
                                    with this study yet.
                                </p>
                            </div>

                            <div
                                v-else
                                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                            >
                                <div
                                    v-for="dataset in study.data.datasets.sort(
                                        (a, b) => (a.name > b.name ? 1 : -1)
                                    )"
                                    :key="dataset.slug"
                                    class="group relative bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 hover:border-gray-300"
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
                                        class="block p-4 h-full"
                                    >
                                        <div>
                                            <div
                                                class="flex items-start justify-between mb-3"
                                            >
                                                <h3
                                                    class="text-md font-bold text-teal-600 group-hover:text-blue-600 transition-colors duration-200 line-clamp-2 flex-1 pr-2"
                                                >
                                                    {{ dataset.name }}
                                                </h3>
                                            </div>

                                            <div
                                                class="flex flex-col gap-2 flex-1"
                                            >
                                                <div v-if="dataset.type">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                    >
                                                        {{
                                                            dataset.type.replace(
                                                                /,\s*$/,
                                                                ""
                                                            )
                                                        }}
                                                    </span>
                                                </div>

                                                <!-- <div class="overflow-hidden rounded-md border-0 text-gray-900">
                                                    <div v-if="dataset.doi" class="cursor-pointer max-w-full">
                                                        <span class="flex items-start rounded-l-md pr-2 py-1 text-xs font-medium flex-shrink-0 h-full">
                                                            DOI:
                                                        </span>
                                                        <span class="flex items-start pr-2 py-1 text-xs font-bold break-all leading-tight min-w-0">
                                                            {{ dataset.doi }}
                                                        </span>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>

                                        <div
                                            class="mt-3 pt-3 border-t border-gray-100"
                                        >
                                            <div
                                                class="flex items-center text-xs text-gray-500"
                                            >
                                                <span class="flex items-center">
                                                    <svg
                                                        class="w-3 h-3 mr-1"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        ></path>
                                                    </svg>
                                                    View Dataset
                                                </span>
                                                <span
                                                    class="ml-auto group-hover:text-blue-600 transition-colors duration-200"
                                                >
                                                    →
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>&emsp;</div>
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
import ShowProjectDates from "@/Shared/ShowProjectDates.vue";

export default {
    name: 'StudyDetail',
    
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
        ShowProjectDates,
    },
    
    props: {
        /** Project data object */
        project: {
            type: Object,
            required: true
        },
        /** Active tab identifier */
        tab: {
            type: String,
            required: true
        },
        /** Study data object */
        study: {
            type: Object,
            required: true
        }
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
