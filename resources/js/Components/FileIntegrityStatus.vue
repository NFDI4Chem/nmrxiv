<template>
    <div class="inline-flex items-center space-x-1">
        <!-- Verified status -->
        <div
            v-if="status === 'verified'"
            class="flex items-center"
            :title="tooltipText"
        >
            <CheckCircleIcon class="h-4 w-4 text-green-500" />
            <span v-if="showText" class="text-xs text-green-600 ml-1"
                >Verified</span
            >
        </div>

        <!-- Pending status -->
        <div
            v-else-if="status === 'pending'"
            class="flex items-center"
            :title="tooltipText"
        >
            <ClockIcon class="h-4 w-4 text-yellow-500" />
            <span v-if="showText" class="text-xs text-yellow-600 ml-1"
                >Pending</span
            >
        </div>

        <!-- Failed status -->
        <div
            v-else-if="status === 'failed'"
            class="flex items-center"
            :title="tooltipText"
        >
            <XCircleIcon class="h-4 w-4 text-red-500" />
            <span v-if="showText" class="text-xs text-red-600 ml-1"
                >Failed</span
            >
        </div>

        <!-- Skipped status -->
        <div
            v-else-if="status === 'skipped'"
            class="flex items-center"
            :title="tooltipText"
        >
            <MinusCircleIcon class="h-4 w-4 text-gray-400" />
            <span v-if="showText" class="text-xs text-gray-500 ml-1"
                >Skipped</span
            >
        </div>

        <!-- No status / directory -->
        <div v-else class="opacity-0 h-4 w-4">
            <!-- Placeholder to maintain layout -->
        </div>

        <!-- Checksum info tooltip -->
        <div
            v-if="showChecksum && checksums"
            class="text-xs text-gray-400 cursor-help"
            :title="checksumTooltip"
        >
            <span class="font-mono"
                >{{ primaryChecksum.substring(0, 8) }}...</span
            >
        </div>
    </div>
</template>

<script>
import {
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    MinusCircleIcon,
} from "@heroicons/vue/24/solid";

export default {
    name: "FileIntegrityStatus",

    components: {
        CheckCircleIcon,
        XCircleIcon,
        ClockIcon,
        MinusCircleIcon,
    },

    props: {
        /**
         * The integrity status of the file
         */
        status: {
            type: String,
            default: null,
            validator: (value) =>
                ["pending", "verified", "failed", "skipped", null].includes(
                    value
                ),
        },

        /**
         * File checksums object
         */
        checksums: {
            type: Object,
            default: null,
        },

        /**
         * Primary checksum algorithm used
         */
        algorithm: {
            type: String,
            default: "sha256",
        },

        /**
         * Whether to show status text alongside icon
         */
        showText: {
            type: Boolean,
            default: false,
        },

        /**
         * Whether to show checksum preview
         */
        showChecksum: {
            type: Boolean,
            default: false,
        },

        /**
         * Error message if verification failed
         */
        error: {
            type: String,
            default: null,
        },

        /**
         * When verification was completed
         */
        verifiedAt: {
            type: String,
            default: null,
        },

        /**
         * File name for context in tooltips
         */
        fileName: {
            type: String,
            default: "File",
        },
    },

    computed: {
        /**
         * Generate tooltip text based on status
         */
        tooltipText() {
            const fileName = this.fileName;

            switch (this.status) {
                case "verified":
                    const verifiedDate = this.verifiedAt
                        ? new Date(this.verifiedAt).toLocaleString()
                        : "recently";
                    return `${fileName} integrity verified ${verifiedDate}`;

                case "pending":
                    return `${fileName} integrity verification in progress`;

                case "failed":
                    const errorMsg = this.error ? ` (${this.error})` : "";
                    return `${fileName} integrity verification failed${errorMsg}`;

                case "skipped":
                    return `${fileName} integrity verification was skipped`;

                default:
                    return null;
            }
        },

        /**
         * Get the primary checksum value
         */
        primaryChecksum() {
            if (!this.checksums) return "";

            if (this.algorithm === "sha256" && this.checksums.sha256) {
                return this.checksums.sha256;
            } else if (this.algorithm === "md5" && this.checksums.md5) {
                return this.checksums.md5;
            } else if (this.checksums.sha256) {
                return this.checksums.sha256;
            } else if (this.checksums.md5) {
                return this.checksums.md5;
            }

            return "";
        },

        /**
         * Generate checksum tooltip
         */
        checksumTooltip() {
            if (!this.checksums) return "";

            const lines = [];

            if (this.checksums.md5) {
                lines.push(`MD5: ${this.checksums.md5}`);
            }

            if (this.checksums.sha256) {
                lines.push(`SHA-256: ${this.checksums.sha256}`);
            }

            if (this.checksums.sha1) {
                lines.push(`SHA-1: ${this.checksums.sha1}`);
            }

            return lines.join("\n");
        },

        /**
         * Determine if file has integrity issues that need attention
         */
        needsAttention() {
            return this.status === "failed";
        },

        /**
         * Get CSS classes for styling
         */
        statusClasses() {
            return {
                "text-green-600": this.status === "verified",
                "text-yellow-600": this.status === "pending",
                "text-red-600": this.status === "failed",
                "text-gray-500": this.status === "skipped",
            };
        },
    },

    methods: {
        /**
         * Format file size in human readable format
         */
        formatFileSize(bytes) {
            if (bytes === 0) return "0 Bytes";

            const k = 1024;
            const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return (
                parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
            );
        },
    },
};
</script>

<style scoped>
/* Add any custom styles here */
.cursor-help {
    cursor: help;
}
</style>
