<script setup>
import { computed } from "vue";
import StatsSunburstChart from "@/Shared/Stats/StatsSunburstChart.vue";
import { FREQUENCY_SUNBURST_COLORS } from "@/Utils/chartColors";
import {
    formatMeasuringFrequencyLabel,
    formatNmrIsotopeLabel,
} from "@/Utils/nmrLabels";
import {
    buildNucleusFrequencySearchPath,
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

const chartGroups = computed(() =>
    props.groups.map((group) => ({
        key: group.nucleus,
        label: formatNmrIsotopeLabel(group.nucleus),
        count: group.count,
        searchHref: buildStatsLegendSearchPath("nucleus", group.nucleus),
        segments: (group.frequencies ?? []).map((frequencyRow) => ({
            key: frequencyRow.value,
            label: formatMeasuringFrequencyLabel(frequencyRow.value),
            count: frequencyRow.count,
            searchHref: buildNucleusFrequencySearchPath(
                group.nucleus,
                frequencyRow.value
            ),
        })),
    }))
);
</script>

<template>
    <StatsSunburstChart
        title="Measuring frequency"
        subtitle="Inner ring: nucleus · outer ring: observed frequency"
        :groups="chartGroups"
        :colors="FREQUENCY_SUNBURST_COLORS"
        :missing="missing"
        empty-message="No nucleus and frequency data available yet."
        aria-label="Nucleus and measuring frequency sunburst chart"
    />
</template>
