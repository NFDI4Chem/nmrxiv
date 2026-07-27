<template>
    <div
        v-if="composition && components.length > 0"
        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-900/5 dark:border-gray-600 dark:bg-slate-900/50 dark:ring-white/5"
    >
        <div
            class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-4 py-3 dark:border-gray-700"
        >
            <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-100">
                Mixture composition
            </h3>
            <span
                v-if="composition.spectrum_verifiable"
                class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 dark:text-emerald-300"
            >
                <ShieldCheckIcon class="h-3.5 w-3.5" aria-hidden="true" />
                Spectrum-verifiable
            </span>
        </div>

        <div class="px-4 py-3">
            <p class="mb-3 text-xs text-gray-500 dark:text-slate-400">
                {{ basisDisplayLabel(composition.basis) }}
                <template v-if="composition.determination_method_label">
                    · {{ composition.determination_method_label }}
                </template>
                <template v-if="composition.nucleus">
                    · {{ composition.nucleus }}
                </template>
            </p>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm">
                    <thead>
                        <tr
                            class="border-b border-gray-200 dark:border-gray-700"
                        >
                            <th
                                class="py-2 pr-2 text-left text-xs font-medium text-gray-500 dark:text-slate-400"
                            >
                                Component
                            </th>
                            <th
                                class="px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-slate-400"
                            >
                                {{ basisUnitLabel(composition.basis) }}
                            </th>
                            <th
                                v-if="showSignalColumn"
                                class="py-2 pl-2 text-left text-xs font-medium text-gray-500 dark:text-slate-400"
                            >
                                Signal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="component in components"
                            :key="component.id ?? component.molecule_id"
                            class="border-b border-gray-100 dark:border-gray-800"
                        >
                            <td class="py-2.5 pr-2 align-top">
                                <div
                                    class="font-medium text-gray-900 dark:text-slate-100"
                                >
                                    {{ componentLabel(component) }}
                                </div>
                            </td>
                            <td
                                class="px-2 py-2.5 text-right align-top font-medium tabular-nums text-gray-900 dark:text-slate-100"
                            >
                                {{ formatMixtureValue(component.value) }}
                            </td>
                            <td
                                v-if="showSignalColumn"
                                class="py-2.5 pl-2 align-top text-xs text-gray-600 dark:text-slate-300"
                            >
                                <template v-if="component.integrated_signal">
                                    {{ component.integrated_signal }}
                                    <span
                                        v-if="component.n_nuclei"
                                        class="text-gray-400"
                                    >
                                        (N={{ component.n_nuclei }})
                                    </span>
                                </template>
                                <span
                                    v-else
                                    class="text-gray-300 dark:text-slate-600"
                                    >—</span
                                >
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr
                            class="border-t border-gray-200 dark:border-gray-600"
                        >
                            <td
                                class="py-2.5 pr-2 font-medium text-gray-900 dark:text-slate-100"
                            >
                                Total
                            </td>
                            <td
                                class="px-2 py-2.5 text-right font-medium tabular-nums"
                                :class="totalValueClass"
                            >
                                {{ formatMixtureValue(total) }}
                                {{ basisUnitLabel(composition.basis) }}
                            </td>
                            <td v-if="showSignalColumn" />
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p
                v-if="composition.relaxation_delay_s != null"
                class="mt-2 text-xs text-gray-500 dark:text-slate-400"
            >
                Relaxation delay: {{ composition.relaxation_delay_s }} s
            </p>
        </div>
    </div>
</template>

<script>
import { ShieldCheckIcon } from "@heroicons/vue/24/outline";
import {
    basisDisplayLabel,
    basisUnitLabel,
    formatMixtureValue,
    mixtureComponentLabel,
    mixtureComponentSum,
    mixtureTotalStatus,
} from "@/Utils/mixtureComposition";

export default {
    name: "MixtureCompositionDisplay",

    components: {
        ShieldCheckIcon,
    },

    props: {
        composition: {
            type: Object,
            default: null,
        },
    },

    computed: {
        components() {
            return this.composition?.components ?? [];
        },
        total() {
            return (
                this.composition?.total ?? mixtureComponentSum(this.components)
            );
        },
        totalStatus() {
            return mixtureTotalStatus(
                this.total,
                this.composition?.basis,
                this.composition?.has_residual
            );
        },
        totalValueClass() {
            if (this.totalStatus === "complete") {
                return "text-emerald-700 dark:text-emerald-300";
            }
            if (this.totalStatus === "warning") {
                return "text-amber-700 dark:text-amber-300";
            }

            return "text-gray-900 dark:text-slate-100";
        },
        showSignalColumn() {
            return this.components.some(
                (c) => c.integrated_signal || c.n_nuclei
            );
        },
    },

    methods: {
        basisDisplayLabel,
        basisUnitLabel,
        formatMixtureValue,
        componentLabel: mixtureComponentLabel,
    },
};
</script>
