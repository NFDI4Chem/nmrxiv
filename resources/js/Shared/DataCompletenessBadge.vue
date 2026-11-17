<template>
    <div
        :class="[
            variant === 'detailed' ? 'space-y-3' : 'inline-flex items-center',
        ]"
    >
        <div
            class="inline-flex items-center gap-1.5"
            :title="tooltipText"
            :aria-label="ariaLabel"
        >
            <template v-if="normalizedTier === 0 && !forceStars">
                <span
                    class="text-xs font-medium text-gray-500 dark:text-gray-400"
                >
                    Not yet rated
                </span>
            </template>
            <template v-else>
                <svg
                    v-for="index in activeStars"
                    :key="'y-' + index"
                    class="h-4 w-4 flex-shrink-0 text-amber-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                        clip-rule="evenodd"
                    />
                </svg>
                <svg
                    v-for="index in inactiveStars"
                    :key="'n-' + index"
                    class="h-4 w-4 flex-shrink-0 text-gray-200 dark:text-gray-600"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"
                        clip-rule="evenodd"
                    />
                </svg>
                <span
                    v-if="variant === 'detailed' && tierLabel"
                    class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-200"
                >
                    {{ tierLabel }}
                </span>
            </template>
        </div>

        <div v-if="variant === 'detailed'" class="space-y-2">
            <ul
                v-if="criterionChips.length > 0"
                class="flex flex-wrap gap-1.5"
                aria-label="Data completeness checklist"
            >
                <li
                    v-for="chip in criterionChips"
                    :key="chip.key"
                    :class="[
                        chip.met
                            ? 'bg-teal-50 text-teal-800 ring-teal-600/20 dark:bg-teal-900/30 dark:text-teal-200 dark:ring-teal-500/30'
                            : 'bg-gray-50 text-gray-500 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-600/30',
                        'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset',
                    ]"
                >
                    {{ chip.label }}
                </li>
            </ul>
            <ul
                v-if="bonusChips.length > 0"
                class="flex flex-wrap gap-1.5"
                aria-label="Bonus experiments"
            >
                <li
                    v-for="chip in bonusChips"
                    :key="'b-' + chip.key"
                    :class="[
                        chip.met
                            ? 'bg-indigo-50 text-indigo-800 ring-indigo-600/20 dark:bg-indigo-900/30 dark:text-indigo-200 dark:ring-indigo-500/30'
                            : 'bg-gray-50 text-gray-400 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-600/30',
                        'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset',
                    ]"
                >
                    {{ chip.label }}
                    <span
                        class="ml-1 text-[10px] uppercase tracking-wide opacity-70"
                        >bonus</span
                    >
                </li>
            </ul>
            <p
                v-if="nextTierHint"
                class="text-xs text-gray-500 dark:text-gray-400"
            >
                {{ nextTierHint }}
            </p>
        </div>
    </div>
</template>

<script>
import { usePage } from "@inertiajs/vue3";

export default {
    name: "DataCompletenessBadge",
    props: {
        tier: {
            type: [Number, String],
            default: 0,
        },
        breakdown: {
            type: Object,
            default: null,
        },
        variant: {
            type: String,
            default: "compact",
            validator: (v) => ["compact", "detailed"].includes(v),
        },
        /** Always show empty stars instead of "Not yet rated" (contributor panels). */
        forceStars: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        rubric() {
            return usePage().props.qualityRubric ?? null;
        },
        normalizedTier() {
            const n = Number(this.tier);
            if (!Number.isFinite(n) || n < 0) {
                return 0;
            }

            return Math.min(5, Math.floor(n));
        },
        activeStars() {
            return this.normalizedTier;
        },
        inactiveStars() {
            return Math.max(0, 5 - this.normalizedTier);
        },
        tierLabel() {
            return (
                this.breakdown?.tier_label ??
                this.rubric?.tiers?.[this.normalizedTier]?.label ??
                null
            );
        },
        ariaLabel() {
            if (this.normalizedTier === 0) {
                return "Data completeness: not yet rated";
            }

            const label = this.tierLabel ?? `${this.normalizedTier} of 5`;

            return `Data completeness: ${this.normalizedTier} of 5 — ${label}`;
        },
        tooltipText() {
            if (this.normalizedTier === 0) {
                return "Not yet rated — no public spectra scored for this compound.";
            }

            const missing = this.breakdown?.next_tier_missing ?? [];
            if (missing.length === 0) {
                return this.ariaLabel;
            }

            const labels = missing.map((key) => this.criterionLabel(key));

            return `${this.ariaLabel}. Next tier needs: ${labels.join(", ")}.`;
        },
        criterionChips() {
            const criteria = this.breakdown?.criteria;
            if (!criteria || typeof criteria !== "object") {
                return [];
            }

            return Object.entries(criteria).map(([key, met]) => ({
                key,
                label: this.criterionLabel(key),
                met: Boolean(met),
            }));
        },
        bonusChips() {
            const bonuses = this.breakdown?.bonuses;
            if (!bonuses || typeof bonuses !== "object") {
                return [];
            }

            return Object.entries(bonuses).map(([key, met]) => ({
                key,
                label: this.criterionLabel(key),
                met: Boolean(met),
            }));
        },
        nextTierHint() {
            const missing = this.breakdown?.next_tier_missing ?? [];
            if (missing.length === 0 || this.normalizedTier >= 5) {
                return null;
            }

            const labels = missing.map((key) => this.criterionLabel(key));
            const nextLabel =
                this.rubric?.tiers?.[this.normalizedTier + 1]?.label ??
                "the next tier";

            return `What's needed for ${nextLabel}: ${labels.join(", ")}.`;
        },
    },
    methods: {
        criterionLabel(key) {
            const parts = String(key).split("|");
            if (parts.length > 1) {
                return parts.map((p) => this.criterionLabel(p)).join(" or ");
            }

            return this.rubric?.criteria?.[key] ?? key;
        },
    },
};
</script>
