<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import StatsDonutSlices from "@/Shared/Stats/StatsDonutSlices.vue";
import { formatDistributionLabel } from "@/Utils/nmrLabels";
import { chartColorForIndex } from "@/Utils/chartColors";
import {
    describeDonutSlice,
    donutRingRadius,
    donutRingStrokeWidth,
    isFullCircleSpan,
} from "@/Utils/chartGeometry";
import { buildStatsLegendSearchPath } from "@/Utils/statsSearchLinks";
import { formatStatsNumber, formatStatsPercent } from "@/Utils/statsChart";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    filterKey: {
        type: String,
        default: "",
    },
    rows: {
        type: Array,
        default: () => [],
    },
    missing: {
        type: Number,
        default: 0,
    },
    unit: {
        type: String,
        default: "spectra",
    },
    maxSlices: {
        type: Number,
        default: 10,
    },
    labelFormat: {
        type: String,
        default: "default",
    },
});

const size = 220;
const center = size / 2;
const outerRadius = 96;
const innerRadius = 52;
const ringRadius = donutRingRadius(innerRadius, outerRadius);
const ringStrokeWidth = donutRingStrokeWidth(innerRadius, outerRadius);

const totalCounted = computed(() =>
    props.rows.reduce((sum, row) => sum + row.count, 0)
);

const displayRows = computed(() => {
    const sorted = [...props.rows].sort((a, b) => b.count - a.count);

    if (sorted.length <= props.maxSlices) {
        return sorted;
    }

    const visible = sorted.slice(0, props.maxSlices - 1);
    const otherCount = sorted
        .slice(props.maxSlices - 1)
        .reduce((sum, row) => sum + row.count, 0);

    if (otherCount === 0) {
        return visible;
    }

    return [...visible, { value: "Other", count: otherCount }];
});

const slices = computed(() => {
    if (!totalCounted.value || !displayRows.value.length) {
        return [];
    }

    let cumulative = 0;

    return displayRows.value.map((row, index) => {
        const fraction = row.count / totalCounted.value;
        const startAngle = cumulative * Math.PI * 2 - Math.PI / 2;
        cumulative += fraction;
        const endAngle = cumulative * Math.PI * 2 - Math.PI / 2;
        const displayLabel = formatDistributionLabel(
            row.value,
            props.labelFormat
        );

        return {
            ...row,
            id: row.value,
            displayLabel,
            label: displayLabel,
            color: chartColorForIndex(index),
            percent: fraction * 100,
            searchHref: props.filterKey
                ? buildStatsLegendSearchPath(props.filterKey, row.value)
                : null,
            isFullRing: isFullCircleSpan(startAngle, endAngle),
            path: describeDonutSlice(
                center,
                center,
                innerRadius,
                outerRadius,
                startAngle,
                endAngle
            ),
        };
    });
});
</script>

<template>
    <div
        class="flex h-full flex-col rounded-2xl border border-gray-100 bg-white p-5"
    >
        <div>
            <h3 class="text-base font-semibold text-gray-900">
                {{ title }}
            </h3>
            <p class="mt-1 text-xs text-gray-500">
                {{ formatStatsNumber(totalCounted) }} {{ unit }}
                <span v-if="missing > 0">
                    · {{ formatStatsNumber(missing) }} missing metadata
                </span>
            </p>
        </div>

        <div
            v-if="slices.length"
            class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-start"
        >
            <div class="flex shrink-0 justify-center sm:w-52">
                <svg
                    :viewBox="`0 0 ${size} ${size}`"
                    class="h-52 w-52"
                    role="img"
                    :aria-label="`${title} pie chart`"
                >
                    <StatsDonutSlices
                        :slices="slices"
                        :center="center"
                        :ring-radius="ringRadius"
                        :ring-stroke-width="ringStrokeWidth"
                    />

                    <text
                        :x="center"
                        :y="center - 4"
                        text-anchor="middle"
                        class="fill-gray-900 text-[15px] font-semibold"
                    >
                        {{ formatStatsNumber(totalCounted) }}
                    </text>
                    <text
                        :x="center"
                        :y="center + 12"
                        text-anchor="middle"
                        class="fill-gray-500 text-[10px]"
                    >
                        {{ unit }}
                    </text>
                </svg>
            </div>

            <ul class="min-h-0 max-h-52 flex-1 space-y-2 overflow-y-auto pr-1">
                <li
                    v-for="slice in slices"
                    :key="`${slice.value}-legend`"
                    class="flex items-start justify-between gap-3"
                >
                    <div class="flex min-w-0 flex-1 items-start gap-2">
                        <span
                            class="mt-1 h-3 w-3 shrink-0 rounded-full"
                            :style="{ backgroundColor: slice.color }"
                        />
                        <Link
                            v-if="slice.searchHref"
                            :href="slice.searchHref"
                            class="min-w-0 truncate text-sm font-medium text-gray-700 underline-offset-2 transition-colors hover:text-gray-900 hover:underline focus-visible:rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
                            :title="slice.displayLabel"
                        >
                            {{ slice.displayLabel }}
                        </Link>
                        <span
                            v-else
                            class="min-w-0 truncate text-sm font-medium text-gray-700"
                            :title="slice.value"
                        >
                            {{ slice.displayLabel }}
                        </span>
                    </div>
                    <div
                        class="shrink-0 text-right text-sm tabular-nums text-gray-600"
                    >
                        <span class="font-medium">{{
                            formatStatsNumber(slice.count)
                        }}</span>
                        <span class="text-gray-400">
                            · {{ formatStatsPercent(slice.percent) }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>

        <p v-else class="mt-5 text-sm text-gray-500">
            No distribution data available yet.
        </p>
    </div>
</template>
