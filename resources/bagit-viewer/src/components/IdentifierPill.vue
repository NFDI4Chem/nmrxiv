<template>
    <div v-if="value">
        <h3 class="text-sm font-bold text-gray-900">Identifier</h3>
        <div
            class="mt-2 inline-flex items-center gap-2 rounded-full bg-white py-1 pl-3 pr-2 text-sm text-gray-700 shadow-sm ring-1 ring-inset ring-gray-900/10"
        >
            <span class="break-all">{{ value }}</span>
            <CopyButton :text="value" what="identifier" />
        </div>
    </div>
</template>

<script setup>
import CopyButton from './CopyButton.vue';

defineProps({ value: { type: String, default: null } });
</script>
