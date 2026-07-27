<template>
    <div class="relative w-full">
        <svg
            :viewBox="`0 0 ${width} ${height}`"
            class="h-auto w-full select-none"
            role="img"
            :aria-label="ariaLabel"
            @wheel.prevent="onWheel"
            @mousedown="onPanStart"
            @mousemove="onPanMove"
            @mouseup="onPanEnd"
            @mouseleave="onPanEnd"
        >
            <rect
                :x="padding.left"
                :y="padding.top"
                :width="plotWidth"
                :height="plotHeight"
                class="fill-gray-50 dark:fill-slate-900/40"
            />

            <line
                v-for="tick in xTicks"
                :key="`xt-${tick.value}`"
                :x1="tick.x"
                :y1="padding.top"
                :x2="tick.x"
                :y2="plotBottom"
                class="stroke-gray-100 dark:stroke-slate-700"
                stroke-width="1"
            />

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

            <g v-for="stick in visibleSticks" :key="stick.id">
                <line
                    :x1="stick.cx"
                    :y1="plotBottom"
                    :x2="stick.cx"
                    :y2="stick.top"
                    :stroke="stick.color"
                    stroke-width="2"
                    stroke-linecap="round"
                    class="cursor-pointer transition-opacity"
                    :class="{
                        'opacity-100': isHighlighted(stick.id),
                        'opacity-25':
                            (activeId || selectedId) &&
                            !isHighlighted(stick.id),
                    }"
                    @mouseenter="$emit('hover', stick.id)"
                    @mouseleave="$emit('leave')"
                    @click.stop="$emit('select', stick.id)"
                >
                    <title>{{ stick.title || stick.label }}</title>
                </line>
                <circle
                    :cx="stick.cx"
                    :cy="stick.top"
                    r="3"
                    :fill="stick.color"
                    class="cursor-pointer transition-opacity"
                    :class="{
                        'opacity-100': isHighlighted(stick.id),
                        'opacity-25':
                            (activeId || selectedId) &&
                            !isHighlighted(stick.id),
                    }"
                    @mouseenter="$emit('hover', stick.id)"
                    @mouseleave="$emit('leave')"
                    @click.stop="$emit('select', stick.id)"
                />
            </g>

            <text
                v-for="tick in xTicks"
                :key="`xl-${tick.value}`"
                :x="tick.x"
                :y="plotBottom + 16"
                text-anchor="middle"
                class="fill-gray-500 text-[10px] tabular-nums dark:fill-slate-400"
            >
                {{ tick.value.toFixed(1) }}
            </text>

            <text
                :x="(padding.left + plotRight) / 2"
                :y="height - 6"
                text-anchor="middle"
                class="fill-gray-500 text-[11px] dark:fill-slate-400"
            >
                {{ xLabel }}
            </text>

            <text
                v-if="domainHint"
                :x="plotRight"
                :y="padding.top - 2"
                text-anchor="end"
                class="fill-gray-400 text-[10px] dark:fill-slate-500"
            >
                {{ domainHint }}
            </text>
        </svg>

        <div class="mt-1 flex items-center justify-between gap-2">
            <p class="text-xs text-gray-500 dark:text-slate-400">
                Scroll to zoom · drag to pan · click a stick for details
            </p>
            <button
                v-if="isZoomed"
                type="button"
                class="text-xs font-medium text-teal-700 hover:underline dark:text-teal-300"
                @click="resetView"
            >
                Reset view
            </button>
        </div>

        <p
            v-if="!sticks.length"
            class="py-6 text-center text-sm text-gray-500 dark:text-slate-400"
        >
            No assignments to plot
        </p>
    </div>
</template>

<script>
export default {
    name: "HifsaStickPlot",
    props: {
        sticks: {
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
            default: "δ (ppm)",
        },
    },
    emits: ["hover", "leave", "select"],
    data() {
        return {
            width: 720,
            height: 260,
            padding: {
                top: 18,
                right: 16,
                bottom: 42,
                left: 28,
            },
            viewMin: null,
            viewMax: null,
            panning: false,
            panStartX: 0,
            panStartMin: 0,
            panStartMax: 0,
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
        dataDomain() {
            const xs = this.sticks
                .map((stick) => stick.x)
                .filter(
                    (value) =>
                        typeof value === "number" && Number.isFinite(value)
                );

            if (!xs.length) {
                return { min: 0, max: 10 };
            }

            const min = Math.min(...xs);
            const max = Math.max(...xs);
            const pad = Math.max((max - min) * 0.05, 0.2);

            return {
                min: min - pad,
                max: max + pad,
            };
        },
        domain() {
            const min =
                this.viewMin == null ? this.dataDomain.min : this.viewMin;
            const max =
                this.viewMax == null ? this.dataDomain.max : this.viewMax;

            if (max <= min) {
                return { min, max: min + 1 };
            }

            return { min, max };
        },
        isZoomed() {
            return this.viewMin != null || this.viewMax != null;
        },
        maxHeight() {
            const heights = this.sticks
                .map((stick) => stick.height)
                .filter(
                    (value) =>
                        typeof value === "number" && Number.isFinite(value)
                );

            return Math.max(...(heights.length ? heights : [1]), 0.1);
        },
        visibleSticks() {
            return this.sticks
                .filter(
                    (stick) =>
                        typeof stick.x === "number" &&
                        stick.x >= this.domain.min &&
                        stick.x <= this.domain.max
                )
                .map((stick) => {
                    const cx = this.xScale(stick.x);
                    const heightPx =
                        (Math.max(stick.height, 0) / this.maxHeight) *
                        (this.plotHeight * 0.92);

                    return {
                        ...stick,
                        cx,
                        top: this.plotBottom - Math.max(heightPx, 8),
                    };
                });
        },
        xTicks() {
            const { min, max } = this.domain;
            const span = max - min;
            const step = this.niceStep(span / 6);
            const start = Math.ceil(min / step) * step;
            const ticks = [];

            for (let value = start; value <= max + step * 0.01; value += step) {
                ticks.push({
                    value,
                    x: this.xScale(value),
                });
            }

            return ticks;
        },
        domainHint() {
            return `${this.domain.max.toFixed(2)} → ${this.domain.min.toFixed(
                2
            )} ppm`;
        },
        ariaLabel() {
            return `Assignment stick plot with ${this.sticks.length} peaks`;
        },
    },
    watch: {
        sticks() {
            this.resetView();
        },
    },
    methods: {
        isHighlighted(id) {
            return this.activeId === id || this.selectedId === id;
        },
        xScale(value) {
            // NMR convention: higher ppm on the left.
            const t =
                (this.domain.max - value) /
                Math.max(this.domain.max - this.domain.min, 1e-9);

            return this.padding.left + t * this.plotWidth;
        },
        niceStep(raw) {
            const magnitude = 10 ** Math.floor(Math.log10(Math.max(raw, 1e-6)));
            const residual = raw / magnitude;

            if (residual <= 1) {
                return magnitude;
            }

            if (residual <= 2) {
                return 2 * magnitude;
            }

            if (residual <= 5) {
                return 5 * magnitude;
            }

            return 10 * magnitude;
        },
        resetView() {
            this.viewMin = null;
            this.viewMax = null;
        },
        onWheel(event) {
            const { min, max } = this.domain;
            const span = max - min;
            const factor = event.deltaY > 0 ? 1.15 : 1 / 1.15;
            const nextSpan = Math.min(
                Math.max(span * factor, 0.2),
                Math.max(this.dataDomain.max - this.dataDomain.min, 1) * 1.5
            );
            const center = (min + max) / 2;

            this.viewMin = center - nextSpan / 2;
            this.viewMax = center + nextSpan / 2;
        },
        onPanStart(event) {
            this.panning = true;
            this.panStartX = event.clientX;
            this.panStartMin = this.domain.min;
            this.panStartMax = this.domain.max;
        },
        onPanMove(event) {
            if (!this.panning) {
                return;
            }

            const dx = event.clientX - this.panStartX;
            const span = this.panStartMax - this.panStartMin;
            const delta = (dx / this.plotWidth) * span;

            // Dragging right moves the window toward higher ppm (left of plot).
            this.viewMin = this.panStartMin + delta;
            this.viewMax = this.panStartMax + delta;
        },
        onPanEnd() {
            this.panning = false;
        },
    },
};
</script>
