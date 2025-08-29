<!--
  File Details Component
  
  Displays detailed information about a selected file including metadata,
  upload timestamp, file size, ETag, and download functionality. Used in
  file browser interfaces to show file properties in a structured format.
-->
<template>
    <!-- Main container with shadow and rounded corners -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Header section with file name and status indicator -->
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 text-xl font-bold text-gray-900">
                <!-- Missing file warning indicator -->
                <span
                    v-if="file.status == 'missing'"
                    class="float-left inline pr-4 pt-1 text-sm font-medium pointer-events-none"
                >
                    <!-- Red warning icon for missing files -->
                    <span
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
                    >
                        <svg
                            class="h-6 w-6 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                            />
                        </svg>
                    </span>
                </span>
                <!-- File name display -->
                {{ file.name }}
            </h3>
            <!-- Subtitle describing the content -->
            <p class="mt-1 max-w-2xl text-sm text-gray-500">File information</p>
        </div>
        
        <!-- File details section with structured data -->
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <!-- Definition list for file metadata -->
            <dl class="sm:divide-y sm:divide-gray-200">
                <!-- File metadata grid -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <!-- Upload timestamp -->
                    <dt class="text-sm font-medium text-gray-500">
                        Uploaded at
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ formatDateTime(file.created_at) }}
                    </dd>
                    
                    <!-- File size information -->
                    <dt class="text-sm font-medium text-gray-500">File size</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ bytesToSize(fileInfo.size) }}
                    </dd>
                    
                    <!-- ETag for file integrity verification -->
                    <dt class="text-sm font-medium text-gray-500">ETag</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <!-- Display ETag without quotes if available -->
                        <div v-if="fileInfo.ETag">
                            {{ fileInfo.ETag.replace(/"/g, "") }}
                        </div>
                        <!-- Placeholder when ETag is not available -->
                        <div v-else>-</div>
                    </dd>
                </div>
                
                <!-- Spacer element -->
                <div></div>
                
                <!-- File content and download section -->
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Content</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <!-- File attachment list -->
                        <ul
                            role="list"
                            class="border border-gray-200 rounded-md divide-y divide-gray-200"
                        >
                            <!-- Single file item with download option -->
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <!-- File icon and name -->
                                <div class="w-0 flex-1 flex items-center">
                                    <!-- Paper clip icon indicating attachment -->
                                    <svg
                                        class="flex-shrink-0 h-5 w-5 text-gray-400"
                                        x-description="Heroicon name: solid/paper-clip"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <!-- Truncated file name -->
                                    <span class="ml-2 flex-1 w-0 truncate">
                                        {{ file.name }}
                                    </span>
                                </div>
                                
                                <!-- Download link (only shown if download URL is available) -->
                                <div
                                    v-if="downloadURL"
                                    class="ml-4 flex-shrink-0"
                                >
                                    <a
                                        :href="downloadURL"
                                        class="font-medium text-indigo-600 hover:text-indigo-500"
                                    >
                                        Download
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>

<script>
/**
 * File Details Component
 * 
 * This component renders detailed information about a selected file in a structured
 * format. It displays file metadata including upload timestamp, file size, ETag for
 * integrity verification, and provides download functionality when available.
 * 
 * Key Features:
 * - File metadata display (name, size, upload date, ETag)
 * - Missing file status indication with warning icon
 * - Download link generation for public and study files
 * - Responsive design with grid layout
 * - File size formatting utilities
 * - Date/time formatting for upload timestamps
 */

// Icon imports from Heroicons
import { PaperClipIcon } from "@heroicons/vue/24/solid";

export default {
    name: "FileDetails",
    
    /**
     * Component dependencies
     */
    components: {
        PaperClipIcon,      // Paper clip icon for file attachments (unused but imported)
    },
    
    /**
     * Component props
     * @prop {Object} file - File object containing metadata and properties
     * @prop {Object} project - Project object for download URL generation (optional)
     * @prop {Object} study - Study object for download URL generation (optional)
     */
    props: ["file", "project", "study"],
    
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
    computed: {
        /**
         * Generate download URL for the file
         * 
         * Creates a download URL based on the project or study context.
         * Handles both direct project files and study-related files.
         * 
         * @returns {String|undefined} Complete download URL or undefined if not available
         */
        downloadURL() {
            // Check if we have project or study context for download
            if (this.study || this.project) {
                let project = null;
                
                // Determine which project to use for URL generation
                if (this.study && !this.project) {
                    // Use project from study if only study is provided
                    project = this.study.project;
                } else {
                    // Use direct project reference
                    project = this.project;
                }
                
                // Generate download URL if project is available
                if (project) {
                    return (
                        this.url +
                        "/" +
                        project.owner.username +
                        "/download/" +
                        project.slug +
                        "?key=" +
                        this.file.key +
                        "&uuid=" +
                        this.file.uuid
                    );
                }
            }
            
            // Return undefined if no download context is available
            return undefined;
        },
        
        /**
         * Get the base URL from page props
         * @returns {String} Base application URL
         */
        url() {
            return String(this.$page.props.url);
        },
        
        /**
         * Parse file information from JSON string
         * 
         * The file.info property contains JSON-encoded metadata about the file
         * including size, ETag, and other storage-related information.
         * 
         * @returns {Object} Parsed file information object
         */
        fileInfo() {
            return JSON.parse(this.file.info);
        },
    },
    
    /**
     * Component methods
     */
    methods: {
        /**
         * Format file size from bytes to human-readable format
         * 
         * Converts byte values to appropriate units (B, KB, MB, GB, TB)
         * with proper decimal places for readability.
         * 
         * @param {Number} bytes - File size in bytes
         * @returns {String} Formatted file size string
         * 
         * @example
         * bytesToSize(1024) // Returns: "1.00 KB"
         * bytesToSize(1048576) // Returns: "1.00 MB"
         */
        bytesToSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        /**
         * Format date and time for display
         * 
         * Converts ISO date string to a human-readable format
         * suitable for displaying upload timestamps.
         * 
         * @param {String} dateString - ISO date string
         * @returns {String} Formatted date and time string
         * 
         * @example
         * formatDateTime("2023-12-01T10:30:00Z") // Returns: "Dec 1, 2023 at 10:30 AM"
         */
        formatDateTime(dateString) {
            if (!dateString) return '-';
            
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            
            return date.toLocaleDateString('en-US', options).replace(',', ' at');
        },
    },
};
</script>