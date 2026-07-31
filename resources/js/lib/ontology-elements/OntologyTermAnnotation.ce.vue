<script setup>
import { computed } from "vue";

const props = defineProps({
    annotation: {
        type: String,
        default: "",
    },
});

const term = computed(() =>
    props.annotation ? props.annotation.split("\t")[0] : ""
);
const iri = computed(() =>
    props.annotation ? props.annotation.split("\t")[2] : ""
);
const source = computed(() =>
    props.annotation ? props.annotation.split("\t")[1] : ""
);
</script>

<template>
    <a
        v-if="annotation && annotation !== ''"
        :href="iri"
        target="_blank"
        rel="noopener noreferrer"
    >
        {{ source }}: {{ term }}
    </a>
</template>
