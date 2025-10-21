<template>
    <div class="text-center py-12">
        <svg
            class="mx-auto h-12 w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">
            {{ title || `No ${entityType} found` }}
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            {{
                message ||
                `No ${entityType} match your search criteria "${searchQuery}". Try adjusting your search terms.`
            }}
        </p>
        <div v-if="showClearButton" class="mt-6">
            <button
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                @click="$emit('clear-search')"
            >
                Clear search
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "EmptySearchState",
    props: {
        /**
         * The type of entity being searched (e.g., 'projects', 'samples', 'drafts')
         */
        entityType: {
            type: String,
            required: true,
        },
        /**
         * The current search query
         */
        searchQuery: {
            type: String,
            required: true,
        },
        /**
         * Custom title to override the default "No {entityType} found"
         */
        title: {
            type: String,
            default: null,
        },
        /**
         * Custom message to override the default search message
         */
        message: {
            type: String,
            default: null,
        },
        /**
         * Whether to show the "Clear search" button
         */
        showClearButton: {
            type: Boolean,
            default: true,
        },
    },
    emits: ["clear-search"],
};
</script>
