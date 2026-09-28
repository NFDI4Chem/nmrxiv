<template>
    <div class="quickcheck-report space-y-5 text-sm">
        <div
            class="flex flex-wrap items-start justify-between gap-3 rounded-lg border px-4 py-3"
            :class="verdictStyle.box"
        >
            <div class="min-w-0">
                <p class="text-base font-semibold" :class="verdictStyle.text">
                    {{ verdictStyle.title }}
                </p>
                <p class="mt-0.5 text-xs text-gray-600 dark:text-slate-300">
                    {{ verdictStyle.hint }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span
                    v-for="nucleus in nuclei"
                    :key="'mark-' + nucleus"
                    class="rounded-full bg-white/80 px-2.5 py-1 text-xs font-semibold text-gray-800 ring-1 ring-gray-200 dark:bg-slate-900/60 dark:text-slate-100 dark:ring-slate-700"
                >
                    {{ nucleusLabel(nucleus) }}
                    {{ report.reports[nucleus].mark }}/10
                    <span class="font-normal text-gray-500 dark:text-slate-400"
                        >· {{ report.reports[nucleus].result }}</span
                    >
                </span>
                <button
                    v-if="printable"
                    type="button"
                    class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 print:hidden dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    @click="print"
                >
                    Print / save as PDF
                </button>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div class="flex min-w-0 flex-col gap-3">
                <div
                    v-if="structureUrl && !structureFailed"
                    class="relative h-72 rounded-lg bg-white sm:h-96 lg:h-auto lg:min-h-[20rem] lg:flex-1"
                >
                    <img
                        :src="highlightedUrl || structureUrl"
                        alt="Structure with the author's atom labels"
                        class="absolute inset-0 h-full w-full object-contain"
                        @error="structureFailed = true"
                    />
                </div>
                <div
                    class="space-y-2 text-xs text-gray-600 empty:hidden dark:text-slate-300"
                >
                    <p
                        v-if="
                            structureUrl &&
                            !structureFailed &&
                            structure.flagged.length
                        "
                    >
                        Highlighted atoms need review or do not fit the
                        prediction.
                    </p>
                    <p
                        v-if="
                            report.assignment_check.offset
                                ?.suspected_referencing_error
                        "
                        class="rounded-md bg-amber-50 px-2 py-1.5 text-amber-900 ring-1 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-100 dark:ring-amber-900/50"
                    >
                        All shifts are offset from the prediction by a similar
                        amount ({{ offsetText }}). Check the spectrum
                        referencing.
                    </p>
                </div>
            </div>

            <section
                class="flex max-h-[28rem] min-w-0 flex-col overflow-hidden rounded-lg border border-gray-200 dark:border-slate-700 lg:max-h-[36rem]"
            >
                <header
                    class="flex flex-wrap items-baseline justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800/60"
                >
                    <h4 class="font-semibold text-gray-900 dark:text-slate-100">
                        Quality report
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-slate-300">
                        Overall mark (1 to 10) per nucleus
                    </p>
                </header>
                <div class="min-h-0 flex-1 overflow-auto">
                    <table
                        class="min-w-full text-xs"
                        @mouseleave="clearHighlight"
                    >
                        <thead
                            class="sticky top-0 z-10 bg-gray-50 shadow-[0_1px_0_0] shadow-gray-200 dark:bg-slate-800 dark:shadow-slate-700"
                        >
                            <tr
                                class="text-left font-medium text-gray-600 dark:text-slate-300"
                            >
                                <th class="px-3 py-2">Atom</th>
                                <th class="px-3 py-2 text-right">δ (ppm)</th>
                                <th class="px-3 py-2 text-right">Predicted</th>
                                <th class="px-3 py-2 text-right">Deviation</th>
                                <th class="px-3 py-2 text-right">Spheres</th>
                                <th class="px-3 py-2">HOSE code</th>
                            </tr>
                        </thead>
                        <tbody
                            v-for="nucleus in nuclei"
                            :key="'report-' + nucleus"
                            class="divide-y divide-gray-100 dark:divide-slate-800"
                        >
                            <tr
                                class="border-t border-gray-200 bg-gray-100 dark:border-slate-700 dark:bg-slate-800/80"
                            >
                                <th
                                    colspan="6"
                                    class="px-3 py-1.5 text-left font-normal"
                                >
                                    <div
                                        class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-0.5"
                                    >
                                        <span
                                            class="font-semibold text-gray-900 dark:text-slate-100"
                                        >
                                            {{ nucleusLabel(nucleus) }} · mark
                                            {{
                                                report.reports[nucleus].mark
                                            }}/10
                                            <span
                                                class="font-normal text-gray-500 dark:text-slate-400"
                                                >·
                                                {{
                                                    report.reports[nucleus]
                                                        .result
                                                }}</span
                                            >
                                        </span>
                                        <span
                                            class="text-[11px] text-gray-500 dark:text-slate-400"
                                        >
                                            Mean deviation
                                            {{
                                                report.reports[nucleus]
                                                    .penalties.mean_deviation
                                                    .ppm
                                            }}
                                            ppm (−{{
                                                report.reports[nucleus]
                                                    .penalties.mean_deviation
                                                    .points
                                            }}) · red or missing
                                            {{
                                                report.reports[nucleus]
                                                    .penalties.red_or_missing
                                                    .count
                                            }}
                                            (−{{
                                                report.reports[nucleus]
                                                    .penalties.red_or_missing
                                                    .points
                                            }}) · yellow
                                            {{
                                                report.reports[nucleus]
                                                    .penalties.yellow.count
                                            }}
                                            (−{{
                                                report.reports[nucleus]
                                                    .penalties.yellow.points
                                            }})
                                        </span>
                                    </div>
                                </th>
                            </tr>
                            <tr
                                v-for="(row, index) in report.reports[nucleus]
                                    .atoms"
                                :key="nucleus + '-atom-' + index"
                                :class="[atomRowClass(row.status), hoverClass]"
                                @mouseenter="highlightRow(nucleus, row.atoms)"
                            >
                                <td
                                    class="whitespace-nowrap px-3 py-1.5 font-medium"
                                >
                                    {{ row.label }}
                                    <span
                                        v-if="
                                            row.status === 'missing' ||
                                            row.status === 'impossible'
                                        "
                                        class="ml-1 font-normal text-gray-500 dark:text-slate-400"
                                        >({{
                                            row.status === "missing"
                                                ? "missing"
                                                : "no prediction"
                                        }})</span
                                    >
                                </td>
                                <td class="px-3 py-1.5 text-right tabular-nums">
                                    {{ formatShift(row.observed, nucleus) }}
                                </td>
                                <td class="px-3 py-1.5 text-right tabular-nums">
                                    {{ formatShift(row.predicted, nucleus) }}
                                </td>
                                <td class="px-3 py-1.5 text-right tabular-nums">
                                    {{ formatShift(row.deviation, nucleus) }}
                                </td>
                                <td class="px-3 py-1.5 text-right tabular-nums">
                                    {{ row.spheres || "—" }}
                                </td>
                                <td
                                    class="max-w-[12rem] truncate px-3 py-1.5 font-mono text-[11px] text-gray-500 dark:text-slate-400"
                                    :title="row.hose_code || ''"
                                >
                                    {{ row.hose_code || "—" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section
            class="overflow-hidden rounded-lg border border-gray-200 dark:border-slate-700"
        >
            <header
                class="flex flex-wrap items-baseline justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800/60"
            >
                <h4 class="font-semibold text-gray-900 dark:text-slate-100">
                    Assignment check
                </h4>
                <span
                    class="rounded-full px-2 py-0.5 text-xs font-semibold"
                    :class="assignmentResultClass"
                    >{{ assignmentResultLabel }}</span
                >
            </header>
            <ul
                v-if="
                    report.assignment_check.suggestions.length ||
                    report.assignment_check.issues.length
                "
                class="mx-4 mt-3 space-y-1.5 text-xs"
            >
                <li
                    v-for="(suggestion, index) in report.assignment_check
                        .suggestions"
                    :key="'swap-' + index"
                    class="rounded-md bg-red-50 px-2 py-1.5 text-red-900 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-100 dark:ring-red-900/50"
                >
                    Possible interchange of
                    <strong>{{ suggestion.labels[0] }}</strong> and
                    <strong>{{ suggestion.labels[1] }}</strong
                    >: swapping them reduces the deviation by
                    {{
                        formatShift(
                            suggestion.error_reduction,
                            suggestion.nucleus
                        )
                    }}
                    ppm.
                </li>
                <li
                    v-for="(issue, index) in report.assignment_check.issues"
                    :key="'issue-' + index"
                    class="rounded-md bg-amber-50 px-2 py-1.5 text-amber-900 ring-1 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-100 dark:ring-amber-900/50"
                >
                    <span class="font-medium"
                        >{{ nucleusLabel(issue.nucleus) }}:</span
                    >
                    {{ issue.message }}
                    <span
                        v-if="issue.labels.length"
                        class="text-amber-800 dark:text-amber-200"
                        >({{ issue.labels.join(", ") }})</span
                    >
                </li>
            </ul>

            <div class="mt-3 overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 text-xs dark:divide-slate-700"
                    @mouseleave="clearHighlight"
                >
                    <thead class="bg-gray-50 dark:bg-slate-800/60">
                        <tr
                            class="text-left font-medium text-gray-600 dark:text-slate-300"
                        >
                            <th class="px-3 py-2">Assignment</th>
                            <th class="px-3 py-2">Nucleus</th>
                            <th class="px-3 py-2 text-right">Observed</th>
                            <th class="px-3 py-2 text-right">Predicted</th>
                            <th class="px-3 py-2 text-right">Δ</th>
                            <th class="px-3 py-2 text-right">Spheres</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-slate-800"
                    >
                        <tr
                            v-for="(row, index) in report.assignment_check.rows"
                            :key="'row-' + index"
                            class="text-gray-800 dark:text-slate-200"
                            :class="hoverClass"
                            @mouseenter="highlightRow(row.nucleus, row.atoms)"
                        >
                            <td
                                class="whitespace-nowrap px-3 py-1.5 font-medium"
                            >
                                {{ row.label }}
                            </td>
                            <td class="px-3 py-1.5">
                                {{ nucleusLabel(row.nucleus) }}
                            </td>
                            <td class="px-3 py-1.5 text-right tabular-nums">
                                {{ formatShift(row.observed, row.nucleus) }}
                            </td>
                            <td class="px-3 py-1.5 text-right tabular-nums">
                                {{ formatShift(row.predicted, row.nucleus) }}
                            </td>
                            <td class="px-3 py-1.5 text-right tabular-nums">
                                {{ formatSigned(row.delta, row.nucleus) }}
                            </td>
                            <td class="px-3 py-1.5 text-right tabular-nums">
                                {{ row.spheres ?? "—" }}
                            </td>
                            <td class="px-3 py-1.5">
                                <span
                                    class="rounded-full px-2 py-0.5 font-semibold"
                                    :class="rowStatusClass(row.status)"
                                    >{{ rowStatusLabel(row.status) }}</span
                                >
                                <span
                                    v-for="reason in row.reasons"
                                    :key="reason"
                                    class="ml-1 text-[11px] text-gray-500 dark:text-slate-400"
                                    >{{ reasonLabel(reason) }}</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p
                v-if="report.adjustments && report.adjustments.length"
                class="px-4 py-2 text-[11px] text-gray-500 dark:text-slate-400"
            >
                {{ report.adjustments.length }} identical shift(s) on different
                atoms were sent 0.001 ppm apart so that nmrshiftdb2 keeps them
                as separate signals.
            </p>
        </section>
    </div>
</template>

<script>
import {
    quickcheckCxsmiles,
    quickcheckDepictionUrl,
} from "@/Utils/quickcheck.js";
import { printQuickcheckReport } from "@/Utils/quickcheckReportDocument.js";

const NUCLEUS_LABELS = { "13C": "¹³C", "1H": "¹H" };

const VERDICTS = {
    accept: {
        title: "Assignments fit the predicted spectra",
        hint: "Shift list and atom assignments agree with nmrshiftdb2.",
        box: "border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/30",
        text: "text-emerald-900 dark:text-emerald-100",
    },
    review: {
        title: "Some assignments need a second look",
        hint: "Check the highlighted atoms, ideally against 2D correlations.",
        box: "border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/30",
        text: "text-amber-900 dark:text-amber-100",
    },
    reject: {
        title: "Assignments disagree with the predicted spectra",
        hint: "Wrong structure, misassigned atoms or referencing problems are common causes.",
        box: "border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/30",
        text: "text-red-900 dark:text-red-100",
    },
    not_assessable: {
        title: "Assignments could not be assessed",
        hint: "No atom could be compared with a prediction.",
        box: "border-gray-200 bg-gray-50 dark:border-slate-700 dark:bg-slate-800/60",
        text: "text-gray-900 dark:text-slate-100",
    },
};

const REASONS = {
    low_confidence_prediction: "low-confidence prediction",
    diastereotopic_pair_mean: "CH₂ pair mean",
};

export default {
    props: {
        report: {
            type: Object,
            required: true,
        },
        molfile: {
            type: String,
            default: "",
        },
        printable: {
            type: Boolean,
            default: true,
        },
        details: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            structureFailed: false,
            highlightedUrl: "",
            highlightTarget: "",
        };
    },
    computed: {
        nuclei() {
            return ["13C", "1H"].filter(
                (nucleus) => this.report.reports?.[nucleus]
            );
        },
        verdictStyle() {
            return VERDICTS[this.report.verdict] || VERDICTS.not_assessable;
        },
        structure() {
            return quickcheckCxsmiles(this.molfile, this.report);
        },
        structureUrl() {
            const cmApi = this.$page?.props?.CM_API;
            if (!this.structure || !cmApi) {
                return "";
            }
            return quickcheckDepictionUrl(
                cmApi,
                this.structure.cxsmiles,
                this.structure.flagged
            );
        },
        canHighlight() {
            return Boolean(this.structureUrl && !this.structureFailed);
        },
        hoverClass() {
            return this.canHighlight
                ? "hover:outline hover:outline-1 hover:-outline-offset-1 hover:outline-sky-400"
                : "";
        },
        offsetText() {
            const offset = this.report.assignment_check.offset || {};
            return ["13C", "1H"]
                .filter((nucleus) => typeof offset[nucleus] === "number")
                .map(
                    (nucleus) =>
                        `${this.nucleusLabel(nucleus)} ${this.formatSigned(
                            offset[nucleus],
                            nucleus
                        )} ppm`
                )
                .join(", ");
        },
        assignmentResultLabel() {
            return (
                {
                    consistent: "Consistent",
                    review: "Review",
                    inconsistent: "Inconsistent",
                    not_assessable: "Not assessable",
                }[this.report.assignment_check.result] ||
                this.report.assignment_check.result
            );
        },
        assignmentResultClass() {
            return this.rowStatusClass(
                {
                    consistent: "ok",
                    review: "review",
                    inconsistent: "fail",
                }[this.report.assignment_check.result] || "not_assessable"
            );
        },
    },
    watch: {
        structureUrl() {
            this.preloaded = false;
            this.clearHighlight();
        },
    },
    created() {
        this.loadedHighlights = new Set();
    },
    methods: {
        highlightUrl(nucleus, atoms) {
            return quickcheckDepictionUrl(
                this.$page.props.CM_API,
                this.structure.cxsmiles,
                this.structure.highlightIds(nucleus, atoms)
            );
        },
        loadHighlight(url, onLoad) {
            if (this.loadedHighlights.has(url)) {
                onLoad?.();
                return;
            }
            const image = new Image();
            image.onload = () => {
                this.loadedHighlights.add(url);
                onLoad?.();
            };
            image.src = url;
        },
        preloadHighlights() {
            if (!this.canHighlight || this.preloaded) {
                return;
            }
            this.preloaded = true;
            const rows = [
                ...this.nuclei.flatMap((nucleus) =>
                    this.report.reports[nucleus].atoms.map((row) => [
                        nucleus,
                        row.atoms,
                    ])
                ),
                ...(this.report.assignment_check.rows || []).map((row) => [
                    row.nucleus,
                    row.atoms,
                ]),
            ];
            new Set(
                rows.map(([nucleus, atoms]) =>
                    this.highlightUrl(nucleus, atoms)
                )
            ).forEach((url) => this.loadHighlight(url));
        },
        highlightRow(nucleus, atoms) {
            if (!this.canHighlight || !atoms?.length) {
                return;
            }
            this.preloadHighlights();
            const url = this.highlightUrl(nucleus, atoms);
            this.highlightTarget = url;
            this.loadHighlight(url, () => {
                if (this.highlightTarget === url) {
                    this.highlightedUrl = url;
                }
            });
        },
        clearHighlight() {
            this.highlightTarget = "";
            this.highlightedUrl = "";
        },
        nucleusLabel(nucleus) {
            return NUCLEUS_LABELS[nucleus] || nucleus;
        },
        formatShift(value, nucleus) {
            if (value === null || value === undefined) {
                return "—";
            }
            return Number(value).toFixed(nucleus === "1H" ? 2 : 1);
        },
        formatSigned(value, nucleus) {
            if (value === null || value === undefined) {
                return "—";
            }
            const text = this.formatShift(Math.abs(value), nucleus);
            if (Number(text) === 0) {
                return text;
            }
            return value < 0 ? `−${text}` : `+${text}`;
        },
        atomRowClass(status) {
            return {
                green: "bg-emerald-50/70 text-emerald-950 dark:bg-emerald-950/20 dark:text-emerald-100",
                yellow: "bg-amber-50 text-amber-950 dark:bg-amber-950/30 dark:text-amber-100",
                red: "bg-red-50 text-red-950 dark:bg-red-950/30 dark:text-red-100",
                missing:
                    "bg-red-50/60 text-red-950 dark:bg-red-950/20 dark:text-red-100",
                impossible: "text-gray-500 dark:text-slate-400",
            }[status];
        },
        rowStatusClass(status) {
            return {
                ok: "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200",
                review: "bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200",
                fail: "bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200",
                not_assessable:
                    "bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-300",
            }[status];
        },
        rowStatusLabel(status) {
            return (
                {
                    ok: "fits",
                    review: "review",
                    fail: "does not fit",
                    not_assessable: "not assessable",
                }[status] || status
            );
        },
        reasonLabel(reason) {
            return REASONS[reason] || reason.replace(/_/g, " ");
        },
        print() {
            printQuickcheckReport({
                report: this.report,
                molfile: this.molfile,
                structureUrl: this.structureFailed ? "" : this.structureUrl,
                hasFlaggedAtoms: Boolean(this.structure?.flagged.length),
                details: this.details,
                logoUrl: `${window.location.origin}/img/logo.svg`,
            });
        },
    },
};
</script>
