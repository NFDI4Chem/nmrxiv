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
                v-slot="{ open }"
                as="div"
                :default-open="file.name == '/' || isExpanded(file.id)"
                class="space-y-1"
                @update:open="(isOpen) => handleDisclosureToggle(file.id, isOpen)"
            >
                <!-- Folder header with selection highlighting -->
                <div
                    style="user-select: none"
                    :class="[
                        $page.props.selectedFileSystemObject &&
                        $page.props.selectedFileSystemObject.relative_url ==
                            file.relative_url
                            ? 'cursor-pointer bg-gray-100'
                            : 'cursor-pointer text-gray-600',
                        'group w-full flex items-center pr-2 py-1 text-left font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                    ]"
                    @click.stop="handleFolderClick(file)"
                >
                    <!-- Disclosure button for expanding/collapsing -->
                    <DisclosureButton
                        class="w-full text-left truncate ..."
                        @click="() => handleDisclosureButtonClick(file.id)"
                    >
                        <!-- Loading spinner when fetching children -->
                        <span v-if="file.loading">
                            <svg
                                class="animate-spin mr-3 ml-1 h-5 w-5 text-dark inline"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                />
                            </svg>
                        </span>
                        
                        <!-- Expand/collapse chevron icon -->
                        <span v-else>
                            <svg
                                :class="[
                                    open
                                        ? 'text-gray-700 rotate-90'
                                        : 'text-gray-300',
                                    'mr-2 flex-shrink-0 inline h-5 w-5 transform group-hover:text-gray-700 transition-colors ease-in-out duration-150',
                                ]"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    d="M6 6L14 10L6 14V6Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </span>
                        
                        <!-- File/folder name with icon and status styling -->
                        <span
                            :class="[
                                file.status == 'missing' ? 'text-red-800' : '',
                                'break-all',
                            ]"
                        >
                            <!-- Dynamic icon for directories (instrument-specific or generic) -->
                            <span
                                v-if="file.type == 'directory'"
                                v-html="composeIcon(file)"
                            />
                            <!-- Generic folder icon for non-directories -->
                            <span v-else>
                                <FolderIcon
                                    class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"
                                    aria-hidden="true"
                                />
                            </span>
                            {{ truncateMiddle(file.name, 25) }}
                        </span>
                    </DisclosureButton>
                </div>
                
                <!-- Collapsible panel containing child items -->
                <DisclosurePanel class="space-y-1">
                    <!-- Iterate through direct children -->
                    <span v-for="sfile in file.children" :key="sfile.name">
                        <div class="ml-2">
                            <!-- Child item container with selection handling -->
                            <div
                                :class="[
                                    sfile.current
                                        ? 'text-gray-900'
                                        : 'cursor-pointer text-gray-600',
                                    'cursor-pointer group w-full flex items-center font-medium rounded-md',
                                ]"
                                @click.stop="displaySelected(sfile)"
                            >
                                <!-- Handle directory children -->
                                <span v-if="sfile.type == 'directory'">
                                    <!-- Directory with children - create nested disclosure -->
                                    <span v-if="sfile.has_children">
                                        <Disclosure
                                            v-slot="{ open }"
                                            as="div"
                                            class="space-y-1"
                                            :default-open="isExpanded(sfile.id)"
                                            @update:open="
                                                (isOpen) =>
                                                    handleDisclosureToggle(
                                                        sfile.id,
                                                        isOpen
                                                    )
                                            "
                                        >
                                            <!-- Nested directory header -->
                                            <div
                                                :class="[
                                                    $page.props
                                                        .selectedFileSystemObject &&
                                                    $page.props
                                                        .selectedFileSystemObject
                                                        .relative_url ==
                                                        sfile.relative_url
                                                        ? 'cursor-pointer bg-gray-100 text-gray-900'
                                                        : 'cursor-pointer text-gray-600',
                                                    'group w-full flex pr-1 py-1 text-left font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                                ]"
                                                @click.stop="handleFolderClick(sfile)"
                                            >
                                                <!-- Nested disclosure button -->
                                                <DisclosureButton
                                                    class="w-full text-left truncate ..."
                                                    @click="
                                                        () =>
                                                            handleDisclosureButtonClick(
                                                                sfile.id
                                                            )
                                                    "
                                                >
                                                    <!-- Loading state for nested directory -->
                                                    <span v-if="sfile.loading">
                                                        <svg
                                                            class="animate-spin ml-1 mr-3 h-5 w-5 text-dark inline"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <circle
                                                                class="opacity-25"
                                                                cx="12"
                                                                cy="12"
                                                                r="10"
                                                                stroke="currentColor"
                                                                stroke-width="4"
                                                            />
                                                            <path
                                                                class="opacity-75"
                                                                fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                            />
                                                        </svg>
                                                    </span>
                                                    
                                                    <!-- Nested chevron icon -->
                                                    <span v-else>
                                                        <svg
                                                            :class="[
                                                                open
                                                                    ? 'text-gray-700 rotate-90'
                                                                    : 'text-gray-300',
                                                                'mr-2 flex-shrink-0 inline h-5 w-5 transform group-hover:text-gray-700 transition-colors ease-in-out duration-150',
                                                            ]"
                                                            viewBox="0 0 20 20"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                d="M6 6L14 10L6 14V6Z"
                                                                fill="currentColor"
                                                            />
                                                        </svg>
                                                    </span>
                                                    
                                                    <!-- Nested directory name and icon -->
                                                    <span>
                                                        <span
                                                            :class="[
                                                                sfile.status ==
                                                                'missing'
                                                                    ? 'text-red-800'
                                                                    : '',
                                                                '',
                                                            ]"
                                                            style="user-select: none"
                                                        >
                                                            <!-- Dynamic icon for nested directory -->
                                                            <span
                                                                v-if="sfile.type == 'directory'"
                                                                v-html="composeIcon(sfile)"
                                                            />
                                                            <!-- Document icon for files -->
                                                            <span v-else>
                                                                <DocumentTextIcon
                                                                    class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"
                                                                    aria-hidden="true"
                                                                />
                                                            </span>
                                                            {{
                                                                truncateMiddle(
                                                                    sfile.name,
                                                                    25
                                                                )
                                                            }}
                                                        </span>
                                                    </span>
                                                </DisclosureButton>
                                            </div>
                                            
                                            <!-- Panel for nested directory children -->
                                            <DisclosurePanel class="space-y-0">
                                                <!-- Deep nested items -->
                                                <div
                                                    v-for="subItem in sfile.children"
                                                    :key="subItem.name"
                                                    as="div"
                                                    class="cursor-pointer group w-full flex pl-4 pr-2 py-0 font-medium text-gray-600 rounded-md"
                                                    @click.stop="displaySelected(subItem)"
                                                >
                                                    <!-- Recursive call for nested directories -->
                                                    <span v-if="subItem.type == 'directory'">
                                                        <children
                                                            :file="subItem"
                                                            :files="subItem.children"
                                                            :study="study"
                                                            :project="project"
                                                            :expanded-folders="expandedFolders"
                                                            @toggle-expansion="
                                                                (fsoId, isOpen) =>
                                                                    $emit(
                                                                        'toggle-expansion',
                                                                        fsoId,
                                                                        isOpen
                                                                    )
                                                            "
                                                        />
                                                    </span>
                                                    
                                                    <!-- File item display -->
                                                    <span
                                                        v-else
                                                        :class="[
                                                            $page.props
                                                                .selectedFileSystemObject &&
                                                            $page.props
                                                                .selectedFileSystemObject
                                                                .relative_url ==
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
                                                            {{
                                                                truncateMiddle(
                                                                    subItem.name,
                                                                    25
                                                                )
                                                            }}
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
                                        $page.props.selectedFileSystemObject &&
                                        $page.props.selectedFileSystemObject
                                            .relative_url == sfile.relative_url
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
                                        {{ truncateMiddle(sfile.name, 25) }}
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

// Component imports
import StudyContent from "@/Pages/Study/Content.vue";

// Icon imports from Heroicons
import { FolderIcon, DocumentTextIcon } from "@heroicons/vue/24/solid";

// HeadlessUI imports for disclosure functionality
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

export default {
    name: "Children",
    
    /**
     * Component dependencies
     */
    components: {
        StudyContent,           // Study content component (unused but imported)
        Disclosure,             // HeadlessUI disclosure container
        DisclosureButton,       // HeadlessUI disclosure trigger button
        DisclosurePanel,        // HeadlessUI collapsible content panel
        FolderIcon,            // Folder icon from Heroicons
        DocumentTextIcon,      // Document/file icon from Heroicons
    },
    
    /**
     * Component props
     * @prop {Object} study - Study data object
     * @prop {Object} project - Project data object
     * @prop {Object} file - Current file/folder object to render
     * @prop {Set} expandedFolders - Set of expanded folder IDs for state tracking
     */
    props: ["study", "project", "file", "expandedFolders"],
    
    /**
     * Events emitted by this component
     */
    emits: ["toggle-expansion"],
    
    /**
     * Composition API setup function
     * @returns {Object} Empty object - using Options API instead
     */
    setup() {
        return {};
    },
    
    /**
     * Component reactive data
     * @returns {Object} Empty object - no local state needed
     */
    data() {
        return {};
    },
    
    /**
     * Computed properties
     */
    computed: {},
    
    /**
     * Component lifecycle - mounted
     * No initialization needed for this component
     */
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
            return text.substring(0, start) + '...' + text.substring(text.length - end);
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
         * Handle folder click events
         * 
         * When a folder is clicked, select it to show its contents in the
         * main file browser panel. This is separate from expansion/collapse.
         * 
         * @param {Object} file - The folder object that was clicked
         */
        handleFolderClick(file) {
            // Select the folder to show its contents in right panel
            this.displaySelected(file);
        },

        /**
         * Handle disclosure toggle events from HeadlessUI
         * 
         * Emits expansion state changes to parent component for tracking.
         * This allows the parent to maintain a global expansion state.
         * 
         * @param {String|Number} fsoId - File system object ID
         * @param {Boolean} isOpen - New expansion state
         */
        handleDisclosureToggle(fsoId, isOpen) {
            // Emit to parent component to update expansion tracking
            this.$emit("toggle-expansion", fsoId, isOpen);
        },

        /**
         * Handle disclosure button clicks directly
         * 
         * Manually toggles expansion state when the disclosure button is clicked.
         * This provides an alternative way to control expansion beyond HeadlessUI's
         * automatic handling.
         * 
         * @param {String|Number} fsoId - File system object ID
         */
        handleDisclosureButtonClick(fsoId) {
            // Toggle the current state and emit
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
         * Compose appropriate icon HTML for different file types
         * 
         * Returns HTML string for icons based on file type and instrument type.
         * This allows for visual differentiation between different types of
         * scientific data folders (Bruker, Varian, JOEL, JCAMP) and studies.
         * 
         * @param {Object} file - File object with type and instrument information
         * @returns {String} HTML string for the appropriate icon
         */
        composeIcon(file) {
            // Instrument-specific icons for scientific data folders
            if (file.instrument_type) {
                if (file.instrument_type == "bruker") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700 border rounded-md" src="/img/bruker.jpg" alt=""/>';
                } else if (file.instrument_type == "varian") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700 border rounded-md" src="/img/varian.jpeg" alt=""/>';
                } else if (file.instrument_type == "joel") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700 border rounded-md" src="/img/joel.jpg" alt=""/>';
                } else if (file.instrument_type == "jcamp") {
                    return '<img class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700" src="/img/jcamp.png" alt=""/>';
                }
            }

            // Special icon for study folders with notification indicator
            if (file.model_type == "study") {
                return '<span class="relative inline-flex"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg><span class="flex absolute h-2 w-2 top-0 right-0"><span class="absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span></span></span>';
            }

            // Default folder icon for generic directories
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="inline -ml-1.5 mr-1 h-5 w-5 text-gray-700"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>';
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
            // Set the selected file system object globally
            this.$page.props.selectedFileSystemObject = file;
            
            // Calculate the current folder path for breadcrumb navigation
            let sFolder = "/";
            if (this.$page.props.selectedFileSystemObject.name == "/") {
                sFolder = "/";
            } else {
                if (this.$page.props.selectedFileSystemObject.type != "file") {
                    // For directories, use the full relative URL
                    sFolder = this.$page.props.selectedFileSystemObject.relative_url;
                } else {
                    // For files, extract the parent directory path
                    if (this.$page.props.selectedFileSystemObject.parent_id == null) {
                        sFolder = "/";
                    } else {
                        // Remove filename from path to get parent directory
                        sFolder = this.$page.props.selectedFileSystemObject.relative_url.replace(
                            "/" + this.$page.props.selectedFileSystemObject.name,
                            ""
                        );
                    }
                }
            }

            // Update the selected folder for breadcrumb display
            this.$page.props.selectedFolder = sFolder;

            // Update URL with selected folder ID for bookmarking/sharing
            this.updateURLWithSelection(file.id);

            // Lazy load folder contents if this is an unexpanded folder with children
            if (file.has_children && file.level > 0 && !file.children) {
                file.loading = true;
                axios
                    .get("/api/v1/files/children/" + file.id)
                    .then((response) => {
                        file.children = response.data.files[0].children;
                        file.loading = false;
                    });
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
            const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
            
            // Update browser history without triggering page reload
            window.history.replaceState({}, "", newUrl);
        },
    },
};
</script>