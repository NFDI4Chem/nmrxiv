<template>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:gap-6">
            <div class="mx-auto w-full max-w-sm shrink-0 lg:mx-0">
                <svg
                    :viewBox="`0 0 ${size} ${size}`"
                    class="h-auto w-full"
                    role="img"
                    :aria-label="ariaLabel"
                >
                    <g
                        v-for="(ring, ringIndex) in rings"
                        :key="`ring-${ringIndex}`"
                    >
                        <polygon
                            :points="ring"
                            fill="none"
                            class="stroke-gray-200 dark:stroke-slate-600"
                            stroke-width="1"
                        />
                    </g>

                    <line
                        v-for="(axis, axisIndex) in axes"
                        :key="`axis-${axisIndex}`"
                        :x1="center"
                        :y1="center"
                        :x2="axis.x"
                        :y2="axis.y"
                        class="stroke-gray-200 dark:stroke-slate-600"
                        stroke-width="1"
                    />

                    <polygon
                        :points="valuePolygon"
                        class="fill-teal-500/25 stroke-teal-600 dark:fill-teal-400/30 dark:stroke-teal-400"
                        stroke-width="2"
                        stroke-linejoin="round"
                    />

                    <circle
                        v-for="(point, pointIndex) in valuePoints"
                        :key="`point-${pointIndex}`"
                        :cx="point.x"
                        :cy="point.y"
                        r="3.5"
                        class="fill-teal-700 dark:fill-teal-300"
                    />

                    <text
                        v-for="(label, labelIndex) in labels"
                        :key="`label-${labelIndex}`"
                        :x="label.x"
                        :y="label.y"
                        text-anchor="middle"
                        dominant-baseline="middle"
                        class="fill-gray-700 text-[11px] font-medium dark:fill-slate-200"
                    >
                        {{ label.text }}
                    </text>
                </svg>
            </div>

            <dl class="grid min-w-0 flex-1 grid-cols-1 gap-3 sm:grid-cols-2">
                <div
                    v-for="metric in scoreMetrics"
                    :key="metric.key"
                    class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-900/40"
                >
                    <dt
                        class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                    >
                        {{ metric.label }}
                    </dt>
                    <dd
                        class="mt-0.5 text-sm font-semibold tabular-nums text-gray-900 dark:text-slate-100"
                    >
                        {{ formatScore(metric.value) }}
                    </dd>
                </div>

                <div
                    class="rounded-lg border border-gray-100 bg-white px-3 py-2 sm:col-span-2 dark:border-slate-700 dark:bg-slate-800/60"
                >
                    <dt
                        class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                    >
                        Remarks
                    </dt>
                    <dd
                        class="mt-0.5 text-sm text-gray-800 dark:text-slate-200"
                    >
                        {{ remarks || "—" }}
                    </dd>
                </div>

                <div
                    class="rounded-lg border border-gray-100 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800/60"
                >
                    <dt
                        class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                    >
                        Cosmic Truth
                    </dt>
                    <dd class="mt-0.5 text-sm">
                        <a
                            v-if="url"
                            :href="url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="break-all font-medium text-teal-700 underline-offset-2 hover:underline dark:text-teal-300"
                        >
                            Open analysis
                        </a>
                        <span v-else class="text-gray-800 dark:text-slate-200"
                            >—</span
                        >
                    </dd>
                </div>

                <div
                    class="rounded-lg border border-gray-100 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800/60"
                >
                    <dt
                        class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                    >
                        Solvent
                    </dt>
                    <dd
                        class="mt-0.5 text-sm text-gray-800 dark:text-slate-200"
                    >
                        {{ solvent || "—" }}
                    </dd>
                </div>

                <div
                    class="rounded-lg border border-gray-100 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-800/60"
                >
                    <dt
                        class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                    >
                        Temperature
                    </dt>
                    <dd
                        class="mt-0.5 text-sm text-gray-800 dark:text-slate-200"
                    >
                        {{ temperatureLabel }}
                    </dd>
                </div>

                <div
                    v-if="createdLabel || modifiedLabel"
                    class="rounded-lg border border-gray-100 bg-white px-3 py-2 sm:col-span-2 dark:border-slate-700 dark:bg-slate-800/60"
                >
                    <dt
                        class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-slate-400"
                    >
                        Provenance
                    </dt>
                    <dd
                        class="mt-0.5 space-y-0.5 text-sm text-gray-800 dark:text-slate-200"
                    >
                        <div v-if="createdLabel">
                            Created: {{ createdLabel }}
                        </div>
                        <div v-if="modifiedLabel">
                            Modified: {{ modifiedLabel }}
                        </div>
                    </dd>
                </div>
            </dl>
        </div>

        <div v-if="detailSections.length" class="flex flex-col gap-3">
            <section
                v-for="section in detailSections"
                :key="section.id"
                class="overflow-hidden rounded-lg border border-gray-100 dark:border-slate-700"
            >
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-3 bg-gray-50 px-3 py-2.5 text-left transition-colors hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-500 dark:bg-slate-900/40 dark:hover:bg-slate-900/70"
                    :aria-expanded="expandedSections[section.id]"
                    @click="toggleSection(section.id)"
                >
                    <span
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        {{ section.title }}
                        <span
                            class="ml-1.5 font-normal text-gray-500 dark:text-slate-400"
                        >
                            ({{ section.count }})
                        </span>
                    </span>
                    <svg
                        class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200 dark:text-slate-500"
                        :class="{
                            'rotate-90': expandedSections[section.id],
                        }"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>

                <div
                    v-show="expandedSections[section.id]"
                    class="border-t border-gray-100 dark:border-slate-700"
                >
                    <div
                        v-if="section.kind === 'spinsystems'"
                        class="overflow-x-auto"
                    >
                        <table class="min-w-full text-left text-sm">
                            <thead
                                class="bg-white text-xs uppercase tracking-wide text-gray-500 dark:bg-slate-800/60 dark:text-slate-400"
                            >
                                <tr>
                                    <th class="px-3 py-2 font-medium">Name</th>
                                    <th class="px-3 py-2 font-medium">Type</th>
                                    <th class="px-3 py-2 font-medium">
                                        Formula
                                    </th>
                                    <th class="px-3 py-2 font-medium">MW</th>
                                    <th class="px-3 py-2 font-medium">
                                        InChIKey
                                    </th>
                                    <th class="px-3 py-2 font-medium">
                                        Population
                                    </th>
                                    <th class="px-3 py-2 font-medium">LRMS</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-100 dark:divide-slate-700"
                            >
                                <tr
                                    v-for="(row, rowIndex) in spinsystems"
                                    :key="row.ct_key || rowIndex"
                                    class="bg-white dark:bg-slate-800/40"
                                >
                                    <td
                                        class="max-w-[12rem] truncate px-3 py-2 text-gray-900 dark:text-slate-100"
                                        :title="row.name || ''"
                                    >
                                        {{ row.name || "—" }}
                                    </td>
                                    <td
                                        class="px-3 py-2 text-gray-700 dark:text-slate-300"
                                    >
                                        {{ row.ss_type || "—" }}
                                    </td>
                                    <td
                                        class="px-3 py-2 font-mono text-xs text-gray-700 dark:text-slate-300"
                                    >
                                        {{ row.formula || "—" }}
                                    </td>
                                    <td
                                        class="px-3 py-2 tabular-nums text-gray-700 dark:text-slate-300"
                                    >
                                        {{ formatNumber(row.mw, 2) }}
                                    </td>
                                    <td
                                        class="max-w-[14rem] truncate px-3 py-2 font-mono text-xs text-gray-700 dark:text-slate-300"
                                        :title="row.inchi_key || ''"
                                    >
                                        {{ row.inchi_key || "—" }}
                                    </td>
                                    <td
                                        class="px-3 py-2 tabular-nums text-gray-700 dark:text-slate-300"
                                    >
                                        {{ formatPercent(row.population) }}
                                    </td>
                                    <td
                                        class="px-3 py-2 tabular-nums text-gray-700 dark:text-slate-300"
                                    >
                                        {{ formatNumber(row.lrms, 3) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="flex flex-col gap-4 p-3 sm:p-4">
                        <div
                            v-for="group in section.groups"
                            :key="group.name"
                            class="overflow-hidden rounded-md border border-gray-100 dark:border-slate-700"
                        >
                            <div
                                class="border-b border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-gray-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300"
                            >
                                {{ group.name }}
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-left text-sm">
                                    <thead
                                        class="bg-white text-xs uppercase tracking-wide text-gray-500 dark:bg-slate-800/60 dark:text-slate-400"
                                    >
                                        <tr>
                                            <th
                                                v-for="column in section.columns"
                                                :key="column.key"
                                                class="px-3 py-2 font-medium"
                                            >
                                                {{ column.label }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="divide-y divide-gray-100 dark:divide-slate-700"
                                    >
                                        <tr
                                            v-for="(
                                                row, rowIndex
                                            ) in group.rows"
                                            :key="rowIndex"
                                            class="bg-white dark:bg-slate-800/40"
                                        >
                                            <td
                                                v-for="column in section.columns"
                                                :key="column.key"
                                                class="px-3 py-2 text-gray-700 dark:text-slate-300"
                                                :class="{
                                                    'tabular-nums':
                                                        column.numeric,
                                                    'font-mono text-xs':
                                                        column.mono,
                                                    'max-w-[10rem] truncate':
                                                        column.truncate,
                                                }"
                                                :title="
                                                    column.truncate
                                                        ? displayCell(
                                                              row[column.key],
                                                              column
                                                          )
                                                        : undefined
                                                "
                                            >
                                                {{
                                                    displayCell(
                                                        row[column.key],
                                                        column
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
const SCORE_AXES = [
    { key: "match", label: "Match" },
    { key: "rms", label: "RMS" },
    { key: "shift_similarity", label: "Shift Similarity" },
    { key: "coupling_similarity", label: "Coupling Similarity" },
    { key: "intensity", label: "Intensity" },
];

const SHIFT_COLUMNS = [
    { key: "name", label: "Name", truncate: true },
    { key: "shift", label: "δ (ppm)", numeric: true, digits: 4 },
    { key: "spincount", label: "Spins", numeric: true, digits: 0 },
    { key: "nucleicount", label: "Nuclei", numeric: true, digits: 0 },
    { key: "line_shape", label: "Lineshape", truncate: true },
    { key: "lrms", label: "LRMS", numeric: true, digits: 3 },
];

const COUPLING_COLUMNS = [
    { key: "name", label: "Name", truncate: true },
    { key: "shift_from", label: "From", truncate: true },
    { key: "shift_to", label: "To", truncate: true },
    { key: "coupling", label: "J (Hz)", numeric: true, digits: 3 },
];

const LINESHAPE_COLUMNS = [
    { key: "name", label: "Name", truncate: true },
    { key: "line_width", label: "Width (Hz)", numeric: true, digits: 3 },
    { key: "gaussian", label: "Gaussian", numeric: true, digits: 4 },
];

const QMGI_COLUMNS = [
    { key: "name", label: "Name", truncate: true },
    { key: "rms", label: "RMS", numeric: true, digits: 3 },
    { key: "weight", label: "Weight", numeric: true, digits: 3 },
    { key: "range_min", label: "Range min", numeric: true, digits: 3 },
    { key: "range_max", label: "Range max", numeric: true, digits: 3 },
    { key: "over", label: "Over", numeric: true, digits: 2 },
    { key: "under", label: "Under", numeric: true, digits: 2 },
    { key: "orphan", label: "Orphan", numeric: true, digits: 2 },
];

export default {
    name: "HifsaScoresPanel",
    props: {
        hifsaData: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            size: 320,
            center: 160,
            radius: 105,
            labelRadius: 132,
            ringCount: 5,
            expandedSections: {
                spinsystems: true,
                chemical_shifts: false,
                couplings: false,
                lineshapes: false,
                qmgi: false,
            },
        };
    },
    computed: {
        scores() {
            return this.hifsaData?.scores ?? {};
        },
        remarks() {
            return this.hifsaData?.remarks ?? null;
        },
        url() {
            return this.hifsaData?.url ?? null;
        },
        solvent() {
            return this.hifsaData?.solvent ?? null;
        },
        temperature() {
            return this.hifsaData?.temperature ?? null;
        },
        createdLabel() {
            return this.formatActorTimestamp(this.hifsaData?.created);
        },
        modifiedLabel() {
            return this.formatActorTimestamp(this.hifsaData?.modified);
        },
        temperatureLabel() {
            if (this.temperature == null || this.temperature === "") {
                return "—";
            }

            const numeric = Number(this.temperature);

            if (!Number.isNaN(numeric) && Number.isFinite(numeric)) {
                return `${numeric} K`;
            }

            return String(this.temperature);
        },
        spinsystems() {
            return Array.isArray(this.hifsaData?.spinsystems)
                ? this.hifsaData.spinsystems
                : [];
        },
        chemicalShifts() {
            return Array.isArray(this.hifsaData?.chemical_shifts)
                ? this.hifsaData.chemical_shifts
                : [];
        },
        couplings() {
            return Array.isArray(this.hifsaData?.couplings)
                ? this.hifsaData.couplings
                : [];
        },
        lineshapes() {
            return Array.isArray(this.hifsaData?.lineshapes)
                ? this.hifsaData.lineshapes
                : [];
        },
        qmgi() {
            return Array.isArray(this.hifsaData?.qmgi)
                ? this.hifsaData.qmgi
                : [];
        },
        detailSections() {
            const sections = [];

            if (this.spinsystems.length) {
                sections.push({
                    id: "spinsystems",
                    kind: "spinsystems",
                    title: "Spin systems",
                    count: this.spinsystems.length,
                });
            }

            if (this.chemicalShifts.length) {
                sections.push({
                    id: "chemical_shifts",
                    kind: "grouped",
                    title: "Chemical shifts",
                    count: this.chemicalShifts.length,
                    columns: SHIFT_COLUMNS,
                    groups: this.groupBySpinSystem(this.chemicalShifts),
                });
            }

            if (this.couplings.length) {
                sections.push({
                    id: "couplings",
                    kind: "grouped",
                    title: "Coupling constants",
                    count: this.couplings.length,
                    columns: COUPLING_COLUMNS,
                    groups: this.groupBySpinSystem(this.couplings),
                });
            }

            if (this.lineshapes.length) {
                sections.push({
                    id: "lineshapes",
                    kind: "grouped",
                    title: "Lineshapes",
                    count: this.lineshapes.length,
                    columns: LINESHAPE_COLUMNS,
                    groups: this.groupBySpinSystem(this.lineshapes),
                });
            }

            if (this.qmgi.length) {
                sections.push({
                    id: "qmgi",
                    kind: "grouped",
                    title: "QMGI fit quality",
                    count: this.qmgi.length,
                    columns: QMGI_COLUMNS,
                    groups: this.groupBySpinSystem(this.qmgi),
                });
            }

            return sections;
        },
        scoreMetrics() {
            return SCORE_AXES.map((axis) => ({
                ...axis,
                value: this.scores[axis.key] ?? null,
            }));
        },
        axes() {
            return SCORE_AXES.map((_, index) =>
                this.pointAt(index, this.radius)
            );
        },
        rings() {
            return Array.from({ length: this.ringCount }, (_, index) => {
                const scale = (index + 1) / this.ringCount;

                return SCORE_AXES.map((_, axisIndex) => {
                    const point = this.pointAt(axisIndex, this.radius * scale);

                    return `${point.x},${point.y}`;
                }).join(" ");
            });
        },
        valuePoints() {
            return SCORE_AXES.map((axis, index) => {
                const raw = this.scores[axis.key];
                const value =
                    typeof raw === "number" && Number.isFinite(raw)
                        ? Math.min(Math.max(raw, 0), 1)
                        : 0;

                return this.pointAt(index, this.radius * value);
            });
        },
        valuePolygon() {
            return this.valuePoints
                .map((point) => `${point.x},${point.y}`)
                .join(" ");
        },
        labels() {
            return SCORE_AXES.map((axis, index) => {
                const point = this.pointAt(index, this.labelRadius);

                return {
                    text: axis.label,
                    x: point.x,
                    y: point.y,
                };
            });
        },
        ariaLabel() {
            const parts = this.scoreMetrics.map(
                (metric) => `${metric.label} ${this.formatScore(metric.value)}`
            );

            return `HiFSA score radar: ${parts.join(", ")}`;
        },
    },
    methods: {
        toggleSection(id) {
            this.expandedSections[id] = !this.expandedSections[id];
        },
        groupBySpinSystem(rows) {
            const groups = new Map();

            for (const row of rows) {
                const name = row.spin_system || "Unknown";

                if (!groups.has(name)) {
                    groups.set(name, []);
                }

                groups.get(name).push(row);
            }

            return Array.from(groups.entries()).map(([name, groupRows]) => ({
                name,
                rows: groupRows,
            }));
        },
        pointAt(index, distance) {
            const angle =
                -Math.PI / 2 + (index * 2 * Math.PI) / SCORE_AXES.length;

            return {
                x: this.center + distance * Math.cos(angle),
                y: this.center + distance * Math.sin(angle),
            };
        },
        formatScore(value) {
            if (typeof value !== "number" || !Number.isFinite(value)) {
                return "—";
            }

            return String(Math.round(value * 100));
        },
        formatPercent(value) {
            if (typeof value !== "number" || !Number.isFinite(value)) {
                return "—";
            }

            return `${(value * 100).toFixed(2)}%`;
        },
        formatNumber(value, digits = 3) {
            if (typeof value !== "number" || !Number.isFinite(value)) {
                return "—";
            }

            return value.toFixed(digits);
        },
        formatActorTimestamp(entry) {
            if (!entry || typeof entry !== "object") {
                return null;
            }

            const by = entry.by || null;
            const at = entry.at || null;

            if (!by && !at) {
                return null;
            }

            if (by && at) {
                return `${by} · ${at}`;
            }

            return by || at;
        },
        displayCell(value, column) {
            if (column.numeric) {
                return this.formatNumber(value, column.digits ?? 3);
            }

            if (value == null || value === "") {
                return "—";
            }

            return String(value);
        },
    },
};
</script>
