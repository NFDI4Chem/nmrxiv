<template>
    <div
        v-if="text"
        class="w-full min-w-0 rounded-lg border border-gray-200 bg-gray-50/60 p-3.5"
    >
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-gray-900">{{ heading }}</h3>
            <CopyButton :text="text" label="Copy" what="citation" />
        </div>
        <select
            v-model="format"
            class="mt-2 block w-full min-w-0 rounded-md border-0 bg-white py-1.5 pl-2 pr-7 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-teal-500"
            aria-label="Citation format"
        >
            <option
                v-for="style in CITATION_STYLES"
                :key="style.key"
                :value="style.key"
            >
                {{ style.label }}
            </option>
        </select>
        <p
            class="mt-3 max-h-[min(18rem,45vh)] overflow-y-auto break-words text-xs leading-relaxed text-gray-600"
        >
            {{ text }}
        </p>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import CopyButton from './CopyButton.vue';
import { CITATION_STYLES, formatCitation } from '../bag/citeStyles.js';

const props = defineProps({
    heading: { type: String, default: 'Cite this sample' },
    title: { type: String, default: '' },
    authors: { type: Array, default: () => [] },
    doi: { type: String, default: null },
    url: { type: String, default: null },
    date: { type: String, default: null },
    publisher: { type: String, default: null },
});

const format = ref('APA');
const text = computed(() => {
    if (!props.title || !props.authors.length) {
        return '';
    }

    try {
        return formatCitation(format.value, {
            title: props.title,
            authors: props.authors,
            doi: props.doi && /^10\./.test(props.doi) ? props.doi : null,
            url: props.url,
            date: props.date,
            publisher: props.publisher,
        });
    } catch {
        return '';
    }
});
</script>
