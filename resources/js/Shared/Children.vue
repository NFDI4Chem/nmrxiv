<!--
  Recursive File Tree Component
  
  This component renders a hierarchical file/folder tree structure with collapsible
  disclosure panels. It recursively displays nested directories and files with
  appropriate icons and selection states. Used within file browser interfaces
  to show project file structures.
-->
<template>
    <!-- Root container - only render if file exists -->
    <span v-if="file" class="text-sm">
        <!-- Navigation container with sidebar semantics -->
        <nav class="flex-1 space-y-0" aria-label="Sidebar">
            <!-- Main disclosure container for root level folder -->
            <Disclosure
                :key="
                    'tree-root-' +
                    String(file.id ?? file.relative_url ?? '') +
                    '-' +
                    (file.name == '/' || isExpanded(file.id) ? 'o' : 'c')
                "
                v-slot="{ open }"
                as="div"
                :default-open="file.name == '/' || isExpanded(file.id)"
                class="space-y-1"
            >
                <!-- Folder header with selection highlighting -->
                <div
                    style="user-select: none"
                    :class="[
                        selectedFileSystemObject &&
                        selectedFileSystemObject.relative_url ==
                            file.relative_url
                            ? 'bg-gray-100 text-gray-900'
                            : 'text-gray-600',
                        'group flex w-full items-center rounded-md py-1 pr-2 text-left font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500',
                    ]"
                >
                    <!-- Expand/collapse chevron only — does not select the folder -->
                    <DisclosureButton
                        class="flex-shrink-0 rounded p-0.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        :aria-label="open ? 'Collapse folder' : 'Expand folder'"
                        @click.stop.prevent="
                            handleDisclosureButtonClick(file.id)
                        "
                    >
                        <span v-if="file.loading">
                            <ArrowPathIcon
                                class="ml-1 inline h-4 w-4 animate-spin text-dark"
                                aria-hidden="true"
                            />
                        </span>
                        <ChevronRightIcon
                            v-else
                            :class="[
                                open
                                    ? 'rotate-90 text-gray-700'
                                    : 'text-gray-300',
                                'ml-1 h-4 w-4 flex-shrink-0 transform transition-colors duration-150 ease-in-out group-hover:text-gray-700',
                            ]"
                            aria-hidden="true"
                        />
                    </DisclosureButton>

                    <!-- Folder label — selects without expanding -->
                    <span
                        :class="sampleFolderRowClasses(file)"
                        :aria-disabled="
                            isSampleFolderProcessing(file) ? 'true' : undefined
                        "
                        @click.stop="handleFolderClick(file)"
                        @contextmenu="onSampleFolderContextMenu($event, file)"
                    >
                        <!-- Dynamic icon for directories (instrument-specific or generic) -->
                        <span v-if="file.type == 'directory'">
                            <!-- Instrument-specific icons -->
                            <img
                                v-if="file.instrument_type == 'bruker'"
                                class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                src="/img/bruker.jpg"
                                alt="Bruker"
                            />
                            <img
                                v-else-if="file.instrument_type == 'varian'"
                                class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                src="/img/varian.jpeg"
                                alt="Varian"
                            />
                            <img
                                v-else-if="file.instrument_type == 'magritek'"
                                class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                src="/img/magritek.png"
                                alt="Magritek"
                            />
                            <img
                                v-else-if="file.instrument_type == 'joel'"
                                class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                src="/img/joel.jpg"
                                alt="JOEL"
                            />
                            <img
                                v-else-if="file.instrument_type == 'jcamp'"
                                class="flex-shrink-0 h-5 w-5 text-gray-700"
                                src="/img/jcamp.png"
                                alt="JCAMP"
                            />
                            <!-- Study folder: processing, ready to publish, or incomplete -->
                            <span
                                v-else-if="isSampleFolder(file)"
                                class="relative inline-flex flex-shrink-0"
                                :title="sampleFolderStatusTitle(file)"
                            >
                                <FolderIcon
                                    :class="[
                                        'h-5 w-5',
                                        isSampleFolderSubmitted(file)
                                            ? 'text-gray-400'
                                            : isSampleFolderProcessing(file)
                                            ? 'text-gray-400'
                                            : isSampleFolderReady(file)
                                            ? 'text-green-600'
                                            : 'text-gray-700',
                                    ]"
                                    aria-hidden="true"
                                />
                                <ArrowPathIcon
                                    v-if="
                                        isSampleFolderProcessing(file) &&
                                        !isSampleFolderSubmitted(file)
                                    "
                                    class="absolute -right-1 -top-1 h-3.5 w-3.5 animate-spin text-teal-600"
                                    aria-hidden="true"
                                />
                                <span
                                    v-else-if="
                                        isSampleFolderPending(file) ||
                                        !isSampleFolderReady(file)
                                    "
                                    class="absolute right-0 top-0 flex h-2 w-2"
                                >
                                    <span
                                        class="absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex h-2 w-2 rounded-full bg-sky-500"
                                    ></span>
                                </span>
                            </span>
                            <!-- Default folder icon -->
                            <FolderIcon
                                v-else
                                class="flex-shrink-0 h-5 w-5 text-gray-700"
                                aria-hidden="true"
                            />
                        </span>
                        <!-- Generic folder icon for non-directories -->
                        <span v-else>
                            <FolderIcon
                                class="flex-shrink-0 h-5 w-5 text-gray-700"
                                aria-hidden="true"
                            />
                        </span>
                        <span class="truncate" :title="file.name">{{
                            truncateMiddle(file.name, 25)
                        }}</span>
                    </span>
                </div>

                <!-- Collapsible panel containing child items -->
                <DisclosurePanel class="space-y-1">
                    <!-- Iterate through direct children -->
                    <span
                        v-for="sfile in sortedChildren(file.children)"
                        :key="sfile.id ?? sfile.name"
                    >
                        <div class="ml-2">
                            <!-- Child item container with selection handling -->
                            <div
                                :class="[
                                    sfile.current
                                        ? 'text-gray-900'
                                        : 'cursor-pointer text-gray-600',
                                    'group w-full flex items-center font-medium rounded-md',
                                    sfile.type !== 'directory' ||
                                    !sfile.has_children
                                        ? 'cursor-pointer'
                                        : '',
                                ]"
                                @click.stop="
                                    sfile.type !== 'directory' ||
                                    !sfile.has_children
                                        ? displaySelected(sfile)
                                        : undefined
                                "
                            >
                                <!-- Handle directory children -->
                                <span v-if="sfile.type == 'directory'">
                                    <!-- Directory with children - create nested disclosure -->
                                    <span v-if="sfile.has_children">
                                        <Disclosure
                                            :key="
                                                'tree-folder-' +
                                                sfile.id +
                                                '-' +
                                                (isExpanded(sfile.id)
                                                    ? 'o'
                                                    : 'c')
                                            "
                                            v-slot="{ open }"
                                            as="div"
                                            class="space-y-1"
                                            :default-open="isExpanded(sfile.id)"
                                        >
                                            <!-- Nested directory header -->
                                            <div
                                                :class="[
                                                    selectedFileSystemObject &&
                                                    selectedFileSystemObject.relative_url ==
                                                        sfile.relative_url
                                                        ? 'bg-gray-100 text-gray-900'
                                                        : 'text-gray-600',
                                                    'group flex w-full rounded-md py-1 pr-4 text-left font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                                ]"
                                            >
                                                <DisclosureButton
                                                    class="flex-shrink-0 rounded p-0.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                                    :aria-label="
                                                        open
                                                            ? 'Collapse folder'
                                                            : 'Expand folder'
                                                    "
                                                    @click.stop.prevent="
                                                        handleDisclosureButtonClick(
                                                            sfile.id
                                                        )
                                                    "
                                                >
                                                    <span v-if="sfile.loading">
                                                        <ArrowPathIcon
                                                            class="ml-1 inline h-4 w-4 animate-spin text-dark"
                                                            aria-hidden="true"
                                                        />
                                                    </span>
                                                    <ChevronRightIcon
                                                        v-else
                                                        :class="[
                                                            open
                                                                ? 'rotate-90 text-gray-700'
                                                                : 'text-gray-300',
                                                            'ml-1 h-4 w-4 flex-shrink-0 transform transition-colors duration-150 ease-in-out group-hover:text-gray-700',
                                                        ]"
                                                        aria-hidden="true"
                                                    />
                                                </DisclosureButton>

                                                <span
                                                    :class="
                                                        sampleFolderRowClasses(
                                                            sfile
                                                        )
                                                    "
                                                    style="user-select: none"
                                                    :aria-disabled="
                                                        isSampleFolderProcessing(
                                                            sfile
                                                        )
                                                            ? 'true'
                                                            : undefined
                                                    "
                                                    @click.stop="
                                                        handleFolderClick(sfile)
                                                    "
                                                    @contextmenu="
                                                        onSampleFolderContextMenu(
                                                            $event,
                                                            sfile
                                                        )
                                                    "
                                                >
                                                    <span
                                                        v-if="
                                                            sfile.type ==
                                                            'directory'
                                                        "
                                                    >
                                                        <!-- Instrument-specific icons -->
                                                        <img
                                                            v-if="
                                                                sfile.instrument_type ==
                                                                'bruker'
                                                            "
                                                            class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                                            src="/img/bruker.jpg"
                                                            alt="Bruker"
                                                        />
                                                        <img
                                                            v-else-if="
                                                                sfile.instrument_type ==
                                                                'varian'
                                                            "
                                                            class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                                            src="/img/varian.jpeg"
                                                            alt="Varian"
                                                        />
                                                        <img
                                                            v-else-if="
                                                                sfile.instrument_type ==
                                                                'magritek'
                                                            "
                                                            class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                                            src="/img/magritek.png"
                                                            alt="Magritek"
                                                        />
                                                        <img
                                                            v-else-if="
                                                                sfile.instrument_type ==
                                                                'joel'
                                                            "
                                                            class="flex-shrink-0 h-5 w-5 text-gray-700 border rounded-md"
                                                            src="/img/joel.jpg"
                                                            alt="JOEL"
                                                        />
                                                        <img
                                                            v-else-if="
                                                                sfile.instrument_type ==
                                                                'jcamp'
                                                            "
                                                            class="flex-shrink-0 h-5 w-5 text-gray-700"
                                                            src="/img/jcamp.png"
                                                            alt="JCAMP"
                                                        />
                                                        <!-- Study folder: processing, ready to publish, or incomplete -->
                                                        <span
                                                            v-else-if="
                                                                isSampleFolder(
                                                                    sfile
                                                                )
                                                            "
                                                            class="relative inline-flex flex-shrink-0"
                                                            :title="
                                                                sampleFolderStatusTitle(
                                                                    sfile
                                                                )
                                                            "
                                                        >
                                                            <FolderIcon
                                                                :class="[
                                                                    'h-5 w-5',
                                                                    isSampleFolderSubmitted(
                                                                        sfile
                                                                    )
                                                                        ? 'text-gray-400'
                                                                        : isSampleFolderProcessing(
                                                                              sfile
                                                                          )
                                                                        ? 'text-gray-400'
                                                                        : isSampleFolderReady(
                                                                              sfile
                                                                          )
                                                                        ? 'text-green-600'
                                                                        : 'text-gray-700',
                                                                ]"
                                                                aria-hidden="true"
                                                            />
                                                            <ArrowPathIcon
                                                                v-if="
                                                                    isSampleFolderProcessing(
                                                                        sfile
                                                                    ) &&
                                                                    !isSampleFolderSubmitted(
                                                                        sfile
                                                                    )
                                                                "
                                                                class="absolute -right-1 -top-1 h-3.5 w-3.5 animate-spin text-teal-600"
                                                                aria-hidden="true"
                                                            />
                                                            <span
                                                                v-else-if="
                                                                    isSampleFolderPending(
                                                                        sfile
                                                                    ) ||
                                                                    (!isSampleFolderSubmitted(
                                                                        sfile
                                                                    ) &&
                                                                        !isSampleFolderReady(
                                                                            sfile
                                                                        ))
                                                                "
                                                                class="absolute right-0 top-0 flex h-2 w-2"
                                                            >
                                                                <span
                                                                    class="absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"
                                                                ></span>
                                                                <span
                                                                    class="relative inline-flex h-2 w-2 rounded-full bg-sky-500"
                                                                ></span>
                                                            </span>
                                                        </span>
                                                        <!-- Default folder icon -->
                                                        <FolderIcon
                                                            v-else
                                                            class="flex-shrink-0 h-5 w-5 text-gray-700"
                                                            aria-hidden="true"
                                                        />
                                                    </span>
                                                    <span v-else>
                                                        <DocumentTextIcon
                                                            class="h-5 w-5 flex-shrink-0 text-gray-700"
                                                            aria-hidden="true"
                                                        />
                                                    </span>
                                                    <span
                                                        class="truncate"
                                                        :title="sfile.name"
                                                        >{{
                                                            truncateMiddle(
                                                                sfile.name,
                                                                25
                                                            )
                                                        }}</span
                                                    >
                                                </span>
                                            </div>

                                            <!-- Panel for nested directory children -->
                                            <DisclosurePanel class="space-y-0">
                                                <!-- Deep nested items -->
                                                <div
                                                    v-for="subItem in sortedChildren(
                                                        sfile.children
                                                    )"
                                                    :key="
                                                        subItem.id ??
                                                        subItem.name
                                                    "
                                                    as="div"
                                                    class="cursor-pointer group w-full flex pl-4 pr-2 py-0 font-medium text-gray-600 rounded-md"
                                                    @click.stop="
                                                        displaySelected(subItem)
                                                    "
                                                >
                                                    <!-- Recursive call for nested directories -->
                                                    <span
                                                        v-if="
                                                            subItem.type ==
                                                            'directory'
                                                        "
                                                    >
                                                        <children
                                                            :file="subItem"
                                                            :files="
                                                                subItem.children
                                                            "
                                                            :study="study"
                                                            :project="project"
                                                            :studies="studies"
                                                            :isolate-selection="
                                                                isolateSelection
                                                            "
                                                            :submitted-study-ids="
                                                                submittedStudyIds
                                                            "
                                                            :studies-workspace-ready="
                                                                studiesWorkspaceReady
                                                            "
                                                            :draft-processing="
                                                                draftProcessing
                                                            "
                                                            :expanded-folders="
                                                                expandedFolders
                                                            "
                                                            :tree-sort-by="
                                                                treeSortBy
                                                            "
                                                            :tree-sort-order="
                                                                treeSortOrder
                                                            "
                                                            @toggle-expansion="
                                                                (
                                                                    fsoId,
                                                                    isOpen
                                                                ) =>
                                                                    $emit(
                                                                        'toggle-expansion',
                                                                        fsoId,
                                                                        isOpen
                                                                    )
                                                            "
                                                            @load-folder-children="
                                                                (folder) =>
                                                                    $emit(
                                                                        'load-folder-children',
                                                                        folder
                                                                    )
                                                            "
                                                            @study-context-menu="
                                                                (payload) =>
                                                                    $emit(
                                                                        'study-context-menu',
                                                                        payload
                                                                    )
                                                            "
                                                            @sample-folder-selected="
                                                                (folder) =>
                                                                    $emit(
                                                                        'sample-folder-selected',
                                                                        folder
                                                                    )
                                                            "
                                                        />
                                                    </span>

                                                    <!-- File item display -->
                                                    <span
                                                        v-else
                                                        :class="[
                                                            selectedFileSystemObject &&
                                                            selectedFileSystemObject.relative_url ==
                                                                subItem.relative_url
                                                                ? 'cursor-pointer bg-gray-100'
                                                                : 'cursor-pointer bg-white text-gray-600',
                                                            'p-1 rounded-md truncate ...',
                                                        ]"
                                                    >
                                                        <span
                                                            :class="[
                                                                subItem.status ==
                                                                'missing'
                                                                    ? 'text-red-800'
                                                                    : '',
                                                            ]"
                                                        >
                                                            <DocumentTextIcon
                                                                class="mr-1 inline h-5 w-5 text-gray-700"
                                                                aria-hidden="true"
                                                            />
                                                            <span
                                                                :title="
                                                                    subItem.name
                                                                "
                                                                >{{
                                                                    truncateMiddle(
                                                                        subItem.name,
                                                                        25
                                                                    )
                                                                }}</span
                                                            >
                                                        </span>
                                                    </span>
                                                </div>
                                            </DisclosurePanel>
                                        </Disclosure>
                                    </span>
                                </span>

                                <!-- File item (not directory) -->
                                <span
                                    v-else
                                    :class="[
                                        selectedFileSystemObject &&
                                        selectedFileSystemObject.relative_url ==
                                            sfile.relative_url
                                            ? 'cursor-pointer bg-gray-100'
                                            : 'cursor-pointer bg-white text-gray-600',
                                        'p-1 ml-5 rounded-md truncate ...',
                                    ]"
                                >
                                    <span
                                        :class="[
                                            sfile.status == 'missing'
                                                ? 'text-red-800'
                                                : '',
                                            'break-all',
                                        ]"
                                    >
                                        <DocumentTextIcon
                                            class="inline mr-1 h-5 w-5 text-gray-700"
                                            aria-hidden="true"
                                        />
                                        <span :title="sfile.name">{{
                                            truncateMiddle(sfile.name, 25)
                                        }}</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </span>
                </DisclosurePanel>
            </Disclosure>
        </nav>
    </span>
</template>

<script>
/**
 * Recursive File Tree Component
 *
 * This component creates a hierarchical, collapsible file tree structure using
 * HeadlessUI's Disclosure components. It recursively renders itself for nested
 * directories and handles file/folder selection, expansion state, and dynamic
 * loading of folder contents.
 *
 * Key Features:
 * - Recursive rendering for nested directory structures
 * - Collapsible/expandable folders with disclosure panels
 * - Dynamic loading of folder contents via API calls
 * - Visual indicators for file types and instrument types
 * - Selection state management with visual highlighting
 * - Missing file status indication
 * - URL synchronization for selected items
 */

// Icon imports from Heroicons
import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/solid";

// HeadlessUI imports for disclosure functionality
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import {
    isSampleFolderReadyToPublish,
    isStudyActivelyProcessing,
    isStudyFolderSubmitted,
    isStudyFolderProcessing,
    findStudyForFolder,
    isDraftSampleFolder,
} from "@/Composables/useDraftProcessing";

export default {
    name: "Children",

    /**
     * Component dependencies
     */
    components: {
        Disclosure, // HeadlessUI disclosure container
        DisclosureButton, // HeadlessUI disclosure trigger button
        DisclosurePanel, // HeadlessUI collapsible content panel
        FolderIcon, // Folder icon from Heroicons
        DocumentTextIcon, // Document/file icon from Heroicons
        ChevronRightIcon, // Right chevron icon for navigation
        ArrowPathIcon, // Loading/refresh icon
    },

    /**
     * Component props
     * @prop {Object} study - Study data object
     * @prop {Object} project - Project data object
     * @prop {Object} file - Current file/folder object to render
     * @prop {String} treeSortBy - 'alphabetical' or 'timestamp' (sidebar tree)
     * @prop {String} treeSortOrder - 'asc' or 'desc'
     */
    props: {
        study: {
            type: Object,
            default: null,
        },
        project: {
            type: Object,
            default: null,
        },
        file: {
            type: Object,
            default: null,
        },
        expandedFolders: {
            type: Object,
            default: null,
        },
        treeSortBy: {
            type: String,
            default: "alphabetical",
        },
        treeSortOrder: {
            type: String,
            default: "asc",
        },
        studies: {
            type: Array,
            default: () => [],
        },
        isolateSelection: {
            type: Boolean,
            default: false,
        },
        submittedStudyIds: {
            type: Array,
            default: () => [],
        },
        studiesWorkspaceReady: {
            type: Boolean,
            default: false,
        },
        draftProcessing: {
            type: Boolean,
            default: false,
        },
    },

    /**
     * Events emitted by this component
     */
    emits: [
        "toggle-expansion",
        "load-folder-children",
        "study-context-menu",
        "sample-folder-selected",
    ],

    /**
     * Composition API setup function
     * @returns {Object} Empty object - using Options API instead
     */
    setup() {
        return {};
    },

    data() {
        return {
            localSelectedFile: null,
            localSelectedFolder: "/",
        };
    },

    computed: {
        selectedFileSystemObject() {
            if (this.isolateSelection) {
                return this.localSelectedFile;
            }

            return this.$page.props.selectedFileSystemObject ?? null;
        },

        selectedFolder() {
            if (this.isolateSelection) {
                return this.localSelectedFolder;
            }

            return this.$page.props.selectedFolder ?? "/";
        },

        submittedStudyIdSet() {
            return new Set(
                (this.submittedStudyIds ?? []).map((id) => Number(id))
            );
        },
    },

    mounted() {},

    /**
     * Component methods
     */
    methods: {
        /**
         * Truncate text in the middle with ellipsis for long file/folder names
         *
         * This method prevents UI overflow by shortening long names while
         * preserving both the beginning and end of the filename, which is
         * often more useful than simple truncation.
         *
         * @param {String} text - The text to truncate
         * @param {Number} maxLength - Maximum allowed length including ellipsis
         * @returns {String} Truncated text with ellipsis in the middle
         *
         * @example
         * truncateMiddle("very-long-filename.txt", 15)
         * // Returns: "very-l...me.txt"
         */
        truncateMiddle(text, maxLength) {
            // Return original text if it's short enough or invalid
            if (!text || text.length <= maxLength) {
                return text;
            }

            // Calculate start and end lengths (accounting for 3-character ellipsis)
            const start = Math.ceil((maxLength - 3) / 2);
            const end = Math.floor((maxLength - 3) / 2);

            // Return truncated string with ellipsis in the middle
            return (
                text.substring(0, start) +
                "..." +
                text.substring(text.length - end)
            );
        },

        /**
         * Check if a folder is currently expanded
         *
         * Uses the expandedFolders Set to track which folders are open.
         * This allows the component to maintain expansion state across re-renders.
         *
         * @param {String|Number} fsoId - File system object ID
         * @returns {Boolean} True if folder is expanded, false otherwise
         */
        isExpanded(fsoId) {
            return this.expandedFolders && this.expandedFolders.has(fsoId);
        },

        /**
         * Return a sorted copy of folder children for the sidebar tree.
         *
         * @param {Array|null|undefined} children
         * @returns {Array}
         */
        sortedChildren(children) {
            if (!Array.isArray(children) || children.length === 0) {
                return [];
            }

            let items = children;

            if (this.submittedStudyIdSet.size > 0) {
                items = items.filter(
                    (child) => !this.isSampleFolderSubmitted(child)
                );
            }

            const mode =
                this.treeSortBy === "timestamp" ? "timestamp" : "alphabetical";
            const order = this.treeSortOrder === "desc" ? "desc" : "asc";
            const mult = order === "asc" ? 1 : -1;

            return [...items].sort((a, b) => {
                if (mode === "alphabetical") {
                    const cmp = String(a.name || "").localeCompare(
                        String(b.name || ""),
                        undefined,
                        { sensitivity: "base", numeric: true }
                    );
                    if (cmp !== 0) {
                        return cmp * mult;
                    }

                    return 0;
                }

                const ta = new Date(
                    a.updated_at || a.created_at || 0
                ).getTime();
                const tb = new Date(
                    b.updated_at || b.created_at || 0
                ).getTime();
                if (ta !== tb) {
                    return (ta < tb ? -1 : 1) * mult;
                }

                return String(a.name || "").localeCompare(
                    String(b.name || ""),
                    undefined,
                    { sensitivity: "base", numeric: true }
                );
            });
        },

        /**
         * Handle folder click events
         *
         * When a folder is clicked, select it to show its contents in the
         * main file browser panel. This is separate from expansion/collapse.
         *
         * @param {Object} file - The folder object that was clicked
         */
        isSampleFolder(file) {
            return isDraftSampleFolder(file);
        },

        isSampleFolderSubmitted(file) {
            return isStudyFolderSubmitted(file, this.submittedStudyIdSet);
        },

        isSampleFolderProcessing(file) {
            if (this.isSampleFolderSubmitted(file)) {
                return false;
            }

            return isStudyFolderProcessing(
                file,
                this.studies,
                this.submittedStudyIdSet,
                {
                    studiesWorkspaceReady: this.studiesWorkspaceReady,
                    draftProcessing: this.draftProcessing,
                }
            );
        },

        isSampleFolderPending(file) {
            if (
                this.isSampleFolderSubmitted(file) ||
                this.isSampleFolderProcessing(file) ||
                this.isSampleFolderReady(file)
            ) {
                return false;
            }

            const study = findStudyForFolder(file, this.studies);

            return Boolean(
                study &&
                    study.internal_status !== "complete" &&
                    !isStudyActivelyProcessing(study)
            );
        },

        isSampleFolderReady(file) {
            return isSampleFolderReadyToPublish(
                file,
                this.studies,
                this.submittedStudyIdSet
            );
        },

        sampleFolderStatusTitle(file) {
            if (this.isSampleFolderSubmitted(file)) {
                return "Submitted for publication — will be removed from this draft when processing finishes";
            }

            if (this.isSampleFolderProcessing(file)) {
                return "Processing sample…";
            }

            if (this.isSampleFolderPending(file)) {
                return "Waiting for spectral processing to finish";
            }

            if (this.isSampleFolderReady(file)) {
                return "Ready to publish (NMRium and structure assigned)";
            }

            return "Sample needs NMRium and/or structure before publishing";
        },

        sampleFolderRowClasses(file) {
            const processing = this.isSampleFolderProcessing(file);
            const submitted = this.isSampleFolderSubmitted(file);

            return [
                file.status == "missing" ? "text-red-800" : "",
                "inline-flex min-w-0 flex-1 items-center gap-1 truncate rounded-md py-0.5 pl-0.5",
                processing
                    ? "cursor-not-allowed opacity-50 text-gray-400"
                    : submitted
                    ? "cursor-default opacity-60 text-gray-500"
                    : "cursor-pointer",
            ];
        },

        handleFolderClick(file) {
            if (
                this.isSampleFolderSubmitted(file) ||
                this.isSampleFolderProcessing(file)
            ) {
                return;
            }

            this.displaySelected(file);
        },

        /**
         * Right-click handler for folders. Only sample folders (those with
         * `model_type == 'study'`) can be reset, so we only emit for those
         * — all other folders fall through to the browser's native menu.
         *
         * @param {MouseEvent} event - Native contextmenu event
         * @param {Object} file - The folder object the user right-clicked
         */
        onSampleFolderContextMenu(event, file) {
            if (
                !file ||
                !this.isSampleFolder(file) ||
                this.isSampleFolderProcessing(file)
            ) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            this.$emit("study-context-menu", {
                file,
                x: event.clientX,
                y: event.clientY,
            });
        },

        /**
         * Handle chevron clicks — toggle expansion from parent state, not
         * HeadlessUI's internal open flag (which can desync during remounts
         * or while a children request is still pending).
         *
         * @param {String|Number} fsoId - File system object ID
         */
        handleDisclosureButtonClick(fsoId) {
            const isCurrentlyExpanded = this.isExpanded(fsoId);
            this.$emit("toggle-expansion", fsoId, !isCurrentlyExpanded);
        },

        /**
         * Context menu option clicked handler (legacy)
         *
         * @deprecated This method appears to be unused legacy code
         * @param {Event} event - Click event object
         */
        optionClicked1(event) {
            window.alert(JSON.stringify(event));
        },

        /**
         * Context menu handler (legacy)
         *
         * @deprecated This method appears to be unused legacy code
         * @param {Event} event - Click event object
         * @param {Object} item - Menu item object
         */
        handleClick1(event, item) {
            this.$refs.vueSimpleContextMenu1.showMenu(event, item);
        },

        /**
         * Display selected file/folder and update application state
         *
         * This method handles the core selection logic:
         * - Updates global selected file system object
         * - Calculates and sets the current folder path
         * - Updates URL to reflect selection
         * - Lazy loads folder contents if needed
         *
         * @param {Object} file - The file or folder object to select
         */
        displaySelected(file) {
            if (
                this.isSampleFolderSubmitted(file) ||
                this.isSampleFolderProcessing(file)
            ) {
                return;
            }

            // Calculate the current folder path for breadcrumb navigation
            let sFolder = "/";
            if (file.name == "/") {
                sFolder = "/";
            } else if (file.type != "file") {
                sFolder = file.relative_url;
            } else if (file.parent_id == null) {
                sFolder = "/";
            } else {
                sFolder = file.relative_url.replace("/" + file.name, "");
            }

            if (this.isolateSelection) {
                this.localSelectedFile = file;
                this.localSelectedFolder = sFolder;
            } else {
                this.$page.props.selectedFileSystemObject = file;
                this.$page.props.selectedFolder = sFolder;
                this.updateURLWithSelection(file.id);
            }

            // Lazy load folder contents when children are not loaded yet
            if (
                file.has_children &&
                file.id &&
                (!file.children || file.children.length === 0) &&
                !file.loading
            ) {
                this.$emit("load-folder-children", file);
            }

            if (this.isSampleFolder(file)) {
                this.$emit("sample-folder-selected", file);
            }
        },

        /**
         * Update URL with selected folder ID for bookmarking and navigation
         *
         * Modifies the current URL to include the selected file/folder ID
         * as a query parameter. This allows users to bookmark specific
         * file browser states and enables back/forward navigation.
         *
         * @param {String|Number} selectedId - ID of the selected file/folder
         */
        updateURLWithSelection(selectedId) {
            // Parse current URL parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Set or update the selected parameter
            urlParams.set("selected", selectedId);

            // Construct new URL with updated parameters
            const newUrl = `${
                window.location.pathname
            }?${urlParams.toString()}`;

            const state =
                window.history.state && typeof window.history.state === "object"
                    ? window.history.state
                    : {};

            window.history.replaceState(state, "", newUrl);
        },
    },
};
</script>
