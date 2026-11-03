<template>
    <div v-if="name || url">
        <h3 class="text-sm font-bold text-gray-900">License</h3>
        <a
            v-if="url"
            :href="url"
            class="mt-1 block break-all text-sm text-gray-900 underline"
            target="_blank"
            rel="noopener noreferrer"
        >
            {{ name || url }}
        </a>
        <p v-else class="mt-1 text-sm text-gray-900">{{ name }}</p>
    </div>
</template>

<script setup>
defineProps({
    name: { type: String, default: null },
    url: { type: String, default: null },
});
</script>
