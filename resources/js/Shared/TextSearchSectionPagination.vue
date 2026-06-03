<template>
    <nav
        v-if="meta.last_page > 1"
        class="mt-8 flex items-center justify-between gap-3 text-sm font-medium text-gray-700"
        :aria-label="navigationLabel"
    >
        <button
            type="button"
            class="inline-flex h-10 items-center rounded-lg border border-gray-300 bg-white px-4 hover:bg-gray-100 disabled:cursor-not-allowed disabled:border-gray-200 disabled:bg-gray-50 disabled:text-gray-400"
            :disabled="meta.current_page <= 1"
            @click="$emit('page-change', meta.current_page - 1)"
        >
            Previous
        </button>
        <span class="text-gray-600">
            Page {{ meta.current_page }} of {{ meta.last_page }}
        </span>
        <button
            type="button"
            class="inline-flex h-10 items-center rounded-lg border border-gray-300 bg-white px-4 hover:bg-gray-100 disabled:cursor-not-allowed disabled:border-gray-200 disabled:bg-gray-50 disabled:text-gray-400"
            :disabled="meta.current_page >= meta.last_page"
            @click="$emit('page-change', meta.current_page + 1)"
        >
            Next
        </button>
    </nav>
</template>

<script>
export default {
    props: {
        meta: {
            type: Object,
            required: true,
        },
        navigationLabel: {
            type: String,
            default: "Pagination",
        },
    },
    emits: ["page-change"],
};
</script>
