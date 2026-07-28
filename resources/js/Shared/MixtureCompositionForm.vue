<template>
    <div class="space-y-3">
        <!-- Basis -->
        <div>
            <div
                class="grid grid-cols-4 gap-1 rounded-lg bg-gray-100 p-1 dark:bg-slate-800/90"
                role="radiogroup"
                aria-label="Composition basis"
            >
                <button
                    v-for="option in basisOptions"
                    :key="option.value"
                    type="button"
                    role="radio"
                    :disabled="disabled"
                    :aria-checked="localBasis === option.value"
                    :title="option.helper"
                    :class="segmentButtonClass(localBasis === option.value)"
                    @click="selectBasis(option.value)"
                >
                    {{ option.label }}
                </button>
            </div>
            <p
                v-if="localBasis === 'mole_percent'"
                class="mt-1.5 text-[11px] leading-snug text-gray-500 dark:text-slate-400"
            >
                NMR integrals give mole fractions directly — mol % is preferred.
            </p>
        </div>

        <!-- Share for the compound being added -->
        <div>
            <label for="mixture-component-value" class="sr-only">
                Share on {{ basisUnitLabel(localBasis) }} basis
            </label>
            <div class="relative">
                <input
                    id="mixture-component-value"
                    v-model="draft.value"
                    type="number"
                    min="0"
                    step="0.001"
                    inputmode="decimal"
                    :placeholder="`Share (${basisUnitLabel(localBasis)})`"
                    class="block w-full rounded-md border-gray-300 pr-16 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-900 dark:text-slate-100"
                    :disabled="disabled"
                />
                <span
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs font-medium text-gray-400 dark:text-slate-500"
                >
                    {{ basisUnitLabel(localBasis) }}
                </span>
            </div>
        </div>

        <!-- Compact total -->
        <div class="space-y-1.5">
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-xs text-gray-500 dark:text-slate-400"
                    >Total</span
                >
                <span
                    class="text-sm font-semibold tabular-nums"
                    :class="totalValueClass"
                    aria-live="polite"
                >
                    {{ formatMixtureValue(projectedTotal) }}
                    {{ basisUnitLabel(localBasis) }}
                </span>
            </div>
            <div
                v-if="expectsNormalizedTotal"
                class="h-1.5 overflow-hidden rounded-full bg-gray-200 dark:bg-slate-700"
                role="progressbar"
                :aria-valuenow="Math.min(projectedTotal, 100)"
                aria-valuemin="0"
                aria-valuemax="100"
            >
                <div
                    class="h-full rounded-full transition-all duration-200"
                    :class="progressBarClass"
                    :style="{ width: `${progressWidth}%` }"
                />
            </div>
            <div
                v-if="sumWarning"
                class="flex flex-wrap items-center gap-2 text-xs text-amber-800 dark:text-amber-200"
            >
                <span class="min-w-0 flex-1 leading-snug">{{
                    sumWarning
                }}</span>
                <button
                    type="button"
                    class="shrink-0 rounded px-2 py-0.5 font-medium underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                    :class="
                        localHasResidual
                            ? 'text-amber-900 dark:text-amber-100'
                            : ''
                    "
                    @click="toggleResidual"
                >
                    {{ localHasResidual ? "Residual marked" : "Mark residual" }}
                </button>
            </div>
        </div>

        <!-- All optional fields in one place -->
        <div>
            <button
                type="button"
                class="flex w-full items-center gap-1.5 text-xs font-medium text-gray-500 transition-colors hover:text-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 dark:text-slate-400 dark:hover:text-teal-300"
                :aria-expanded="detailsExpanded"
                @click="detailsExpanded = !detailsExpanded"
            >
                <ChevronRightIcon
                    class="h-3.5 w-3.5 transition-transform duration-150"
                    :class="{ 'rotate-90': detailsExpanded }"
                    aria-hidden="true"
                />
                Spectrum details
                <span class="font-normal text-gray-400">(optional)</span>
                <ShieldCheckIcon
                    v-if="spectrumVerifiable"
                    class="ml-auto h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400"
                    title="Spectrum-verifiable"
                    aria-label="Spectrum-verifiable"
                />
            </button>

            <div
                v-show="detailsExpanded"
                class="mt-2 space-y-2 rounded-md border border-gray-200 bg-gray-50/80 p-2.5 dark:border-gray-600 dark:bg-slate-900/30"
            >
                <input
                    v-model="draft.integrated_signal"
                    type="text"
                    placeholder="Integrated signal, e.g. 1.18 ppm (CH₃, t)"
                    class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-900 dark:text-slate-100"
                    :disabled="disabled"
                />
                <div class="flex gap-2">
                    <input
                        v-model="draft.n_nuclei"
                        type="number"
                        min="1"
                        step="1"
                        inputmode="numeric"
                        placeholder="N"
                        title="Number of nuclei (N)"
                        class="w-16 rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-900 dark:text-slate-100"
                        :disabled="disabled"
                    />
                    <input
                        v-model="localNucleus"
                        type="text"
                        placeholder="Nucleus, e.g. ¹H"
                        class="min-w-0 flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-900 dark:text-slate-100"
                        @change="emitMetadataChange"
                    />
                    <input
                        v-model="localRelaxationDelay"
                        type="number"
                        min="0"
                        step="0.1"
                        inputmode="decimal"
                        placeholder="d₁ (s)"
                        title="Relaxation delay (seconds)"
                        class="w-20 rounded-md border-gray-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-900 dark:text-slate-100"
                        @change="emitMetadataChange"
                    />
                </div>
                <div
                    class="flex flex-wrap gap-1 rounded-lg bg-gray-100 p-0.5 dark:bg-slate-800/90"
                    role="radiogroup"
                    aria-label="Determination method"
                >
                    <button
                        v-for="option in methodOptions"
                        :key="option.value"
                        type="button"
                        role="radio"
                        :aria-checked="
                            localDeterminationMethod === option.value
                        "
                        :class="
                            segmentButtonClass(
                                localDeterminationMethod === option.value
                            )
                        "
                        @click="selectMethod(option.value)"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ChevronRightIcon, ShieldCheckIcon } from "@heroicons/vue/24/outline";
import {
    MIXTURE_BASIS_OPTIONS,
    MIXTURE_METHOD_OPTIONS,
    basisUnitLabel,
    formatMixtureValue,
    isMixtureSpectrumVerifiable,
    mixtureComponentSum,
    mixtureExpectsNormalizedTotal,
    mixtureSumWarning,
    mixtureTotalStatus,
} from "@/Utils/mixtureComposition";

export default {
    name: "MixtureCompositionForm",

    components: {
        ChevronRightIcon,
        ShieldCheckIcon,
    },

    props: {
        composition: {
            type: Object,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:draft", "metadata-change", "basis-change"],

    data() {
        return {
            basisOptions: MIXTURE_BASIS_OPTIONS,
            methodOptions: MIXTURE_METHOD_OPTIONS,
            localBasis: this.composition?.basis ?? "mole_percent",
            localDeterminationMethod:
                this.composition?.determination_method ?? "qnmr",
            localNucleus: this.composition?.nucleus ?? "",
            localRelaxationDelay: this.composition?.relaxation_delay_s ?? "",
            localHasResidual: this.composition?.has_residual ?? false,
            detailsExpanded: false,
            draft: {
                value: "",
                integrated_signal: "",
                n_nuclei: "",
            },
        };
    },

    computed: {
        existingComponents() {
            return this.composition?.components ?? [];
        },
        existingTotal() {
            return mixtureComponentSum(this.existingComponents);
        },
        projectedTotal() {
            const draftValue = Number(this.draft.value);
            const increment = Number.isFinite(draftValue) ? draftValue : 0;

            return this.existingTotal + increment;
        },
        expectsNormalizedTotal() {
            return mixtureExpectsNormalizedTotal(this.localBasis);
        },
        totalStatus() {
            return mixtureTotalStatus(
                this.projectedTotal,
                this.localBasis,
                this.localHasResidual
            );
        },
        totalValueClass() {
            if (this.totalStatus === "complete") {
                return "text-emerald-700 dark:text-emerald-300";
            }
            if (this.totalStatus === "warning") {
                return "text-amber-700 dark:text-amber-300";
            }

            return "text-teal-800 dark:text-teal-200";
        },
        progressBarClass() {
            if (this.totalStatus === "complete") {
                return "bg-emerald-500";
            }
            if (this.totalStatus === "warning") {
                return "bg-amber-500";
            }

            return "bg-teal-600";
        },
        progressWidth() {
            return Math.min(Math.max(this.projectedTotal, 0), 100);
        },
        sumWarning() {
            return mixtureSumWarning(
                [
                    ...this.existingComponents,
                    ...(this.draft.value !== "" && this.draft.value != null
                        ? [{ value: this.draft.value }]
                        : []),
                ],
                this.localBasis,
                this.localHasResidual
            );
        },
        spectrumVerifiable() {
            const components = [
                ...this.existingComponents,
                ...(this.draft.integrated_signal
                    ? [{ integrated_signal: this.draft.integrated_signal }]
                    : []),
            ];

            return isMixtureSpectrumVerifiable({
                basis: this.localBasis,
                components,
            });
        },
        hasStoredDetails() {
            return Boolean(
                this.composition?.nucleus ||
                    this.composition?.relaxation_delay_s != null ||
                    this.existingComponents.some(
                        (c) => c.integrated_signal || c.n_nuclei
                    )
            );
        },
    },

    watch: {
        composition: {
            deep: true,
            handler(next) {
                if (!next) {
                    return;
                }

                this.localBasis = next.basis ?? this.localBasis;
                this.localDeterminationMethod =
                    next.determination_method ?? this.localDeterminationMethod;
                this.localNucleus = next.nucleus ?? "";
                this.localRelaxationDelay = next.relaxation_delay_s ?? "";
                this.localHasResidual = Boolean(next.has_residual);
            },
        },
        draft: {
            deep: true,
            handler(next) {
                this.$emit("update:draft", {
                    ...next,
                    n_nuclei:
                        next.n_nuclei === "" || next.n_nuclei == null
                            ? null
                            : Number(next.n_nuclei),
                });
            },
        },
        hasStoredDetails: {
            immediate: true,
            handler(hasDetails) {
                if (hasDetails) {
                    this.detailsExpanded = true;
                }
            },
        },
    },

    methods: {
        basisUnitLabel,
        formatMixtureValue,
        segmentButtonClass(selected) {
            return [
                selected
                    ? "bg-white text-teal-800 shadow-sm ring-1 ring-gray-200/80 dark:bg-slate-800 dark:text-teal-200 dark:ring-gray-600"
                    : "text-gray-600 hover:text-gray-900 dark:text-slate-400 dark:hover:text-slate-200",
                "min-w-0 flex-1 rounded px-1.5 py-1.5 text-center text-[11px] font-medium transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 sm:text-xs",
            ];
        },
        selectBasis(value) {
            if (this.disabled || this.localBasis === value) {
                return;
            }

            this.localBasis = value;
            this.emitBasisChange();
        },
        selectMethod(value) {
            this.localDeterminationMethod = value;
            this.emitMetadataChange();
        },
        toggleResidual() {
            this.localHasResidual = !this.localHasResidual;
            this.emitMetadataChange();
        },
        emitBasisChange() {
            this.$emit("basis-change", this.localBasis);
            this.emitMetadataChange();
        },
        emitMetadataChange() {
            this.$emit("metadata-change", this.metadataPayload());
        },
        metadataPayload() {
            return {
                basis: this.localBasis,
                determination_method: this.localDeterminationMethod,
                nucleus: this.localNucleus?.trim() || null,
                relaxation_delay_s:
                    this.localRelaxationDelay === "" ||
                    this.localRelaxationDelay == null
                        ? null
                        : Number(this.localRelaxationDelay),
                has_residual: this.localHasResidual,
            };
        },
        mixturePayload() {
            const payload = this.metadataPayload();
            const value = Number(this.draft.value);

            return {
                ...payload,
                value: Number.isFinite(value) ? value : null,
                integrated_signal: this.draft.integrated_signal?.trim() || null,
                n_nuclei:
                    this.draft.n_nuclei === "" || this.draft.n_nuclei == null
                        ? null
                        : Number(this.draft.n_nuclei),
            };
        },
        resetDraft() {
            this.draft = {
                value: "",
                integrated_signal: "",
                n_nuclei: "",
            };
        },
        canAddComponent() {
            const value = Number(this.draft.value);

            return Number.isFinite(value) && value >= 0;
        },
        expandDetails() {
            this.detailsExpanded = true;
        },
    },
};
</script>
