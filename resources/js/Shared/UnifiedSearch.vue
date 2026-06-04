<template>
    <div>
        <div v-if="variant === 'hero'" class="w-full max-w-3xl mx-auto">
            <label
                v-if="heroSearchType.type === 'metadata'"
                for="hero-search"
                class="sr-only"
            >
                Search nmrXiv
            </label>
            <p v-else-if="heroSearchType.type === 'advanced'" class="sr-only">
                Advanced metadata search for nmrXiv
            </p>
            <nav
                class="mb-3 flex flex-wrap items-center gap-x-6 gap-y-2 border-b border-gray-200 px-2"
                aria-label="Search type"
            >
                <button
                    v-for="option in heroSearchTypes"
                    :key="option.type"
                    type="button"
                    class="-mb-px flex items-center gap-1.5 border-b-2 pb-2 text-sm font-medium transition-colors focus:outline-none"
                    :class="
                        heroSearchType.type === option.type
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'
                    "
                    :aria-current="
                        heroSearchType.type === option.type ? 'page' : undefined
                    "
                    @click="heroSearchType = option"
                >
                    <component
                        :is="option.icon"
                        class="h-4 w-4 shrink-0"
                        aria-hidden="true"
                    />
                    <span>{{ option.label }}</span>
                    <span
                        v-if="option.comingSoon"
                        class="rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-400"
                    >
                        Soon
                    </span>
                </button>
            </nav>
            <div
                v-if="heroSearchType.type === 'structure'"
                class="rounded-3xl border border-gray-200 bg-white p-4 shadow-lg ring-1 ring-gray-900/5"
            >
                <StructureEditorContent
                    v-model:search-type="structureSearchType"
                    editor-id="heroStructureSearchEditor"
                    :initial-smiles="heroStructureInitialSmiles"
                    compact
                    @ready="onHeroStructureEditorReady"
                />
                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        class="inline-flex shrink-0 items-center gap-2 rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                        @click="performStructureSearch"
                    >
                        <MagnifyingGlassIcon
                            class="h-4 w-4 shrink-0"
                            aria-hidden="true"
                        />
                        Search
                    </button>
                </div>
            </div>
            <div
                v-else-if="heroSearchType.type === 'spectra'"
                class="rounded-3xl border border-gray-200 bg-white p-4 shadow-lg ring-1 ring-gray-900/5"
            >
                <SpectraUploadContent
                    compact
                    @files-uploaded="handleSpectraFiles"
                />
                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        :disabled="spectraFiles.length === 0"
                        class="inline-flex shrink-0 items-center gap-2 rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition-colors"
                        :class="
                            spectraFiles.length === 0
                                ? 'opacity-50 cursor-not-allowed'
                                : 'hover:bg-gray-800'
                        "
                        @click="performSpectraSearch"
                    >
                        <MagnifyingGlassIcon
                            class="h-4 w-4 shrink-0"
                            aria-hidden="true"
                        />
                        Search{{
                            spectraFiles.length > 0
                                ? ` (${spectraFiles.length} file${
                                      spectraFiles.length > 1 ? "s" : ""
                                  })`
                                : ""
                        }}
                    </button>
                </div>
            </div>
            <div
                v-else-if="heroSearchType.type === 'advanced'"
                class="rounded-3xl border border-gray-200 bg-white p-4 shadow-lg ring-1 ring-gray-900/5"
            >
                <MetadataSearchContent
                    compact
                    @search-params-updated="handleMetadataParams"
                />
                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        class="inline-flex shrink-0 items-center gap-2 rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                        @click="performMetadataSearch"
                    >
                        <MagnifyingGlassIcon
                            class="h-4 w-4 shrink-0"
                            aria-hidden="true"
                        />
                        Search
                    </button>
                </div>
            </div>
            <div
                v-else-if="heroSearchType.type === 'metadata'"
                class="flex items-center rounded-full border border-gray-200 bg-white pl-1 pr-2 py-1 shadow-lg ring-1 ring-gray-900/5 focus-within:ring-2 focus-within:ring-gray-900 transition-shadow"
            >
                <MagnifyingGlassIcon
                    class="ml-3 h-5 w-5 shrink-0 text-gray-400"
                    aria-hidden="true"
                />
                <input
                    id="hero-search"
                    ref="heroSearchInput"
                    type="search"
                    class="min-w-0 flex-1 border-0 bg-transparent py-2 pr-2 text-base text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-0"
                    :placeholder="heroPlaceholder"
                    @input="onHeroSearchInput"
                    @keydown.enter.prevent="openHeroSearch"
                />
                <button
                    type="button"
                    class="shrink-0 rounded-full bg-gray-900 px-4 sm:px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 transition-colors"
                    @click="openHeroSearch"
                >
                    Search
                </button>
            </div>
            <div
                class="mt-3 flex flex-wrap items-center justify-between gap-x-4 gap-y-2"
            >
                <p
                    v-if="heroSearchType.type === 'structure'"
                    class="flex flex-wrap items-center gap-x-1 gap-y-1 text-sm text-gray-500"
                >
                    <span>Try:</span>
                    <button
                        type="button"
                        class="font-medium text-gray-700 underline-offset-2 hover:text-gray-900 hover:underline"
                        @click="
                            tryHeroStructureExample(
                                'Caffeine',
                                caffeineExampleSmiles
                            )
                        "
                    >
                        Caffeine
                    </button>
                    <span>,</span>
                    <button
                        type="button"
                        class="max-w-full break-all font-medium text-gray-700 underline-offset-2 hover:text-gray-900 hover:underline sm:break-normal"
                        @click="
                            tryHeroStructureExample(
                                caffeineExampleSmiles,
                                caffeineExampleSmiles
                            )
                        "
                    >
                        {{ caffeineExampleSmiles }}
                    </button>
                </p>

                <a
                    href="https://github.com/NFDI4Chem/nmrxiv/issues"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="ml-auto shrink-0 text-sm text-gray-500 hover:text-gray-900"
                >
                    Report bugs:
                    <span
                        class="font-medium text-gray-700 underline-offset-2 hover:underline"
                        >Issue Tracker</span
                    >
                </a>
            </div>
        </div>
        <button
            v-else
            ref="searchButton"
            type="button"
            class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200"
            @click="openModal"
        >
            <MagnifyingGlassIcon class="h-5 w-5 mr-2" aria-hidden="true" />
            Search
        </button>

        <TransitionRoot :show="open" as="template" appear>
            <HDialog as="div" class="relative z-50" @close="closeModal">
                <TransitionChild
                    as="template"
                    enter="ease-out duration-200"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="ease-in duration-150"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div
                        class="fixed inset-0 bg-gray-500/40 backdrop-blur-xl transition-opacity"
                    />
                </TransitionChild>

                <div
                    class="fixed inset-0 z-10 flex items-center justify-center p-4"
                >
                    <!-- Selection View -->
                    <TransitionChild
                        v-if="
                            !showStructureEditor &&
                            !showSpectraUpload &&
                            !showPeakListSearch &&
                            !showMetadataSearch
                        "
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="mx-auto w-[90vw] max-w-6xl max-h-[90vh] transform transition-all bg-white rounded-3xl p-8 shadow-2xl overflow-y-auto"
                        >
                            <!-- Header -->
                            <div class="text-center mb-8">
                                <h2
                                    class="text-2xl sm:text-3xl font-semibold text-gray-900"
                                >
                                    How would you like to search?
                                </h2>
                                <p class="mt-2 text-gray-600">
                                    Choose a method to find compounds and
                                    datasets
                                </p>
                            </div>

                            <!-- Cards Grid -->
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                            >
                                <div
                                    v-for="option in searchOptions"
                                    :key="option.type"
                                    class="group relative bg-white rounded-3xl p-6 text-left transition-all duration-200 min-h-[320px] flex flex-col overflow-hidden"
                                    :class="[
                                        option.comingSoon
                                            ? 'border border-gray-200 cursor-not-allowed opacity-60'
                                            : 'cursor-pointer hover:shadow-lg active:scale-[0.98] border border-gray-200 hover:border-gray-300',
                                        selectedType === option.type &&
                                        !option.comingSoon
                                            ? 'border-2 border-blue-500 shadow-lg'
                                            : '',
                                    ]"
                                    @click="
                                        !option.comingSoon &&
                                            selectAndProceed(option.type)
                                    "
                                >
                                    <!-- Coming Soon Badge -->
                                    <div
                                        v-if="option.comingSoon"
                                        class="absolute bottom-4 right-4 z-20 bg-gray-900 text-white text-xs font-semibold px-3 py-1 rounded-full"
                                    >
                                        Coming Soon
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 relative z-10">
                                        <p
                                            class="text-xs font-medium text-gray-500 uppercase tracking-wide"
                                        >
                                            {{ option.category }}
                                        </p>
                                        <h3
                                            class="mt-2 text-xl font-semibold text-gray-900 leading-tight"
                                        >
                                            {{ option.title }}
                                        </h3>
                                        <p class="mt-2 text-sm text-gray-600">
                                            {{ option.description }}
                                        </p>
                                    </div>

                                    <!-- Image Area -->
                                    <div
                                        class="absolute -bottom-20 -right-10 w-64 h-64 pointer-events-none"
                                    >
                                        <img
                                            v-if="option.image"
                                            :src="option.image"
                                            :alt="option.title"
                                            class="w-full h-full object-cover object-bottom-right grayscale opacity-20 rotate-10"
                                        />
                                    </div>

                                    <!-- Plus Button (only show for active cards) -->
                                    <div
                                        v-if="!option.comingSoon"
                                        class="absolute bottom-4 right-4 w-8 h-8 bg-gray-900 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                                    >
                                        <PlusIcon
                                            class="h-4 w-4 text-white"
                                            aria-hidden="true"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-8 text-center">
                                <!-- <a
                                    href="#"
                                    autofocus
                                    class="text-sm text-gray-500 hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 rounded px-2 py-1"
                                    @click.prevent="closeModal"
                                >
                                    Cancel
                                </a> -->
                                <!-- <span class="mx-3 text-gray-300">|</span> -->
                                <span class="text-sm text-gray-500">
                                    Press
                                    <kbd
                                        class="px-1.5 py-0.5 text-xs font-medium bg-gray-200 text-gray-600 rounded"
                                        >Esc</kbd
                                    >
                                    to close
                                </span>
                            </div>
                        </DialogPanel>
                    </TransitionChild>

                    <!-- Structure Editor View -->
                    <TransitionChild
                        v-if="showStructureEditor"
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="mx-auto flex h-[90vh] w-[90vw] max-w-6xl transform flex-col overflow-hidden rounded-3xl bg-white shadow-2xl transition-all"
                        >
                            <!-- Main Content -->
                            <div class="min-h-0 flex-1 overflow-hidden">
                                <div
                                    class="mx-auto flex h-full min-h-0 max-w-6xl flex-col px-4 py-4"
                                >
                                    <StructureEditorContent
                                        v-model:search-type="
                                            structureSearchType
                                        "
                                        editor-id="unifiedStructureSearchEditor"
                                        :initial-smiles="
                                            modalStructureInitialSmiles
                                        "
                                        @ready="onModalStructureEditorReady"
                                    />
                                </div>
                            </div>

                            <!-- Footer with Actions -->
                            <div
                                class="flex shrink-0 items-center justify-between border-t border-gray-100 bg-gray-50 px-4 py-4"
                            >
                                <a
                                    href="#"
                                    autofocus
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                    @click.prevent="backToOptions"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                    @click.prevent="performStructureSearch"
                                >
                                    Search
                                </a>
                            </div>
                        </DialogPanel>
                    </TransitionChild>

                    <!-- Spectra Upload View -->
                    <TransitionChild
                        v-if="showSpectraUpload"
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="mx-auto w-[90vw] max-w-6xl max-h-[90vh] transform transition-all bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
                        >
                            <!-- Main Content -->
                            <div class="flex-1 overflow-y-auto">
                                <div class="px-4 py-6 max-w-6xl mx-auto">
                                    <SpectraUploadContent
                                        @files-uploaded="handleSpectraFiles"
                                    />
                                </div>
                            </div>

                            <!-- Footer with Actions -->
                            <div
                                class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between"
                            >
                                <a
                                    href="#"
                                    autofocus
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                    @click.prevent="backToOptions"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    :class="
                                        spectraFiles.length === 0
                                            ? 'opacity-50 cursor-not-allowed'
                                            : 'hover:bg-gray-800'
                                    "
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                    @click.prevent="performSpectraSearch"
                                >
                                    Search
                                    {{
                                        spectraFiles.length > 0
                                            ? `(${spectraFiles.length} file${
                                                  spectraFiles.length > 1
                                                      ? "s"
                                                      : ""
                                              })`
                                            : ""
                                    }}
                                </a>
                            </div>
                        </DialogPanel>
                    </TransitionChild>

                    <!-- Peak List Search View -->
                    <TransitionChild
                        v-if="showPeakListSearch"
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="mx-auto w-[90vw] max-w-6xl max-h-[90vh] transform transition-all bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
                        >
                            <!-- Main Content -->
                            <div class="flex-1 overflow-y-auto">
                                <div class="px-4 py-6 max-w-6xl mx-auto">
                                    <PeakListSearchContent
                                        @search-params-updated="
                                            handlePeakListParams
                                        "
                                    />
                                </div>
                            </div>

                            <!-- Footer with Actions -->
                            <div
                                class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between"
                            >
                                <a
                                    href="#"
                                    autofocus
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                    @click.prevent="backToOptions"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                    @click.prevent="performPeakListSearch"
                                >
                                    Search
                                </a>
                            </div>
                        </DialogPanel>
                    </TransitionChild>

                    <!-- Metadata Search View -->
                    <TransitionChild
                        v-if="showMetadataSearch"
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="mx-auto w-[90vw] max-w-6xl max-h-[90vh] transform transition-all bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
                        >
                            <!-- Main Content -->
                            <div class="flex-1 overflow-y-auto">
                                <div class="px-4 py-6 max-w-6xl mx-auto">
                                    <MetadataSearchContent
                                        @search-params-updated="
                                            handleMetadataParams
                                        "
                                    />
                                </div>
                            </div>

                            <!-- Footer with Actions -->
                            <div
                                class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between"
                            >
                                <a
                                    href="#"
                                    autofocus
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                    @click.prevent="backToOptions"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                    @click.prevent="performMetadataSearch"
                                >
                                    Search
                                </a>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </HDialog>
        </TransitionRoot>
    </div>
</template>

<script>
import { ref, computed, watch, watchEffect, onMounted, shallowRef } from "vue";
import { router } from "@inertiajs/vue3";
import { SEARCH_SCOPE, buildSearchPagePath } from "@/Utils/unifiedSearchApi.js";
import { useMagicKeys } from "@vueuse/core";
import {
    Dialog as HDialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import {
    MagnifyingGlassIcon,
    PlusIcon,
    PencilIcon,
    DocumentTextIcon,
    BeakerIcon,
    ChartBarIcon,
    AdjustmentsHorizontalIcon,
} from "@heroicons/vue/24/outline";
import { loadOpenChemLib } from "@/Utils/structureEditor";
import StructureEditorContent from "@/Shared/StructureEditorContent.vue";
import SpectraUploadContent from "@/Shared/SpectraUploadContent.vue";
import PeakListSearchContent from "@/Shared/PeakListSearchContent.vue";
import MetadataSearchContent from "@/Shared/MetadataSearchContent.vue";

export default {
    components: {
        HDialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        MagnifyingGlassIcon,
        PlusIcon,
        PencilIcon,
        AdjustmentsHorizontalIcon,
        StructureEditorContent,
        SpectraUploadContent,
        PeakListSearchContent,
        MetadataSearchContent,
    },
    props: {
        variant: {
            type: String,
            default: "button",
            validator: (value) => ["button", "hero"].includes(value),
        },
    },
    emits: ["search-type-selected"],
    setup(props, { emit }) {
        const { meta, k } = useMagicKeys();
        const open = ref(false);
        const selectedType = ref(null);
        const showStructureEditor = ref(false);
        const showSpectraUpload = ref(false);
        const showPeakListSearch = ref(false);
        const showMetadataSearch = ref(false);
        const heroStructureEditor = shallowRef(null);
        const modalStructureEditor = shallowRef(null);
        const structureSearchType = ref("exact");
        const spectraFiles = ref([]);
        const peakListParams = ref(null);
        const metadataParams = ref(null);
        const searchButton = ref(null);
        const heroSearchInput = ref(null);
        const caffeineExampleSmiles = "CN1C(=O)C2=C(N=CN2C)N(C)C1=O";
        const pendingHeroStructureSmiles = ref(null);
        const heroSearchQuery = ref("");
        const heroStructureDraft = ref(null);

        const heroSearchTypes = [
            {
                type: "metadata",
                label: "Text search",
                icon: DocumentTextIcon,
                comingSoon: false,
            },
            {
                type: "structure",
                label: "Structure",
                icon: BeakerIcon,
                comingSoon: false,
            },
            {
                type: "spectra",
                label: "Spectra",
                icon: ChartBarIcon,
                comingSoon: false,
            },
            {
                type: "advanced",
                label: "Advanced",
                icon: AdjustmentsHorizontalIcon,
                comingSoon: false,
            },
        ];

        const resolveHeroSearchTypeFromUrl = () => {
            if (typeof window === "undefined") {
                return heroSearchTypes[0];
            }

            const tabParam = new URL(window.location.href).searchParams.get(
                "tab"
            );

            return (
                heroSearchTypes.find((option) => option.type === tabParam) ??
                heroSearchTypes[0]
            );
        };

        const heroSearchType = ref(
            props.variant === "hero"
                ? resolveHeroSearchTypeFromUrl()
                : heroSearchTypes[0]
        );

        const heroStructureInitialSmiles = computed(
            () =>
                pendingHeroStructureSmiles.value ??
                heroStructureDraft.value ??
                null
        );

        const modalStructureInitialSmiles = computed(
            () =>
                pendingHeroStructureSmiles.value ??
                heroStructureDraft.value ??
                null
        );

        watch(
            () => heroSearchType.value.type,
            (newType, oldType) => {
                spectraFiles.value = [];

                if (oldType === "advanced") {
                    metadataParams.value = null;
                }

                if (props.variant !== "hero") {
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set("tab", newType);
                window.history.replaceState({}, "", url.toString());

                if (oldType === "structure") {
                    persistHeroStructureDraft();
                    heroStructureEditor.value = null;
                }
            },
            { flush: "post" }
        );

        const heroPlaceholder = computed(() => {
            switch (heroSearchType.value.type) {
                case "spectra":
                    return "Upload NMR spectra to find similar data...";
                case "structure":
                    return "Compound name, SMILES, InChI, InChIKey...";
                case "metadata":
                    return "Search across all metadata fields...";
                case "advanced":
                    return "Search by free text or specific NMR metadata fields";
                default:
                    return "Search across all metadata fields...";
            }
        });

        const hasHeroStructure = computed(() => {
            if (heroSearchQuery.value.trim() !== "") {
                return true;
            }

            if (heroStructureDraft.value) {
                return true;
            }

            if (heroStructureEditor.value) {
                try {
                    const smiles = heroStructureEditor.value
                        .getSmiles()
                        ?.trim();

                    return Boolean(smiles);
                } catch {
                    return false;
                }
            }

            return false;
        });

        const heroStructureActionLabel = computed(() =>
            hasHeroStructure.value ? "Edit structure" : "Draw structure"
        );

        const onHeroSearchInput = (event) => {
            heroSearchQuery.value = event.target.value;
        };

        const persistHeroStructureDraft = () => {
            if (!heroStructureEditor.value) {
                return;
            }

            try {
                const smiles = heroStructureEditor.value.getSmiles()?.trim();

                if (smiles) {
                    heroStructureDraft.value = smiles;
                }
            } catch {
                // Editor may not be ready yet
            }
        };

        const onHeroStructureEditorReady = async (editor) => {
            heroStructureEditor.value = editor;

            const url = new URL(window.location.href);
            const querySmiles = url.searchParams.get("query");

            if (
                querySmiles &&
                !pendingHeroStructureSmiles.value &&
                !heroStructureDraft.value
            ) {
                const OCL = await loadOpenChemLib();
                const decoded = decodeURIComponent(querySmiles);
                editor.setMolFile(OCL.Molecule.fromSmiles(decoded).toMolfile());
                heroStructureDraft.value = decoded;
            }

            persistHeroStructureDraft();
        };

        const onModalStructureEditorReady = (editor) => {
            modalStructureEditor.value = editor;
        };

        const searchOptions = [
            {
                type: "structure",
                category: "Structure Search",
                title: "Chemical Structure",
                description:
                    "Draw or paste a structure to search by exact match, substructure, or similarity.",
                image: "/img/molecule-formats.png",
                bgClass: "bg-teal-100",
                iconClass: "text-teal-600",
                comingSoon: false,
            },
            {
                type: "spectra",
                category: "Spectra Search",
                title: "NMR Spectra",
                description:
                    "Upload raw NMR data from Bruker or JEOL to find similar spectra.",
                image: "/img/instrument-format.png",
                bgClass: "bg-indigo-100",
                iconClass: "text-indigo-600",
                comingSoon: false,
            },
            {
                type: "peaks",
                category: "Peak Search",
                title: "Peak List",
                description:
                    "Enter peak positions to search for matching spectra in the database.",
                image: "/img/spectra-format.png",
                bgClass: "bg-amber-100",
                iconClass: "text-amber-600",
                comingSoon: true,
            },
            {
                type: "metadata",
                category: "Text Search",
                title: "Metadata",
                description:
                    "Search by compound name, author, project, or other metadata.",
                image: "/img/metadata-format.png",
                bgClass: "bg-rose-100",
                iconClass: "text-rose-600",
                comingSoon: false,
            },
        ];

        // Update URL when modal state changes
        const updateUrl = (isOpen, type = null) => {
            const url = new URL(window.location.href);
            if (isOpen) {
                url.searchParams.set("search", type || "open");
            } else {
                url.searchParams.delete("search");
            }
            window.history.replaceState({}, "", url.toString());
        };

        // Keyboard shortcut: Cmd+K / Ctrl+K
        watchEffect(() => {
            if (meta.value && k.value) {
                open.value = true;
                updateUrl(true);
            }
        });

        const openModal = () => {
            open.value = true;
            showStructureEditor.value = false;
            updateUrl(true);
            if (props.variant === "hero" && heroSearchInput.value) {
                heroSearchInput.value.blur();
            } else if (searchButton.value) {
                searchButton.value.blur();
            }
        };

        const closeModal = () => {
            if (showStructureEditor.value && modalStructureEditor.value) {
                try {
                    const smiles = modalStructureEditor.value
                        .getSmiles()
                        ?.trim();

                    if (smiles) {
                        heroStructureDraft.value = smiles;
                    }
                } catch {
                    // Modal editor may not be ready yet
                }
            } else {
                persistHeroStructureDraft();
            }

            open.value = false;
            showStructureEditor.value = false;
            showSpectraUpload.value = false;
            showPeakListSearch.value = false;
            showMetadataSearch.value = false;
            spectraFiles.value = [];
            peakListParams.value = null;
            metadataParams.value = null;
            modalStructureEditor.value = null;
            pendingHeroStructureSmiles.value = null;
            updateUrl(false);
        };

        const tryHeroStructureExample = (inputValue, smiles) => {
            heroSearchType.value =
                heroSearchTypes.find((t) => t.type === "structure") ??
                heroSearchTypes[0];

            heroSearchQuery.value = inputValue;

            if (heroSearchInput.value) {
                heroSearchInput.value.value = inputValue;
            }

            heroStructureDraft.value = smiles;
            pendingHeroStructureSmiles.value = smiles;
        };

        const selectAndProceed = (type) => {
            selectedType.value = type;
            emit("search-type-selected", type);

            // Update URL with selected type
            updateUrl(true, type);

            // Handle structure search differently - show editor instead of navigating
            if (type === "structure") {
                showStructureEditor.value = true;
                return;
            }

            // Handle spectra search - show upload interface
            if (type === "spectra") {
                showSpectraUpload.value = true;
                return;
            }

            // Handle peak list search - show peak list form
            if (type === "peaks") {
                showPeakListSearch.value = true;
                return;
            }

            // Handle metadata search - show metadata form
            if (type === "metadata") {
                showMetadataSearch.value = true;
                return;
            }
        };

        const resetModalToSearchOptions = () => {
            selectedType.value = null;
            showStructureEditor.value = false;
            showSpectraUpload.value = false;
            showPeakListSearch.value = false;
            showMetadataSearch.value = false;
        };

        const hasMetadataCriteria = (params) => {
            if (!params) {
                return false;
            }

            return Object.values(params).some(
                (value) => value !== null && value !== undefined && value !== ""
            );
        };

        const hasOnlyFreeTextMetadata = (params) => {
            if (!params?.freeText?.trim()) {
                return false;
            }

            const rest = { ...params };
            delete rest.freeText;

            return !Object.values(rest).some(
                (value) => value !== null && value !== undefined && value !== ""
            );
        };

        const performTextSearch = (query) => {
            const q = (query ?? heroSearchQuery.value).trim();
            if (!q) {
                return;
            }

            router.visit(buildSearchPagePath(SEARCH_SCOPE.CATALOG, { q }));
        };

        const openHeroSearch = () => {
            if (heroSearchType.value.type === "metadata") {
                performTextSearch();
                return;
            }

            if (heroSearchType.value.type === "advanced") {
                performMetadataSearch();
                return;
            }

            if (
                heroSearchType.value.type === "structure" &&
                heroStructureDraft.value &&
                !pendingHeroStructureSmiles.value
            ) {
                pendingHeroStructureSmiles.value = heroStructureDraft.value;
            }

            open.value = true;
            selectAndProceed(heroSearchType.value.type);
            if (heroSearchInput.value) {
                heroSearchInput.value.blur();
            }
        };

        const backToOptions = () => {
            showStructureEditor.value = false;
            showSpectraUpload.value = false;
            showPeakListSearch.value = false;
            showMetadataSearch.value = false;
            spectraFiles.value = [];
            peakListParams.value = null;
            metadataParams.value = null;
            selectedType.value = null;
            modalStructureEditor.value = null;
        };

        const handleSpectraFiles = (files) => {
            spectraFiles.value = files;
        };

        const handlePeakListParams = (params) => {
            peakListParams.value = params;
        };

        const handleMetadataParams = (params) => {
            metadataParams.value = params;
        };

        const performStructureSearch = () => {
            const editor =
                open.value && showStructureEditor.value
                    ? modalStructureEditor.value
                    : heroStructureEditor.value;

            if (editor) {
                const smiles = editor.getSmiles();
                window.location.href = buildSearchPagePath(
                    SEARCH_SCOPE.COMPOUNDS,
                    {
                        query: smiles,
                        type: structureSearchType.value,
                    }
                );
            }
        };

        const performSpectraSearch = () => {
            if (spectraFiles.value.length === 0) {
                return;
            }
            // TODO: Implement spectra file upload and search
            // For now, just log the files
            console.log("Searching with spectra files:", spectraFiles.value);
            alert(
                `Ready to search with ${spectraFiles.value.length} file(s). Upload functionality will be implemented next.`
            );
        };

        const performPeakListSearch = () => {
            if (!peakListParams.value || !peakListParams.value.peaks) {
                alert("Please enter at least one chemical peak");
                return;
            }
            // TODO: Implement peak list search
            console.log("Searching with peak list:", peakListParams.value);
            alert("Peak list search functionality will be implemented next.");
        };

        const performMetadataSearch = () => {
            if (!hasMetadataCriteria(metadataParams.value)) {
                alert("Please enter search criteria");
                return;
            }

            if (hasOnlyFreeTextMetadata(metadataParams.value)) {
                performTextSearch(metadataParams.value.freeText);
                return;
            }

            alert(
                "Field-specific metadata search will be implemented next. Use free text search for now."
            );
        };

        // Check URL on mount and open modal if search param exists
        onMounted(() => {
            const url = new URL(window.location.href);
            const searchParam = url.searchParams.get("search");

            if (searchParam) {
                open.value = true;
                const validTypes = [
                    "structure",
                    "spectra",
                    "peaks",
                    "metadata",
                ];

                if (searchParam === "advanced" || searchParam === "open") {
                    resetModalToSearchOptions();
                } else if (validTypes.includes(searchParam)) {
                    selectedType.value = searchParam;

                    if (searchParam === "structure") {
                        showStructureEditor.value = true;
                    }
                }
            }
        });

        return {
            open,
            selectedType,
            showStructureEditor,
            showSpectraUpload,
            showPeakListSearch,
            showMetadataSearch,
            heroStructureInitialSmiles,
            modalStructureInitialSmiles,
            onHeroStructureEditorReady,
            onModalStructureEditorReady,
            structureSearchType,
            spectraFiles,
            peakListParams,
            metadataParams,
            searchButton,
            heroSearchInput,
            heroSearchTypes,
            heroSearchType,
            heroPlaceholder,
            heroStructureActionLabel,
            onHeroSearchInput,
            caffeineExampleSmiles,
            tryHeroStructureExample,
            searchOptions,
            openModal,
            openHeroSearch,
            closeModal,
            selectAndProceed,
            backToOptions,
            handleSpectraFiles,
            handlePeakListParams,
            handleMetadataParams,
            performStructureSearch,
            performSpectraSearch,
            performPeakListSearch,
            performMetadataSearch,
        };
    },
};
</script>
