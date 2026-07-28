<template>
    <!-- Main container with modern card styling -->
    <div class="bg-white rounded-lg border border-gray-200">
        <!-- Header section with file name and status -->
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex items-start space-x-3">
                <!-- Missing file warning indicator -->
                <span v-if="file.status == 'missing'" class="flex-shrink-0">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100"
                    >
                        <svg
                            class="h-5 w-5 text-red-600"
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

                <!-- Integrity status badge -->
                <span v-else-if="file.integrity_status" class="flex-shrink-0">
                    <span
                        v-if="file.integrity_status === 'verified'"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100"
                    >
                        <svg
                            class="h-5 w-5 text-green-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </span>
                    <span
                        v-else-if="file.integrity_status === 'failed'"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100"
                    >
                        <svg
                            class="h-5 w-5 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </span>
                    <span
                        v-else
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100"
                    >
                        <svg
                            class="h-5 w-5 text-yellow-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </span>
                </span>

                <div class="flex-1 min-w-0">
                    <h3
                        class="text-base font-semibold text-gray-900 truncate"
                        :title="file.name"
                    >
                        {{ file.name }}
                    </h3>
                    <p class="text-sm text-gray-500">File information</p>
                </div>
            </div>
        </div>

        <!-- File details section with structured data -->
        <div class="px-5 py-4 space-y-4">
            <!-- Basic file properties -->
            <dl class="grid grid-cols-1 gap-3">
                <!-- Upload timestamp -->
                <div class="flex justify-between items-center">
                    <dt
                        class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Uploaded
                    </dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        {{ formatDateTime(file.created_at) }}
                    </dd>
                </div>

                <!-- Last modified -->
                <div
                    v-if="
                        file.updated_at && file.updated_at !== file.created_at
                    "
                    class="flex justify-between items-center"
                >
                    <dt
                        class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Modified
                    </dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        {{ formatDateTime(file.updated_at) }}
                    </dd>
                </div>

                <!-- File size -->
                <div class="flex justify-between items-center">
                    <dt
                        class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Size
                    </dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        {{ formatFileSize() }}
                    </dd>
                </div>

                <!-- File type/extension -->
                <div
                    v-if="fileExtension"
                    class="flex justify-between items-center"
                >
                    <dt
                        class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Type
                    </dt>
                    <dd class="text-sm text-gray-900 font-medium">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                        >
                            {{ fileExtension.toUpperCase() }}
                        </span>
                    </dd>
                </div>
            </dl>

            <!-- Integrity section (if available) -->
            <div
                v-if="
                    file.integrity_status ||
                    file.checksum_sha256 ||
                    file.checksum_md5
                "
                class="pt-3 border-t border-gray-100"
            >
                <h4
                    class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3"
                >
                    Integrity
                </h4>
                <dl class="space-y-3">
                    <!-- Integrity status -->
                    <div
                        v-if="file.integrity_status"
                        class="flex justify-between items-center"
                    >
                        <dt
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Status
                        </dt>
                        <dd>
                            <span
                                :class="[
                                    file.integrity_status === 'verified'
                                        ? 'bg-green-100 text-green-800'
                                        : file.integrity_status === 'failed'
                                        ? 'bg-red-100 text-red-800'
                                        : 'bg-yellow-100 text-yellow-800',
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                ]"
                            >
                                {{
                                    formatIntegrityStatus(file.integrity_status)
                                }}
                            </span>
                        </dd>
                    </div>

                    <!-- Verification time -->
                    <div
                        v-if="file.integrity_verified_at"
                        class="flex justify-between items-center"
                    >
                        <dt
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Verified
                        </dt>
                        <dd class="text-sm text-gray-900 font-medium">
                            {{ formatDateTime(file.integrity_verified_at) }}
                        </dd>
                    </div>

                    <!-- SHA256 checksum -->
                    <div v-if="file.checksum_sha256">
                        <dt
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5"
                        >
                            SHA-256
                        </dt>
                        <dd
                            class="text-xs font-mono text-gray-600 bg-gray-50 px-3 py-2 rounded border border-gray-200 break-all"
                        >
                            {{ file.checksum_sha256 }}
                        </dd>
                    </div>

                    <!-- MD5 checksum -->
                    <div v-if="file.checksum_md5">
                        <dt
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5"
                        >
                            MD5
                        </dt>
                        <dd
                            class="text-xs font-mono text-gray-600 bg-gray-50 px-3 py-2 rounded border border-gray-200 break-all"
                        >
                            {{ file.checksum_md5 }}
                        </dd>
                    </div>

                    <!-- Integrity error (if failed) -->
                    <div
                        v-if="
                            file.integrity_status === 'failed' &&
                            file.integrity_error
                        "
                        class="mt-2"
                    >
                        <div
                            class="bg-red-50 border border-red-200 rounded-md p-3"
                        >
                            <p class="text-xs text-red-800">
                                {{ file.integrity_error }}
                            </p>
                        </div>
                    </div>
                </dl>
            </div>

            <!-- Legacy ETag (if available and no checksums) -->
            <div
                v-if="
                    fileInfo.ETag && !file.checksum_sha256 && !file.checksum_md5
                "
                class="pt-3 border-t border-gray-100"
            >
                <dl class="space-y-3">
                    <div>
                        <dt
                            class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5"
                        >
                            ETag
                        </dt>
                        <dd
                            class="text-xs font-mono text-gray-600 bg-gray-50 px-3 py-2 rounded border border-gray-200 break-all"
                        >
                            {{ fileInfo.ETag.replace(/"/g, "") }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Download section -->
            <div v-if="downloadURL" class="pt-3 border-t border-gray-100">
                <button
                    type="button"
                    class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors"
                    @click="
                        requestDownload(downloadURL, downloadTrackingIdentifier)
                    "
                >
                    <svg
                        class="h-4 w-4 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"
                        />
                    </svg>
                    Download File
                </button>
            </div>
        </div>

        <DownloadTermsModal
            :show="showDownloadTerms"
            :download-url="pendingDownloadUrl"
            :download-identifier="pendingDownloadIdentifier"
            :license-title="project?.license?.title || study?.license?.title"
            @close="closeDownloadTerms"
        />
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

import DownloadTermsModal from "@/Shared/DownloadTermsModal.vue";
import DownloadTerms from "@/Mixins/DownloadTerms.js";

export default {
    name: "FileDetails",

    /**
     * Component dependencies
     */
    components: {
        DownloadTermsModal,
    },

    mixins: [DownloadTerms],

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

        downloadTrackingIdentifier() {
            if (this.project) {
                return this.trackingIdentifier(this.project);
            }

            if (this.study) {
                return this.trackingIdentifier(this.study);
            }

            return null;
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
            if (this.file.info) {
                try {
                    return JSON.parse(this.file.info);
                } catch (e) {
                    return {};
                }
            }
            return {};
        },

        /**
         * Extract file extension from filename
         *
         * @returns {String|null} File extension without dot, or null if no extension
         */
        fileExtension() {
            if (!this.file.name) return null;
            const parts = this.file.name.split(".");
            if (parts.length > 1) {
                return parts.pop().toLowerCase();
            }
            return null;
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
            if (bytes === 0) return "0 Bytes";

            const k = 1024;
            const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return (
                parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
            );
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
            if (!dateString) return "-";

            const date = new Date(dateString);
            const options = {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                hour12: true,
            };

            return date
                .toLocaleDateString("en-US", options)
                .replace(",", " at");
        },

        /**
         * Format file size for display
         *
         * Uses file_size from the model if available, otherwise falls back
         * to the info.ContentLength property.
         *
         * @returns {String} Formatted file size string
         */
        formatFileSize() {
            // Prefer the file_size attribute from the model
            if (this.file.file_size) {
                return this.bytesToSize(this.file.file_size);
            }
            // Fall back to ContentLength from info JSON
            if (this.fileInfo.ContentLength) {
                return this.bytesToSize(this.fileInfo.ContentLength);
            }
            return "-";
        },

        /**
         * Format integrity status for display
         *
         * Converts internal status values to human-readable labels.
         *
         * @param {String} status - Internal integrity status value
         * @returns {String} Human-readable status label
         */
        formatIntegrityStatus(status) {
            const statusMap = {
                verified: "Verified",
                failed: "Failed",
                pending: "Pending",
                not_verified: "Not Verified",
            };
            return statusMap[status] || status;
        },
    },
};
</script>
