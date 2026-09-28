<template>
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                    :class="badgeClass"
                >
                    {{ badgeLabel }}
                </span>
                <span v-if="algorithm" class="text-xs text-gray-500">
                    {{ algorithm }}
                </span>
            </div>
            <button
                v-if="result && result.files.length"
                type="button"
                class="text-xs font-medium text-teal-700 hover:text-teal-800"
                @click="showDetails = !showDetails"
            >
                {{ showDetails ? 'Hide details' : 'Show details' }}
            </button>
        </div>

        <div v-if="running" class="mt-3">
            <div class="mb-1 flex justify-between text-xs text-gray-500">
                <span class="truncate pr-2">{{ progress.path || 'Validating…' }}</span>
                <span class="tabular-nums shrink-0">
                    {{ progress.current }}/{{ progress.total || '?' }}
                </span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                <div
                    class="h-full rounded-full bg-teal-500 transition-all"
                    :style="{ width: percent + '%' }"
                ></div>
            </div>
        </div>

        <ul v-if="result?.errors?.length" class="mt-3 space-y-1 text-sm text-red-700">
            <li v-for="(err, i) in result.errors" :key="'e' + i">{{ err }}</li>
        </ul>
        <ul
            v-if="result?.warnings?.length"
            class="mt-2 space-y-1 text-sm text-amber-700"
        >
            <li v-for="(w, i) in result.warnings" :key="'w' + i">{{ w }}</li>
        </ul>

        <div v-if="showDetails && result" class="mt-3 max-h-48 overflow-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="sticky top-0 bg-white text-gray-500">
                    <tr>
                        <th class="py-1 pr-2 font-medium">Status</th>
                        <th class="py-1 font-medium">Path</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="file in result.files"
                        :key="file.path + file.status"
                        class="border-t border-gray-50"
                    >
                        <td class="py-1 pr-2">
                            <span :class="statusClass(file.status)">
                                {{ file.status }}
                            </span>
                        </td>
                        <td class="py-1 font-mono text-gray-700 break-all">
                            {{ file.path }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    running: { type: Boolean, default: false },
    progress: {
        type: Object,
        default: () => ({ current: 0, total: 0, path: '' }),
    },
    result: { type: Object, default: null },
    algorithm: { type: String, default: null },
});

const showDetails = ref(false);

const percent = computed(() => {
    if (!props.progress?.total) {
        return props.running ? 5 : 0;
    }

    return Math.min(
        100,
        Math.round((props.progress.current / props.progress.total) * 100),
    );
});

const badgeLabel = computed(() => {
    if (props.running) {
        return 'Validating checksums…';
    }
    if (!props.result) {
        return 'Not validated';
    }

    return props.result.valid ? 'BagIt valid' : 'BagIt invalid';
});

const badgeClass = computed(() => {
    if (props.running) {
        return 'bg-blue-50 text-blue-800';
    }
    if (!props.result) {
        return 'bg-gray-100 text-gray-600';
    }

    return props.result.valid
        ? 'bg-green-50 text-green-800'
        : 'bg-red-50 text-red-800';
});

function statusClass(status) {
    switch (status) {
        case 'pass':
            return 'text-green-700';
        case 'fail':
            return 'text-red-700 font-semibold';
        case 'missing':
            return 'text-red-700';
        case 'extra':
            return 'text-amber-700';
        default:
            return 'text-gray-600';
    }
}
</script>
