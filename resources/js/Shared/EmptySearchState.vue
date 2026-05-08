<template>
    <div
        class="w-full min-w-0 rounded-xl border border-gray-200/90 bg-gray-50/60 px-6 py-14 text-center dark:border-gray-800 dark:bg-gray-950/40"
        role="status"
        aria-live="polite"
    >
        <svg
            class="mx-auto h-11 w-11 text-gray-400 dark:text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
        </svg>
        <h3
            class="mt-4 text-base font-semibold leading-snug tracking-tight text-gray-900 dark:text-gray-100"
        >
            {{ displayTitle }}
        </h3>
        <p
            class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-gray-600 dark:text-gray-400"
        >
            {{ displayMessage }}
        </p>
        <div v-if="showClearButton" class="mt-8">
            <button
                type="button"
                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950"
                @click="$emit('clear-search')"
            >
                Clear filters & search
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "EmptySearchState",
    props: {
        /**
         * The type of entity being searched (e.g., 'projects', 'samples', 'drafts').
         * `samples` is labelled as compounds in copy (compound library terminology).
         */
        entityType: {
            type: String,
            required: true,
        },
        /**
         * The current search query or filter summary
         */
        searchQuery: {
            type: String,
            required: true,
        },
        /**
         * Custom title to override the default
         */
        title: {
            type: String,
            default: null,
        },
        /**
         * Custom message to override the default
         */
        message: {
            type: String,
            default: null,
        },
        /**
         * Whether to show the clear button
         */
        showClearButton: {
            type: Boolean,
            default: true,
        },
    },
    emits: ["clear-search"],
    computed: {
        entityLabel() {
            const raw = String(this.entityType || "").trim().toLowerCase();
            if (!raw) {
                return "results";
            }
            const plural = ["projects", "drafts"];
            if (plural.includes(raw)) {
                return raw;
            }
            if (raw === "samples") {
                return "compounds";
            }

            return raw.endsWith("s") ? raw : `${raw}s`;
        },
        displayTitle() {
            if (this.title != null && String(this.title).trim() !== "") {
                return this.title;
            }

            return `No matching ${this.entityLabel}`;
        },
        displayMessage() {
            if (this.message != null && String(this.message).trim() !== "") {
                return this.message;
            }

            const q = String(this.searchQuery || "").trim();
            if (!q) {
                return `Adjust search keywords or filters to find ${this.entityLabel} in your workspace.`;
            }

            return `Nothing matched “${q}”. Try broader terms, or clear filters to see everything available.`;
        },
    },
};
</script>
