<template>
    <div
        class="relative overflow-hidden"
        :class="containerClass"
        :style="{ background: cover.baseGradient }"
        aria-hidden="true"
    >
        <div
            v-for="(blob, index) in cover.blobs"
            :key="index"
            class="absolute rounded-full blur-2xl"
            :style="{
                left: blob.left,
                top: blob.top,
                width: blob.width,
                height: blob.height,
                background: blob.background,
                opacity: blob.opacity,
            }"
        />
        <div
            class="absolute inset-0 opacity-60 mix-blend-overlay"
            :style="{
                backgroundImage: cover.overlay,
                backgroundSize: cover.overlaySize,
            }"
        />
        <div
            class="absolute inset-0 bg-gradient-to-br from-white/25 via-transparent to-black/5"
        />
    </div>
</template>

<script>
import { computed } from "vue";
import { seededCoverStyle } from "@/Utils/seededCoverStyle.js";

export default {
    name: "SeededCoverBackground",

    props: {
        seed: {
            type: [String, Number, Object],
            default: null,
        },
        containerClass: {
            type: [String, Array, Object],
            default: "",
        },
    },

    setup(props) {
        const cover = computed(() => seededCoverStyle(props.seed));

        return { cover };
    },
};
</script>
