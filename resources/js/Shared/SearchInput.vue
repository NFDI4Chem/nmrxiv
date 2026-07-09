<template>
    <div class="flex w-full min-w-0 items-center">
        <div
            :class="[
                'flex w-full min-w-0 bg-white shadow ring-1 ring-gray-200',
                roundedFull ? 'rounded-full' : 'rounded-lg',
            ]"
        >
            <input
                :id="inputId || undefined"
                :class="[
                    'relative w-full border-0 px-4 py-2.5 focus:shadow-outline sm:px-5 sm:py-3',
                    roundedFull ? 'rounded-full' : 'rounded-lg',
                ]"
                autocomplete="off"
                type="text"
                :name="name"
                :placeholder="placeholder"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </div>
        <button
            v-if="showClear"
            class="-mr-1 ml-2 inline-flex shrink-0 rounded-full p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:hover:bg-gray-700 dark:hover:text-gray-200"
            type="button"
            :aria-label="clearAriaLabel"
            @click="$emit('reset')"
        >
            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
        </button>
    </div>
</template>

<script>
import { XMarkIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        XMarkIcon,
    },

    props: {
        modelValue: {
            type: String,
            default: "",
        },
        placeholder: {
            type: String,
            default: "Search...",
        },
        name: {
            type: String,
            default: "search",
        },
        inputId: {
            type: String,
            default: null,
        },
        maxWidth: {
            type: Number,
            default: 300,
        },
        /** When true (e.g. status filter active), show clear even if the input is empty. */
        filtersActive: {
            type: Boolean,
            default: false,
        },
        roundedFull: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        showClear() {
            const q =
                this.modelValue != null ? String(this.modelValue).trim() : "";

            return q.length > 0 || this.filtersActive;
        },
        clearAriaLabel() {
            return this.filtersActive
                ? "Clear search and filters"
                : "Clear search";
        },
    },
};
</script>
