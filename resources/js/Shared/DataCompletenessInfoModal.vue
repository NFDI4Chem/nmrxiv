<template>
    <jet-dialog-modal :show="show" max-width="2xl" @close="$emit('close')">
        <template #title> How is data completeness scored? </template>

        <template #content>
            <div
                class="max-h-[min(70vh,32rem)] space-y-5 overflow-y-auto pr-1 text-sm leading-relaxed text-gray-700 dark:text-slate-300"
            >
                <p>
                    Stars measure how complete the
                    <strong>publicly available NMR evidence</strong> is for a
                    compound's constitution. They are not a measure of spectral
                    quality or of whether the proposed structure is correct.
                </p>

                <section v-if="rubric">
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Tiers
                    </h3>
                    <ol class="mt-2 space-y-2">
                        <li
                            v-for="(tier, level) in sortedTiers"
                            :key="level"
                            class="rounded-md border border-gray-100 bg-gray-50/80 px-3 py-2 dark:border-gray-700 dark:bg-slate-900/40"
                        >
                            <span
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                {{ level }} ★ — {{ tier.label }}
                            </span>
                            <p
                                class="mt-0.5 text-xs text-gray-600 dark:text-slate-400"
                            >
                                Requires:
                                {{ formatRequires(tier.requires) }}
                            </p>
                        </li>
                    </ol>
                </section>

                <section v-if="bonusLabels.length > 0">
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Bonus badges
                    </h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">
                        {{ bonusLabels.join(", ") }} — shown when present, but
                        not required for any tier.
                    </p>
                </section>

                <p class="text-xs text-gray-500 dark:text-slate-500">
                    Rubric version {{ rubric?.version ?? "—" }}.
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

export default {
    name: "DataCompletenessInfoModal",
    components: {
        JetDialogModal,
    },
    props: {
        show: {
            type: Boolean,
            default: false,
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
        sortedTiers() {
            const tiers = this.rubric?.tiers ?? {};
            const entries = Object.entries(tiers).map(([k, v]) => [
                Number(k),
                v,
            ]);
            entries.sort((a, b) => a[0] - b[0]);

            return Object.fromEntries(entries);
        },
        bonusLabels() {
            const bonuses = this.rubric?.bonuses ?? [];
            const labels = this.rubric?.criteria ?? {};

            return bonuses.map((key) => labels[key] ?? key);
        },
    },
    methods: {
        formatRequires(requires) {
            if (!Array.isArray(requires)) {
                return "—";
            }

            return requires
                .map((item) => {
                    if (Array.isArray(item)) {
                        return item
                            .map((k) => this.rubric?.criteria?.[k] ?? k)
                            .join(" or ");
                    }

                    return this.rubric?.criteria?.[item] ?? item;
                })
                .join(", ");
        },
    },
};
</script>
