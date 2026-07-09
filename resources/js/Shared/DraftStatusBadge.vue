<script>
const STATUS_LABELS = {
    draft: "Draft",
    received: "Received",
    zip_processed: "ZIP Processed",
    validated: "Validated",
    processed: "Processed",
    successful: "Successful",
    published: "Published",
    failed: "Failed",
    processing: "Processing",
    pending: "Pending",
    job_dispatched: "Job Dispatched",
    queued: "Queued",
    complete: "Complete",
    embargo: "Embargo",
    archived: "Archived",
    deleted: "Deleted",
};

const STATUS_COLOR_GROUPS = {
    blue: ["received"],
    yellow: [
        "zip_processed",
        "processing",
        "pending",
        "job_dispatched",
        "queued",
    ],
    green: [
        "validated",
        "processed",
        "successful",
        "published",
        "complete",
        "embargo",
    ],
    red: ["failed"],
    gray: ["draft", "archived", "deleted"],
};

const COLOR_CLASSES = {
    blue: "bg-blue-100 text-blue-800",
    yellow: "bg-yellow-100 text-yellow-800",
    green: "bg-green-100 text-green-800",
    red: "bg-red-100 text-red-800",
    gray: "bg-gray-100 text-gray-800",
};

export default {
    name: "DraftStatusBadge",
    props: {
        // Pass the whole draft (preferred). The component will display the
        // associated project's status when available, falling back to the
        // draft's own status. This keeps status display uniform across views.
        draft: {
            type: Object,
            default: null,
        },
        // Optional override when a draft is not available.
        status: {
            type: String,
            default: null,
        },
    },
    computed: {
        resolvedStatus() {
            const projectStatus = this.draft?.project?.status;
            if (projectStatus) {
                return projectStatus;
            }

            if (this.draft?.status) {
                return this.draft.status;
            }

            return this.status;
        },
        normalizedStatus() {
            return this.resolvedStatus
                ? String(this.resolvedStatus).toLowerCase()
                : null;
        },
        colorClass() {
            if (!this.normalizedStatus) {
                return COLOR_CLASSES.gray;
            }

            const group = Object.keys(STATUS_COLOR_GROUPS).find((color) =>
                STATUS_COLOR_GROUPS[color].includes(this.normalizedStatus)
            );

            return COLOR_CLASSES[group] || COLOR_CLASSES.gray;
        },
        label() {
            if (!this.resolvedStatus) {
                return "";
            }

            const key = this.normalizedStatus;
            if (STATUS_LABELS[key]) {
                return STATUS_LABELS[key];
            }

            return String(this.resolvedStatus)
                .replace(/_/g, " ")
                .replace(/\b\w/g, (l) => l.toUpperCase());
        },
    },
};
</script>

<template>
    <span
        v-if="resolvedStatus"
        :title="label"
        class="inline-flex w-fit shrink-0 items-center justify-center whitespace-nowrap rounded-full px-3 py-3 text-center text-xs font-medium leading-none"
        :class="colorClass"
    >
        {{ label }}
    </span>
</template>
