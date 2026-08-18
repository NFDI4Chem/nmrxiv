<template>
    <Head :title="study.data.name" />
    <sample-layout :study="study.data">
        <template #sample-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
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
                            <Menu
                                v-if="study.data.is_public"
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
                                                            :value="shareURL"
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
                    </div>
                    <div class="clear-both"></div>

                    <!-- Mobile layout section -->
                    <div class="mt-4">
                        <!-- Mobile controls (stacked vertically) -->
                        <div class="sm:hidden">
                            <Menu
                                v-if="study.data.is_public"
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
                                                            :value="shareURL"
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
                    </div>
                </div>

                <div class="mt-4 lg:grid lg:grid-cols-12 lg:gap-x-6 lg:gap-y-6">
                    <div
                        :class="[
                            'min-w-0 space-y-6 overflow-visible',
                            hasInfoSidebar ? 'lg:col-span-9' : 'lg:col-span-12',
                        ]"
                    >
                        <!-- Submitted Through Information -->
                        <div v-if="study.data.submitted_through">
                            <div
                                class="flex items-center space-x-3 text-sm text-gray-600"
                            >
                                <svg
                                    class="h-4 w-4 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
                                    />
                                </svg>
                                <span>Submitted via:</span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-800 uppercase"
                                >
                                    <img
                                        :src="`/img/eln/${study.data.submitted_through}.png`"
                                        class="h-12"
                                    />
                                </span>
                                <a
                                    :href="study.data.external_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center py-1 rounded text-md font-bold text-gray-800 uppercase hover:text-blue-600"
                                >
                                    <span
                                        class="inline-flex items-center py-1 rounded text-md font-bold uppercase"
                                    >
                                        {{ study.data.external_id }}
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-4 h-4 ml-3"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"
                                            />
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Author Section -->
                        <div
                            v-if="
                                study.data.authors &&
                                study.data.authors.length > 0
                            "
                            class="mt-6"
                        >
                            <div class="relative">
                                <div
                                    class="absolute inset-0 flex items-center"
                                    aria-hidden="true"
                                >
                                    <div
                                        class="w-full border-t border-gray-100"
                                    ></div>
                                </div>
                                <div
                                    class="relative flex items-center justify-between"
                                >
                                    <span
                                        class="pr-3 text-md bg-white font-medium text-gray-400"
                                    >
                                        Authors
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div
                                    class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-3"
                                >
                                    <author-card
                                        :authors="study.data.authors"
                                    />
                                </div>
                            </div>
                        </div>

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

                        <div class="min-w-0 max-w-full overflow-x-auto">
                            <SpectraViewer
                                ref="spectraViewerREF"
                                :study="study.data"
                            ></SpectraViewer>
                        </div>

                        <div class="overflow-visible">
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

                        <div
                            v-if="
                                study.data.description &&
                                study.data.description.length > 0
                            "
                            class="overflow-hidden"
                        >
                            <div class="relative">
                                <div
                                    class="absolute inset-0 flex items-center"
                                    aria-hidden="true"
                                >
                                    <div
                                        class="w-full border-t border-gray-100 dark:border-gray-700"
                                    ></div>
                                </div>
                                <div
                                    class="relative flex items-center justify-between"
                                >
                                    <span
                                        class="bg-white pr-3 text-md font-medium text-gray-400 dark:bg-gray-900"
                                    >
                                        Description
                                    </span>
                                </div>
                            </div>
                            <p
                                class="relative mt-1 max-h-64 overflow-scroll pb-10 text-sm text-blue-gray-500"
                                v-html="sanitizeHtml(study.data.description)"
                            ></p>
                            <div class="relative" aria-hidden="true">
                                <div
                                    class="absolute -inset-x-20 bottom-0 bg-gradient-to-t from-white pt-[7%] dark:from-gray-900"
                                ></div>
                            </div>
                        </div>
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
                                        v-if="study.data.license.url"
                                        :href="study.data.license.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-medium text-gray-900 underline decoration-gray-400 underline-offset-2 hover:text-teal-700 dark:text-gray-200 dark:hover:text-teal-400"
                                    >
                                        {{ study.data.license.title }}
                                    </a>
                                    <span
                                        v-else
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ study.data.license.title }}
                                    </span>
                                </div>
                                <p
                                    v-if="study.data.license.description"
                                    class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400"
                                    v-html="
                                        sanitizeHtml(
                                            study.data.license.description
                                        )
                                    "
                                ></p>
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

                            <div v-if="hasMixtureComposition">
                                <MixtureCompositionDisplay
                                    :composition="mixtureComposition"
                                />
                            </div>

                            <div v-else-if="hasMolecularCompositionSidebar">
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
    </sample-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import SampleLayout from "@/Pages/Public/Sample/Layout.vue";
import { ShareIcon, ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import SpectraViewer from "@/Shared/SpectraViewer.vue";
import DOIBadge from "@/Shared/DOIBadge.vue";
import MolecularInfoPanel from "@/Shared/MolecularInfoPanel.vue";
import MixtureCompositionDisplay from "@/Shared/MixtureCompositionDisplay.vue";
import Tag from "@/Shared/Tag.vue";
import { Head } from "@inertiajs/vue3";
import Citation from "@/Shared/Citation.vue";
import AuthorCard from "@/Shared/AuthorCard.vue";
import CitationCard from "@/Shared/CitationCard.vue";
export default {
    components: {
        SampleLayout,
        ShareIcon,
        ClipboardDocumentIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        SpectraViewer,
        DOIBadge,
        MolecularInfoPanel,
        MixtureCompositionDisplay,
        Tag,
        Head,
        Citation,
        AuthorCard,
        CitationCard,
    },
    props: ["project", "tab", "study"],
    data() {
        return {
            schema: {},
        };
    },
    computed: {
        shareURL() {
            return this.study.data.public_url;
        },
        url() {
            return String(this.$page.props.url);
        },
        /**
         * Molecules for composition UI: prefer nested sample.molecules, fall back
         * to study.data.molecules (StudyResource always exposes the latter).
         */
        mixtureComposition() {
            return (
                this.study?.data?.sample?.mixture_composition ??
                this.study?.data?.mixture_composition ??
                null
            );
        },
        hasMixtureComposition() {
            return (this.mixtureComposition?.components?.length ?? 0) > 0;
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
        hasLicense() {
            return Boolean(this.study?.data?.license);
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
        hasInfoSidebar() {
            return (
                Boolean(this.studyIdentifier) ||
                this.showDoiCitation ||
                this.hasMixtureComposition ||
                this.hasMolecularCompositionSidebar ||
                this.hasKeywords ||
                Boolean(this.study?.data?.release_date) ||
                Boolean(this.study?.data?.created_at) ||
                this.hasStudyCitations ||
                this.hasLicense
            );
        },
    },
    mounted() {
        if (this.study?.data?.identifier) {
            axios
                .get(route("bioschemas.id", this.study.data.identifier))
                .then((response) => {
                    this.schema = response.data;
                });
        }
    },
    methods: {
        datasetHref(dataset) {
            if (dataset?.public_url) {
                try {
                    return new URL(dataset.public_url).pathname;
                } catch {
                    return dataset.public_url;
                }
            }

            if (dataset?.identifier) {
                const id = String(dataset.identifier).replace(/^NMRXIV:/i, "");

                return `/dataset/${id}`;
            }

            return "#";
        },
    },
};
</script>
