<template>
    <div class="relative w-full">
        <svg
            :viewBox="`0 0 ${width} ${height}`"
            class="h-auto w-full"
            role="img"
            :aria-label="ariaLabel"
        >
            <line
                :x1="padding.left"
                :y1="plotBottom"
                :x2="plotRight"
                :y2="plotBottom"
                class="stroke-gray-300 dark:stroke-slate-500"
                stroke-width="1"
            />
            <line
                :x1="padding.left"
                :y1="padding.top"
                :x2="padding.left"
                :y2="plotBottom"
                class="stroke-gray-300 dark:stroke-slate-500"
                stroke-width="1"
            />

            <circle
                v-for="point in layoutPoints"
                :key="point.id"
                :cx="point.cx"
                :cy="point.cy"
                :r="isHighlighted(point.id) ? 6 : 4.5"
                :fill="point.color"
                class="cursor-pointer transition-opacity"
                :class="{
                    'opacity-100': isHighlighted(point.id) || !activeId,
                    'opacity-30':
                        (activeId || selectedId) && !isHighlighted(point.id),
                }"
                @mouseenter="$emit('hover', point.id)"
                @mouseleave="$emit('leave')"
                @click="$emit('select', point.id)"
            >
                <title>{{ point.title || point.label }}</title>
            </circle>

            <text
                :x="(padding.left + plotRight) / 2"
                :y="height - 8"
                text-anchor="middle"
                class="fill-gray-500 text-[11px] dark:fill-slate-400"
            >
                {{ xLabel }}
            </text>
            <text
                :x="14"
                :y="(padding.top + plotBottom) / 2"
                text-anchor="middle"
                class="fill-gray-500 text-[11px] dark:fill-slate-400"
                :transform="`rotate(-90 14 ${(padding.top + plotBottom) / 2})`"
            >
                {{ yLabel }}
            </text>
        </svg>

        <p
            v-if="!points.length"
            class="py-6 text-center text-sm text-gray-500 dark:text-slate-400"
        >
            No lineshape points to plot
        </p>
    </div>
</template>

<script>
export default {
    name: "HifsaScatterPlot",
    props: {
        points: {
            type: Array,
            default: () => [],
        },
        activeId: {
            type: String,
            default: null,
        },
        selectedId: {
            type: String,
            default: null,
        },
        xLabel: {
            type: String,
            default: "X",
        },
        yLabel: {
            type: String,
            default: "Y",
        },
    },
    emits: ["hover", "leave", "select"],
    data() {
        return {
            width: 640,
            height: 260,
            padding: {
                top: 16,
                right: 16,
                bottom: 40,
                left: 48,
            },
        };
    },
    computed: {
        plotBottom() {
            return this.height - this.padding.bottom;
        },
        plotRight() {
            return this.width - this.padding.right;
        },
        plotWidth() {
            return this.plotRight - this.padding.left;
        },
        plotHeight() {
            return this.plotBottom - this.padding.top;
        },
        xDomain() {
            const xs = this.points
                .map((point) => point.x)
                .filter(
                    (value) =>
                        typeof value === "number" && Number.isFinite(value)
                );

            if (!xs.length) {
                return { min: 0, max: 1 };
            }

            const min = Math.min(...xs);
            const max = Math.max(...xs);
            const pad = Math.max((max - min) * 0.08, 0.01);

            return { min: min - pad, max: max + pad };
        },
        yDomain() {
            const ys = this.points
                .map((point) => point.y)
                .filter(
                    (value) =>
                        typeof value === "number" && Number.isFinite(value)
                );

            if (!ys.length) {
                return { min: 0, max: 1 };
            }

            const min = Math.min(...ys, 0);
            const max = Math.max(...ys);
            const pad = Math.max((max - min) * 0.08, 0.01);

            return { min: min - pad * 0.2, max: max + pad };
        },
        layoutPoints() {
            return this.points
                .filter(
                    (point) =>
                        typeof point.x === "number" &&
                        typeof point.y === "number" &&
                        Number.isFinite(point.x) &&
                        Number.isFinite(point.y)
                )
                .map((point) => ({
                    ...point,
                    cx:
                        this.padding.left +
                        ((point.x - this.xDomain.min) /
                            Math.max(
                                this.xDomain.max - this.xDomain.min,
                                1e-9
                            )) *
                            this.plotWidth,
                    cy:
                        this.plotBottom -
                        ((point.y - this.yDomain.min) /
                            Math.max(
                                this.yDomain.max - this.yDomain.min,
                                1e-9
                            )) *
                            this.plotHeight,
                }));
        },
        ariaLabel() {
            return `Scatter plot with ${this.points.length} points`;
        },
    },
    methods: {
        isHighlighted(id) {
            return this.activeId === id || this.selectedId === id;
        },
    },
};
</script>
