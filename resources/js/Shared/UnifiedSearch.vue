<template>
    <div>
        <button
            ref="searchButton"
            type="button"
            class="inline-flex items-center justify-center px-6 py-3 text-base font-medium rounded-full text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200"
            @click="openModal"
        >
            <MagnifyingGlassIcon class="h-5 w-5 mr-2" aria-hidden="true" />
            Search
        </button>

        <TransitionRoot :show="open" as="template" appear>
            <Dialog as="div" class="relative z-50" @close="closeModal">
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
                        v-if="!showStructureEditor && !showSpectraUpload && !showPeakListSearch && !showMetadataSearch"
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
                                    class="group relative bg-white rounded-3xl p-6 text-left transition-all duration-200 hover:shadow-lg active:scale-[0.98] min-h-[320px] flex flex-col cursor-pointer overflow-hidden"
                                    :class="selectedType === option.type ? 'border-2 border-blue-500 shadow-lg' : 'border border-gray-200 hover:border-gray-300'"
                                    @click="selectAndProceed(option.type)"
                                >
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

                                    <!-- Plus Button -->
                                    <div
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
                            class="mx-auto w-[90vw] max-w-6xl max-h-[90vh] transform transition-all bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
                        >
                            <!-- Main Content -->
                            <div class="flex-1 overflow-y-auto">
                                <div class="px-4 py-6 max-w-6xl mx-auto">
                                    <StructureEditorContent
                                        editor-id="structureSearchEditor"
                                        v-model:search-type="structureSearchType"
                                        :editor="structureEditor"
                                    />
                                </div>
                            </div>
                            
                            <!-- Footer with Actions -->
                            <div class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <a
                                    href="#"
                                    autofocus
                                    @click.prevent="backToOptions"
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    @click.prevent="performStructureSearch"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
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
                            <div class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <a
                                    href="#"
                                    autofocus
                                    @click.prevent="backToOptions"
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    @click.prevent="performSpectraSearch"
                                    :class="spectraFiles.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-800'"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                >
                                    Search {{ spectraFiles.length > 0 ? `(${spectraFiles.length} file${spectraFiles.length > 1 ? 's' : ''})` : '' }}
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
                                        @search-params-updated="handlePeakListParams"
                                    />
                                </div>
                            </div>
                            
                            <!-- Footer with Actions -->
                            <div class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <a
                                    href="#"
                                    autofocus
                                    @click.prevent="backToOptions"
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    @click.prevent="performPeakListSearch"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
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
                                        @search-params-updated="handleMetadataParams"
                                    />
                                </div>
                            </div>
                            
                            <!-- Footer with Actions -->
                            <div class="px-4 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                <a
                                    href="#"
                                    autofocus
                                    @click.prevent="backToOptions"
                                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition-colors"
                                >
                                    Cancel
                                </a>
                                <a
                                    href="#"
                                    @click.prevent="performMetadataSearch"
                                    class="px-8 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 shadow-sm transition-colors"
                                >
                                    Search
                                </a>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script>
import { ref, watchEffect, markRaw, onMounted, nextTick } from "vue";
import { useMagicKeys } from "@vueuse/core";
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import {
    MagnifyingGlassIcon,
    BeakerIcon,
    ChartBarIcon,
    QueueListIcon,
    DocumentMagnifyingGlassIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";
import OCL from "openchemlib/full";
import StructureEditorContent from "@/Shared/StructureEditorContent.vue";
import SpectraUploadContent from "@/Shared/SpectraUploadContent.vue";
import PeakListSearchContent from "@/Shared/PeakListSearchContent.vue";
import MetadataSearchContent from "@/Shared/MetadataSearchContent.vue";

export default {
    components: {
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
        MagnifyingGlassIcon,
        BeakerIcon,
        ChartBarIcon,
        QueueListIcon,
        DocumentMagnifyingGlassIcon,
        PlusIcon,
        StructureEditorContent,
        SpectraUploadContent,
        PeakListSearchContent,
        MetadataSearchContent,
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
        const structureEditor = ref(null);
        const structureSearchType = ref("exact");
        const spectraFiles = ref([]);
        const peakListParams = ref(null);
        const metadataParams = ref(null);
        const searchButton = ref(null);

        const searchOptions = [
            {
                type: "structure",
                category: "Structure Search",
                title: "Chemical Structure",
                description:
                    "Draw or paste a structure to search by exact match, substructure, or similarity.",
                image: "/img/molecule-formats.png",
                icon: markRaw(BeakerIcon),
                bgClass: "bg-teal-100",
                iconClass: "text-teal-600",
            },
            {
                type: "spectra",
                category: "Spectra Search",
                title: "NMR Spectra",
                description:
                    "Upload raw NMR data from Bruker or JEOL to find similar spectra.",
                image: "/img/instrument-format.png",
                icon: markRaw(ChartBarIcon),
                bgClass: "bg-indigo-100",
                iconClass: "text-indigo-600",
            },
            {
                type: "peaks",
                category: "Peak Search",
                title: "Peak List",
                description:
                    "Enter peak positions to search for matching spectra in the database.",
                image: "/img/spectra-format.png",
                icon: markRaw(QueueListIcon),
                bgClass: "bg-amber-100",
                iconClass: "text-amber-600",
            },
            {
                type: "metadata",
                category: "Text Search",
                title: "Metadata",
                description:
                    "Search by compound name, author, project, or other metadata.",
                image: "/img/metadata-format.png",
                icon: markRaw(DocumentMagnifyingGlassIcon),
                bgClass: "bg-rose-100",
                iconClass: "text-rose-600",
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
            // Blur the search button to prevent focus trap issues
            if (searchButton.value) {
                searchButton.value.blur();
            }
        };

        const closeModal = () => {
            open.value = false;
            showStructureEditor.value = false;
            showSpectraUpload.value = false;
            showPeakListSearch.value = false;
            showMetadataSearch.value = false;
            spectraFiles.value = [];
            peakListParams.value = null;
            metadataParams.value = null;
            updateUrl(false);
        };

        const selectAndProceed = (type) => {
            selectedType.value = type;
            emit("search-type-selected", type);

            // Update URL with selected type
            updateUrl(true, type);

            // Handle structure search differently - show editor instead of navigating
            if (type === "structure") {
                showStructureEditor.value = true;
                // Initialize the structure editor after DOM update
                nextTick(() => {
                    initializeStructureEditor();
                });
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

        const initializeStructureEditor = () => {
            if (document.getElementById("structureSearchEditor")) {
                structureEditor.value = OCL.StructureEditor.createSVGEditor(
                    "structureSearchEditor",
                    1
                );
                
                // Check if there's a query parameter in URL and load it
                const url = new URL(window.location.href);
                const querySmiles = url.searchParams.get("query");
                if (querySmiles && structureEditor.value) {
                    try {
                        // Set the molecule from SMILES
                        structureEditor.value.setMolFile(
                            OCL.Molecule.fromSmiles(decodeURIComponent(querySmiles)).toMolfile()
                        );
                    } catch (error) {
                        console.error("Error loading structure from query:", error);
                    }
                }
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
            if (structureEditor.value) {
                const smiles = structureEditor.value.getSmiles();
                window.location.href =
                    "/compounds/?query=" +
                    encodeURI(smiles) +
                    "&type=" +
                    structureSearchType.value;
            }
        };

        const performSpectraSearch = () => {
            if (spectraFiles.value.length === 0) {
                return;
            }
            // TODO: Implement spectra file upload and search
            // For now, just log the files
            console.log("Searching with spectra files:", spectraFiles.value);
            alert(`Ready to search with ${spectraFiles.value.length} file(s). Upload functionality will be implemented next.`);
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
            if (!metadataParams.value) {
                alert("Please enter search criteria");
                return;
            }
            // TODO: Implement metadata search
            console.log("Searching with metadata:", metadataParams.value);
            alert("Metadata search functionality will be implemented next.");
        };

        // Check URL on mount and open modal if search param exists
        onMounted(() => {
            const url = new URL(window.location.href);
            const searchParam = url.searchParams.get("search");
            if (searchParam) {
                // Open modal for any search param value
                open.value = true;
                // Set selected type if it matches a valid option
                const validTypes = ["structure", "spectra", "peaks", "metadata"];
                if (validTypes.includes(searchParam)) {
                    selectedType.value = searchParam;
                    // Show structure editor if structure type
                    if (searchParam === "structure") {
                        showStructureEditor.value = true;
                        nextTick(() => {
                            initializeStructureEditor();
                        });
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
            structureEditor,
            structureSearchType,
            spectraFiles,
            peakListParams,
            metadataParams,
            searchButton,
            searchOptions,
            openModal,
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
