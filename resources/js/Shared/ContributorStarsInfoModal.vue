<template>
    <jet-dialog-modal :show="show" max-width="2xl" @close="$emit('close')">
        <template #title> How contributor stars are scored </template>

        <template #content>
            <div
                class="max-h-[min(70vh,32rem)] space-y-5 overflow-y-auto pr-1 text-sm leading-relaxed text-gray-700 dark:text-slate-300"
            >
                <p>
                    These stars are for the workspace. A compound counts only
                    when
                    <strong>this workspace</strong> has published a full NMR set
                    for it (¹H, ¹³C, COSY or TOCSY, HSQC, and HMBC).
                </p>

                <section
                    class="rounded-md border border-gray-200 px-3 py-3 dark:border-gray-700"
                >
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        This library
                    </h3>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <DataCompletenessBadge
                            :tier="normalizedStars"
                            variant="compact"
                            force-stars
                        />
                        <span
                            class="tabular-nums text-gray-700 dark:text-slate-200"
                        >
                            {{ formatCount(countingCompounds) }}
                            {{
                                countingCompounds === 1
                                    ? "compound"
                                    : "compounds"
                            }}
                            with a full NMR set
                        </span>
                    </div>
                    <p
                        v-if="atTop"
                        class="mt-2 text-xs text-gray-500 dark:text-slate-400"
                    >
                        This library has reached the top.
                    </p>
                    <p
                        v-else-if="nextLevel"
                        class="mt-2 text-xs text-gray-500 dark:text-slate-400"
                    >
                        {{ formatCount(nextLevel.needed) }} more to reach
                        {{ nextLevel.stars }}
                        {{ nextLevel.stars === 1 ? "star" : "stars" }}.
                    </p>
                </section>

                <section v-if="thresholdRows.length > 0">
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Thresholds
                    </h3>
                    <ol class="mt-2 space-y-2">
                        <li
                            v-for="row in thresholdRows"
                            :key="row.stars"
                            :class="[
                                row.current
                                    ? 'border-indigo-200 bg-indigo-50/80 ring-1 ring-indigo-200 dark:border-indigo-500/40 dark:bg-indigo-950/40 dark:ring-indigo-500/30'
                                    : 'border-gray-100 bg-gray-50/80 dark:border-gray-700 dark:bg-slate-900/40',
                                'rounded-md border px-3 py-2',
                            ]"
                        >
                            <span
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                {{ row.stars }} ★ —
                                {{ formatCount(row.compounds) }}
                                {{
                                    row.compounds === 1
                                        ? "compound"
                                        : "compounds"
                                }}
                                with a full NMR set
                            </span>
                            <span
                                v-if="row.current"
                                class="ml-2 text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-300"
                            >
                                Current
                            </span>
                        </li>
                    </ol>
                </section>

                <p class="text-xs text-gray-500 dark:text-slate-500">
                    Each compound still has its own stars on the compound page.
                    <a
                        v-if="docsUrl"
                        :href="docsUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                    >
                        Read the full guide
                    </a>
                </p>
            </div>
        </template>

        <template #footer>
            <button
                type="button"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                @click="$emit('close')"
            >
                Close
            </button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import { usePage } from "@inertiajs/vue3";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import DataCompletenessBadge from "@/Shared/DataCompletenessBadge.vue";

export default {
    name: "ContributorStarsInfoModal",
    components: {
        JetDialogModal,
        DataCompletenessBadge,
    },
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        quality: {
            type: Object,
            default: null,
        },
    },
    emits: ["close"],
    computed: {
        rubric() {
            return usePage().props.qualityRubric ?? null;
        },
        docsUrl() {
            return this.rubric?.docs_url || null;
        },
        normalizedStars() {
            const n = Number(this.quality?.stars);
            if (!Number.isFinite(n) || n < 0) {
                return 0;
            }

            return Math.min(5, Math.floor(n));
        },
        countingCompounds() {
            const n = Number(this.quality?.high_quality_compounds);

            return Number.isFinite(n) && n > 0 ? Math.floor(n) : 0;
        },
        nextLevel() {
            const next = this.quality?.next_level;
            if (!next || this.atTop) {
                return null;
            }

            const stars = Number(next.stars);
            const needed = Number(next.needed);
            if (!Number.isFinite(stars) || !Number.isFinite(needed)) {
                return null;
            }

            return {
                stars: Math.floor(stars),
                needed: Math.max(0, Math.floor(needed)),
            };
        },
        thresholdRows() {
            const thresholds = this.rubric?.contributor?.thresholds ?? {};

            return Object.entries(thresholds)
                .map(([stars, compounds]) => ({
                    stars: Number(stars),
                    compounds: Number(compounds),
                }))
                .filter(
                    (row) =>
                        Number.isFinite(row.stars) &&
                        row.stars > 0 &&
                        Number.isFinite(row.compounds)
                )
                .sort((a, b) => a.stars - b.stars)
                .map((row) => ({
                    ...row,
                    current:
                        row.stars === this.normalizedStars &&
                        this.normalizedStars > 0,
                }));
        },
        atTop() {
            const levels = this.thresholdRows.map((row) => row.stars);
            if (levels.length === 0) {
                return false;
            }

            return this.normalizedStars >= Math.max(...levels);
        },
    },
    methods: {
        formatCount(value) {
            return new Intl.NumberFormat().format(Number(value) || 0);
        },
    },
};
</script>
