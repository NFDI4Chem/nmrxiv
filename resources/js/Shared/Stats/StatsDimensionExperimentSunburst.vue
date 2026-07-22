<script setup>
import { computed } from "vue";
import StatsSunburstChart from "@/Shared/Stats/StatsSunburstChart.vue";
import { EXPERIMENT_SUNBURST_COLORS } from "@/Utils/chartColors";
import { formatNmrIsotopeLabel } from "@/Utils/nmrLabels";
import {
    buildDimensionExperimentSearchPath,
    buildDimensionNucleusSearchPath,
    buildStatsLegendSearchPath,
} from "@/Utils/statsSearchLinks";

const props = defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    missing: {
        type: Number,
        default: 0,
    },
});

function segmentLabel(dimension, segment) {
    if (segment.kind === "nucleus") {
        return formatNmrIsotopeLabel(segment.value);
    }

    return segment.value;
}

function segmentSearchHref(dimension, segment) {
    if (segment.kind === "nucleus") {
        return buildDimensionNucleusSearchPath(dimension, segment.value);
    }

    return buildDimensionExperimentSearchPath(dimension, segment.value);
}

const chartGroups = computed(() =>
    props.groups.map((group) => ({
        key: group.dimension,
        label: group.dimension,
        count: group.count,
        searchHref: buildStatsLegendSearchPath("dimension", group.dimension),
        segments: (group.segments ?? []).map((segment) => ({
            key: `${segment.kind}-${segment.value}`,
            label: segmentLabel(group.dimension, segment),
            count: segment.count,
            searchHref: segmentSearchHref(group.dimension, segment),
        })),
    }))
);
</script>

<template>
    <StatsSunburstChart
        title="Experiment type"
        subtitle="Inner ring: 1D / 2D · outer ring: nucleus (1D) or experiment (2D)"
        :groups="chartGroups"
        :colors="EXPERIMENT_SUNBURST_COLORS"
        :missing="missing"
        empty-message="No dimension and experiment data available yet."
        aria-label="Dimension and experiment type sunburst chart"
    />
</template>
