<script setup>
import {
    chartSliceInteractionProps,
    visitStatsSearch,
} from "@/Utils/statsChart";

defineProps({
    slices: {
        type: Array,
        default: () => [],
    },
    center: {
        type: Number,
        required: true,
    },
    ringRadius: {
        type: Number,
        default: null,
    },
    ringStrokeWidth: {
        type: Number,
        default: null,
    },
});

function sliceRingRadius(slice, fallbackRadius) {
    return slice.ringRadius ?? fallbackRadius;
}

function sliceRingStrokeWidth(slice, fallbackStrokeWidth) {
    return slice.ringStrokeWidth ?? fallbackStrokeWidth;
}

function ariaLabelFor(slice) {
    if (slice.ariaLabel) {
        return slice.ariaLabel;
    }

    if (slice.groupLabel) {
        return `Search ${slice.groupLabel}: ${slice.label}`;
    }

    return `Search spectra: ${slice.displayLabel ?? slice.label}`;
}
</script>

<template>
    <template v-for="slice in slices" :key="slice.id ?? slice.value">
        <circle
            v-if="slice.isFullRing"
            :cx="center"
            :cy="center"
            :r="sliceRingRadius(slice, ringRadius)"
            fill="none"
            :stroke="slice.color"
            :stroke-width="sliceRingStrokeWidth(slice, ringStrokeWidth)"
            v-bind="
                chartSliceInteractionProps(
                    slice.searchHref,
                    ariaLabelFor(slice)
                )
            "
            @click="visitStatsSearch(slice.searchHref)"
            @keydown.enter.prevent="visitStatsSearch(slice.searchHref)"
            @keydown.space.prevent="visitStatsSearch(slice.searchHref)"
        />
        <path
            v-else
            :d="slice.path"
            :fill="slice.color"
            v-bind="
                chartSliceInteractionProps(
                    slice.searchHref,
                    ariaLabelFor(slice)
                )
            "
            @click="visitStatsSearch(slice.searchHref)"
            @keydown.enter.prevent="visitStatsSearch(slice.searchHref)"
            @keydown.space.prevent="visitStatsSearch(slice.searchHref)"
        />
    </template>
</template>
