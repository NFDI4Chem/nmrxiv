<template>
    <figure class="w-full">
        <svg
            :viewBox="`0 0 ${width} ${height}`"
            class="h-auto w-full"
            role="img"
            :aria-label="ariaLabel"
        >
            <rect
                v-for="(region, index) in regions"
                :key="`region-${index}`"
                :x="region.x"
                :y="region.excluded ? top : top + 6"
                :width="Math.max(region.width, 2)"
                :height="
                    region.excluded
                        ? baseline - top + recordHeight
                        : queryHeight - 6
                "
                :class="
                    region.excluded
                        ? 'fill-red-100 stroke-red-300'
                        : region.matched
                        ? 'fill-gray-200 stroke-gray-500'
                        : 'fill-gray-100 stroke-gray-300'
                "
                stroke-width="1"
                rx="2"
            />

            <line
                v-for="(match, index) in links"
                :key="`link-${index}`"
                :x1="match.from"
                :x2="match.to"
                :y1="baseline - 1"
                :y2="baseline + 1"
                class="stroke-emerald-500"
                stroke-width="1.5"
            />

            <line
                v-for="(stick, index) in querySticks"
                :key="`query-${index}`"
                :x1="stick.x"
                :x2="stick.x"
                :y1="baseline - 2"
                :y2="top + 6"
                :class="stick.matched ? 'stroke-gray-900' : 'stroke-amber-500'"
                :stroke-dasharray="stick.matched ? undefined : '3 2'"
                stroke-width="2"
            />

            <line
                v-for="(stick, index) in recordSticks"
                :key="`record-${index}`"
                :x1="stick.x"
                :x2="stick.x"
                :y1="baseline + 2"
                :y2="baseline + recordHeight"
                :class="
                    stick.matched ? 'stroke-emerald-600' : 'stroke-gray-300'
                "
                stroke-width="2"
            />

            <line
                :x1="0"
                :x2="width"
                :y1="baseline"
                :y2="baseline"
                class="stroke-gray-300"
                stroke-width="1"
            />

            <g v-for="tick in ticks" :key="`tick-${tick.value}`">
                <line
                    :x1="tick.x"
                    :x2="tick.x"
                    :y1="axisY"
                    :y2="axisY + 4"
                    class="stroke-gray-400"
                    stroke-width="1"
                />
                <text
                    :x="tick.x"
                    :y="axisY + 15"
                    text-anchor="middle"
                    class="fill-gray-500 text-[10px]"
                >
                    {{ tick.label }}
                </text>
            </g>
        </svg>
        <figcaption
            class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-gray-500"
        >
            <span class="inline-flex items-center gap-1">
                <span class="inline-block h-3 w-0.5 bg-gray-900" />
                Your peaks (above)
            </span>
            <span class="inline-flex items-center gap-1">
                <span class="inline-block h-3 w-0.5 bg-emerald-600" />
                Matched in this spectrum (below)
            </span>
            <span v-if="hasMissing" class="inline-flex items-center gap-1">
                <span class="inline-block h-3 w-0.5 bg-amber-500" />
                Not found
            </span>
            <span
                v-if="recordSticks.some((stick) => !stick.matched)"
                class="inline-flex items-center gap-1"
            >
                <span class="inline-block h-3 w-0.5 bg-gray-300" />
                Other peaks
            </span>
            <span
                v-if="regions.some((region) => region.excluded)"
                class="inline-flex items-center gap-1"
            >
                <span
                    class="inline-block h-3 w-3 rounded-sm border border-red-300 bg-red-100"
                />
                Must not have
            </span>
            <span class="ml-auto">ppm</span>
        </figcaption>
    </figure>
</template>

<script>
import { computed } from "vue";

const NICE_STEPS = [0.1, 0.2, 0.5, 1, 2, 5, 10, 20, 25, 50];

export default {
    props: {
        nucleus: {
            type: String,
            required: true,
        },
        peaks: {
            type: Array,
            default: () => [],
        },
        matches: {
            type: Array,
            default: () => [],
        },
        extra: {
            type: Array,
            default: () => [],
        },
        offset: {
            type: Number,
            default: 0,
        },
    },
    setup(props) {
        const width = 600;
        const top = 4;
        const queryHeight = 34;
        const recordHeight = 30;
        const baseline = top + queryHeight;
        const axisY = baseline + recordHeight + 2;
        const height = axisY + 18;

        const matchedByPeak = computed(
            () => new Map(props.matches.map((match) => [match.peak, match]))
        );

        const domain = computed(() => {
            const values = [
                ...props.peaks.flatMap((peak) => [peak.from, peak.to]),
                ...props.matches.map((match) => match.shift),
                ...props.extra,
            ].filter((value) => Number.isFinite(value));

            if (values.length === 0) {
                return props.nucleus === "13C" ? [0, 200] : [0, 10];
            }

            const min = Math.min(...values);
            const max = Math.max(...values);
            const padding = Math.max(
                (max - min) * 0.06,
                props.nucleus === "13C" ? 3 : 0.2
            );

            return [min - padding, max + padding];
        });

        const toX = (value) => {
            const [min, max] = domain.value;

            return ((max - value) / (max - min)) * width;
        };

        const regions = computed(() =>
            props.peaks
                .map((peak, index) => ({ peak, index }))
                .filter(
                    ({ peak }) => peak.to > peak.from || peak.rule === "not"
                )
                .map(({ peak, index }) => {
                    const x = toX(peak.to);

                    return {
                        x: peak.to > peak.from ? x : x - 1,
                        width: toX(peak.from) - x,
                        excluded: peak.rule === "not",
                        matched: matchedByPeak.value.has(index),
                    };
                })
        );

        const querySticks = computed(() =>
            props.peaks
                .map((peak, index) => ({ peak, index }))
                .filter(
                    ({ peak }) => peak.to <= peak.from && peak.rule !== "not"
                )
                .map(({ peak, index }) => ({
                    x: toX(peak.from),
                    matched: matchedByPeak.value.has(index),
                }))
        );

        const recordSticks = computed(() => [
            ...props.matches.map((match) => ({
                x: toX(match.shift),
                matched: true,
            })),
            ...props.extra.map((shift) => ({ x: toX(shift), matched: false })),
        ]);

        const links = computed(() =>
            props.matches
                .map((match) => {
                    const peak = props.peaks[match.peak];
                    if (!peak || peak.to > peak.from) {
                        return null;
                    }

                    return { from: toX(peak.from), to: toX(match.shift) };
                })
                .filter(Boolean)
        );

        const hasMissing = computed(() =>
            props.peaks.some(
                (peak, index) =>
                    peak.rule !== "not" && !matchedByPeak.value.has(index)
            )
        );

        const ticks = computed(() => {
            const [min, max] = domain.value;
            const step =
                NICE_STEPS.find((candidate) => (max - min) / candidate <= 8) ??
                50;
            const digits = step < 1 ? 1 : 0;
            const values = [];
            for (
                let value = Math.ceil(min / step) * step;
                value <= max;
                value += step
            ) {
                values.push(value);
            }

            return values.map((value) => ({
                value,
                x: toX(value),
                label: value.toFixed(digits),
            }));
        });

        const ariaLabel = computed(() => {
            const wanted = props.peaks.filter(
                (peak) => peak.rule !== "not"
            ).length;

            return `${
                props.matches.length
            } of ${wanted} of your peaks were found in this spectrum, which has ${
                props.matches.length + props.extra.length
            } peaks.`;
        });

        return {
            width,
            height,
            top,
            baseline,
            queryHeight,
            recordHeight,
            axisY,
            regions,
            querySticks,
            recordSticks,
            links,
            ticks,
            hasMissing,
            ariaLabel,
        };
    },
};
</script>
