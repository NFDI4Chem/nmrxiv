<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import StatsDonutSlices from "@/Shared/Stats/StatsDonutSlices.vue";
import { chartColorForIndex, CHART_COLORS } from "@/Utils/chartColors";
import {
    describeDonutSlice,
    donutRingRadius,
    donutRingStrokeWidth,
    isFullCircleSpan,
    mixChartColorWithWhite,
} from "@/Utils/chartGeometry";
import { formatStatsNumber, formatStatsPercent } from "@/Utils/statsChart";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: "",
    },
    groups: {
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
    emptyMessage: {
        type: String,
        default: "No distribution data available yet.",
    },
    ariaLabel: {
        type: String,
        default: "Sunburst chart",
    },
    colors: {
        type: Array,
        default: () => CHART_COLORS,
    },
});

const size = 220;
const center = size / 2;
const innerHole = 25;
const innerRingInner = 28;
const innerRingOuter = 67;
const outerRingInner = 69;
const outerRingOuter = 101;

const totalCounted = computed(() =>
    props.groups.reduce((sum, group) => sum + group.count, 0)
);

const chart = computed(() => {
    if (!totalCounted.value || !props.groups.length) {
        return { innerSlices: [], outerSlices: [], legendGroups: [] };
    }

    const total = totalCounted.value;
    let angle = -Math.PI / 2;
    const innerSlices = [];
    const outerSlices = [];
    const legendGroups = [];

    props.groups.forEach((group, groupIndex) => {
        const groupFraction = group.count / total;
        const groupStart = angle;
        const groupEnd = angle + groupFraction * Math.PI * 2;
        const color = chartColorForIndex(groupIndex, props.colors);

        innerSlices.push({
            id: group.key,
            innerR: innerRingInner,
            outerR: innerRingOuter,
            ringRadius: donutRingRadius(innerRingInner, innerRingOuter),
            ringStrokeWidth: donutRingStrokeWidth(
                innerRingInner,
                innerRingOuter
            ),
            isFullRing: isFullCircleSpan(groupStart, groupEnd),
            path: describeDonutSlice(
                center,
                center,
                innerRingInner,
                innerRingOuter,
                groupStart,
                groupEnd
            ),
            color,
            label: group.label,
            count: group.count,
            percent: groupFraction * 100,
            searchHref: group.searchHref ?? null,
            ariaLabel: `Search ${group.label} spectra`,
        });

        const segments = [];
        let childAngle = groupStart;

        (group.segments ?? []).forEach((segment, segmentIndex) => {
            const childFraction = segment.count / total;
            const childEnd = childAngle + childFraction * Math.PI * 2;

            outerSlices.push({
                id: `${group.key}-${segment.key}`,
                innerR: outerRingInner,
                outerR: outerRingOuter,
                ringRadius: donutRingRadius(outerRingInner, outerRingOuter),
                ringStrokeWidth: donutRingStrokeWidth(
                    outerRingInner,
                    outerRingOuter
                ),
                isFullRing: isFullCircleSpan(childAngle, childEnd),
                path: describeDonutSlice(
                    center,
                    center,
                    outerRingInner,
                    outerRingOuter,
                    childAngle,
                    childEnd
                ),
                color: mixChartColorWithWhite(
                    color,
                    0.12 + (segmentIndex % 4) * 0.1
                ),
                label: segment.label,
                groupLabel: group.label,
                count: segment.count,
                percent: childFraction * 100,
                searchHref: segment.searchHref ?? null,
            });

            segments.push({
                key: segment.key,
                label: segment.label,
                count: segment.count,
                percent: (segment.count / group.count) * 100,
                searchHref: segment.searchHref ?? null,
            });

            childAngle = childEnd;
        });

        legendGroups.push({
            key: group.key,
            label: group.label,
            color,
            count: group.count,
            percent: groupFraction * 100,
            groupSearchHref: group.searchHref ?? null,
            segments,
        });

        angle = groupEnd;
    });

    return { innerSlices, outerSlices, legendGroups };
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
            <p v-if="subtitle" class="mt-1 text-xs text-gray-500">
                {{ subtitle }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ formatStatsNumber(totalCounted) }} {{ unit }}
                <span v-if="missing > 0">
                    · {{ formatStatsNumber(missing) }} missing metadata
                </span>
            </p>
        </div>

        <div
            v-if="chart.legendGroups.length"
            class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-start"
        >
            <div class="flex shrink-0 justify-center sm:w-52">
                <svg
                    :viewBox="`0 0 ${size} ${size}`"
                    class="h-52 w-52"
                    role="img"
                    :aria-label="ariaLabel"
                >
                    <circle
                        :cx="center"
                        :cy="center"
                        :r="innerHole"
                        class="fill-white"
                    />

                    <StatsDonutSlices
                        :slices="chart.innerSlices"
                        :center="center"
                    />

                    <StatsDonutSlices
                        :slices="chart.outerSlices"
                        :center="center"
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

            <ul
                class="min-h-0 flex-1 space-y-3 overflow-y-auto pr-1 sm:max-h-52"
            >
                <li
                    v-for="group in chart.legendGroups"
                    :key="group.key"
                    class="space-y-2"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 flex-1 items-start gap-2">
                            <span
                                class="mt-1 h-3 w-3 shrink-0 rounded-full"
                                :style="{ backgroundColor: group.color }"
                            />
                            <Link
                                v-if="group.groupSearchHref"
                                :href="group.groupSearchHref"
                                class="min-w-0 truncate text-sm font-semibold text-gray-800 underline-offset-2 transition-colors hover:text-gray-900 hover:underline"
                                :title="group.label"
                            >
                                {{ group.label }}
                            </Link>
                            <span
                                v-else
                                class="text-sm font-semibold text-gray-800"
                            >
                                {{ group.label }}
                            </span>
                        </div>
                        <div
                            class="shrink-0 text-right text-sm tabular-nums text-gray-600"
                        >
                            <span class="font-medium">{{
                                formatStatsNumber(group.count)
                            }}</span>
                            <span class="text-gray-400">
                                · {{ formatStatsPercent(group.percent) }}
                            </span>
                        </div>
                    </div>

                    <ul class="space-y-1.5 border-l border-gray-100 pl-4">
                        <li
                            v-for="segment in group.segments"
                            :key="`${group.key}-${segment.key}`"
                            class="flex items-start justify-between gap-3"
                        >
                            <Link
                                v-if="segment.searchHref"
                                :href="segment.searchHref"
                                class="min-w-0 flex-1 truncate text-sm text-gray-600 underline-offset-2 transition-colors hover:text-gray-900 hover:underline"
                                :title="segment.label"
                            >
                                {{ segment.label }}
                            </Link>
                            <span
                                v-else
                                class="min-w-0 flex-1 truncate text-sm text-gray-600"
                                :title="segment.label"
                            >
                                {{ segment.label }}
                            </span>
                            <div
                                class="shrink-0 text-right text-sm tabular-nums text-gray-500"
                            >
                                <span>{{
                                    formatStatsNumber(segment.count)
                                }}</span>
                                <span class="text-gray-400">
                                    · {{ formatStatsPercent(segment.percent) }}
                                </span>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <p v-else class="mt-5 text-sm text-gray-500">
            {{ emptyMessage }}
        </p>
    </div>
</template>
