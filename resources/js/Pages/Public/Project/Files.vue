<!--
  Project Files Page
  
  Displays the file browser interface for a public project, allowing users to navigate
  through the project's file structure with a breadcrumb navigation system.
-->
<template>
    <!-- Dynamic page title showing project name -->
    <Head :title="'Files - ' + project.data.name" />
    
    <!-- Main project layout wrapper with files tab selected -->
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <!-- Main content container with responsive padding -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6">
                <!-- File browser section - only shown if project has files -->
                <div v-if="project.data.files">
                    <!-- Breadcrumb navigation - hidden on mobile, shown on desktop -->
                    <nav
                        v-if="$page.props.selectedFolder"
                        class="flex p-3 hidden md:flex overflow-hidden"
                        aria-label="Breadcrumb"
                    >
                        <!-- Breadcrumb list with responsive spacing -->
                        <ol
                            role="list"
                            class="flex items-center space-x-1 sm:space-x-2 min-w-0 flex-1"
                        >
                            <!-- Home/Root breadcrumb item -->
                            <li class="flex-shrink-0">
                                <div>
                                    <a class="text-gray-400 hover:text-gray-900">
                                        <!-- Home icon for root directory -->
                                        <HomeIcon
                                            class="flex-shrink-0 h-5 w-5"
                                            aria-hidden="true"
                                        />
                                        <span class="sr-only">Home</span>
                                    </a>
                                </div>
                            </li>
                            
                            <!-- Project name breadcrumb item -->
                            <li class="min-w-0 flex-shrink">
                                <div class="flex items-center min-w-0">
                                    <!-- Chevron separator -->
                                    <ChevronRightIcon
                                        class="flex-shrink-0 h-4 w-4 sm:h-5 sm:w-5 text-gray-400 mx-1 sm:mx-2"
                                        aria-hidden="true"
                                    />
                                    <!-- Project name with truncation for long names -->
                                    <a
                                        class="text-xs sm:text-sm font-medium text-gray-500 hover:text-gray-700 truncate min-w-0"
                                        :title="project.data.name"
                                    >
                                        {{ truncateMiddle(project.data.name, 20) }}
                                    </a>
                                </div>
                            </li>
                            
                            <!-- Dynamic folder path breadcrumb items -->
                            <li
                                v-for="(page, index) in $page.props.selectedFolder
                                    .split('/')
                                    .filter((p) => p !== '')"
                                :key="index"
                                class="min-w-0 flex-shrink"
                            >
                                <div class="flex items-center min-w-0">
                                    <!-- Chevron separator -->
                                    <ChevronRightIcon
                                        class="flex-shrink-0 h-4 w-4 sm:h-5 sm:w-5 text-gray-400 mx-1 sm:mx-2"
                                        aria-hidden="true"
                                    />
                                    <!-- Folder name with current page indicator -->
                                    <a
                                        class="text-xs sm:text-sm font-medium text-gray-500 hover:text-gray-700 truncate min-w-0"
                                        :aria-current="
                                            index ===
                                            $page.props.selectedFolder
                                                .split('/')
                                                .filter((p) => p !== '')
                                                .length - 1
                                                ? 'page'
                                                : undefined
                                        "
                                        :title="page"
                                    >
                                        {{ truncateMiddle(page, 15) }}
                                    </a>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    
                    <!-- File system browser container with border styling -->
                    <div class="border border-gray-200 pb-1 rounded-lg mb-10">
                        <!-- File system browser component in read-only mode -->
                        <file-system-browser
                            ref="fsbRef"
                            :readonly="true"
                            :project="project.data"
                        />
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
/**
 * Project Files Page Component
 * 
 * This component renders the public files view for a project, displaying a file browser
 * with breadcrumb navigation. Users can navigate through the project's file structure
 * in read-only mode.
 */

// Component imports
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import FileSystemBrowser from "./../../../Shared/FileSystemBrowser.vue";

// Icon imports from Heroicons
import {
    FolderIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    HomeIcon,
} from "@heroicons/vue/24/solid";

// Inertia.js imports
import { Head } from "@inertiajs/vue3";

export default {
    name: "ProjectFiles",
    
    /**
     * Component dependencies
     */
    components: {
        ProjectLayout,           // Main project layout wrapper
        FolderIcon,            // Folder icon (unused but imported)
        DocumentTextIcon,      // Document icon (unused but imported)
        ChevronRightIcon,      // Right arrow for breadcrumb separators
        HomeIcon,              // Home icon for root breadcrumb
        FileSystemBrowser,     // Main file browser component
        Head,                  // Inertia head component for page title
    },
    
    /**
     * Component props
     * @prop {Object} project - Project data object containing files and metadata
     * @prop {String} tab - Currently selected tab identifier
     */
    props: ["project", "tab"],
    
    /**
     * Component reactive data
     */
    data() {
        return {
            // Currently selected file system object (file or folder)
            selectedFileSystemObject: null,
        };
    },
    
    /**
     * Computed properties
     */
    computed: {
        /**
         * Get the base URL from page props
         * @returns {String} Base application URL
         */
        url() {
            return String(this.$page.props.url);
        },
        
        /**
         * Generate download URL for the currently selected file
         * @returns {String} Complete download URL with authentication parameters
         */
        downloadURL() {
            return (
                this.url +
                "/" +
                this.project.data.owner.username +
                "/download/" +
                this.project.data.slug +
                "?key=" +
                this.$page.props.selectedFileSystemObject.name +
                "&uuid=" +
                this.$page.props.selectedFileSystemObject.uuid
            );
        },
    },
    
    /**
     * Component lifecycle - mounted
     * Initialize the file browser with default values and load files
     */
    mounted() {
        // Set initial file system object to project files root
        this.$page.props.selectedFileSystemObject = this.project.data.files;
        
        // Set initial folder to root directory
        this.$page.props.selectedFolder = "/";
        
        // Load files in the next tick to ensure DOM is ready
        this.$nextTick(function () {
            if (this.$refs.fsbRef) {
                this.$refs.fsbRef.loadFiles();
            }
        });
    },
    
    /**
     * Component methods
     */
    methods: {
        /**
         * Truncate text in the middle with ellipsis for long file/folder names
         * 
         * This method is used in breadcrumb navigation to prevent overflow
         * while keeping both the beginning and end of the text visible.
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
    },
};
</script>
