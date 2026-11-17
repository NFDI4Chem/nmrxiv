<template>
    <button
        type="button"
        :class="[
            'inline-flex shrink-0 items-center gap-1 rounded-md text-xs font-medium transition-colors',
            label ? 'px-2 py-1' : 'p-1',
            copied
                ? 'text-emerald-600'
                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700',
        ]"
        :title="copied ? 'Copied!' : `Copy ${what}`"
        :aria-label="
            copied ? `${what} copied to clipboard` : `Copy ${what} to clipboard`
        "
        @click="copy"
    >
        <svg
            v-if="!copied"
            class="h-3.5 w-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
        </svg>
        <svg
            v-else
            class="h-3.5 w-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
            />
        </svg>
        <span v-if="label">{{ copied ? 'Copied' : label }}</span>
    </button>
</template>

<script setup>
import { onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    text: { type: String, required: true },
    /** Visible text next to the icon; icon-only when empty. */
    label: { type: String, default: '' },
    /** What is being copied, for the tooltip / aria-label. */
    what: { type: String, default: 'text' },
});

const copied = ref(false);
let timer = null;

async function copy() {
    try {
        await navigator.clipboard.writeText(props.text);
        copied.value = true;
        clearTimeout(timer);
        timer = setTimeout(() => (copied.value = false), 2000);
    } catch {
        // Clipboard may be blocked on file://; the text stays selectable.
    }
}

onBeforeUnmount(() => clearTimeout(timer));
</script>
