<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                Bags found
                <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium tabular-nums text-gray-700">
                    {{ bags.length }}
                </span>
            </h2>
            <button
                type="button"
                class="text-sm font-medium text-teal-700 hover:text-teal-800"
                @click="$emit('clear')"
            >
                Choose other files
            </button>
        </div>
        <ul class="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white">
            <li v-for="bag in bags" :key="bag.id">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-gray-50"
                    :class="selectedId === bag.id ? 'bg-teal-50' : ''"
                    @click="$emit('select', bag)"
                >
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-gray-900">
                            {{ bag.label }}
                        </div>
                        <div class="mt-0.5 text-xs text-gray-500">
                            <span
                                class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 font-medium text-gray-600"
                            >
                                {{ bag.kind }}
                            </span>
                            <span v-if="bag.zipName" class="ml-2">{{ bag.zipName }}</span>
                            <span class="ml-2 tabular-nums">
                                {{ bag.entries.length }} files
                            </span>
                        </div>
                    </div>
                    <svg
                        class="h-4 w-4 shrink-0 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                        />
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    bags: { type: Array, required: true },
    selectedId: { type: String, default: null },
});

defineEmits(['select', 'clear']);
</script>
