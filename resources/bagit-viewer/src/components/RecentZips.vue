<template>
    <section :class="compact ? '' : 'mx-auto max-w-2xl'">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-sm font-medium text-gray-500">Recent zips</h2>
            <button
                type="button"
                class="text-sm text-gray-500 hover:text-gray-800"
                @click="$emit('clear')"
            >
                Clear ({{ items.length }})
            </button>
        </div>
        <ul class="divide-y divide-gray-100">
            <li v-for="item in items" :key="item.path + item.openedAt">
                <button
                    type="button"
                    class="flex w-full items-baseline justify-between gap-6 py-2.5 text-left transition hover:bg-gray-50 disabled:opacity-60"
                    :disabled="busyPath === item.path"
                    :title="'Open ' + item.path"
                    @click="$emit('open', item)"
                >
                    <span class="truncate text-sm font-semibold text-gray-900">
                        {{ item.name }}
                    </span>
                    <span
                        class="min-w-0 truncate text-right font-mono text-xs text-gray-400"
                    >
                        {{
                            busyPath === item.path ? 'Opening…' : item.path
                        }}
                    </span>
                </button>
            </li>
        </ul>
        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    </section>
</template>

<script setup>
defineProps({
    items: { type: Array, required: true },
    compact: { type: Boolean, default: false },
    busyPath: { type: String, default: null },
    error: { type: String, default: null },
});

defineEmits(['clear', 'open']);
</script>
