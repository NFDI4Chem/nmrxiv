<template>
    <jet-dialog-modal :show="show" max-width="2xl" @close="$emit('close')">
        <template #title> Mixture composition guide </template>

        <template #content>
            <div
                class="max-h-[min(70vh,32rem)] space-y-6 overflow-y-auto pr-1 text-sm leading-relaxed text-gray-700 dark:text-slate-300"
            >
                <section>
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Sample type
                    </h3>
                    <dl class="mt-2 space-y-3">
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Pure sample
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                A single compound at (nominally) 100%. Use this
                                when the sample is one substance with no
                                co-contaminants you need to quantify.
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Mixture
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                Two or more components with explicit shares on a
                                chosen basis. Add each structure, enter its
                                share, then click <strong>Add compound</strong>.
                                Shares are stored per component, not as one
                                ambiguous total.
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Unknown
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                Structures are recorded without composition
                                percentages — for example when identity is known
                                but relative amounts are not.
                            </dd>
                        </div>
                    </dl>
                </section>

                <section>
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Basis
                    </h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">
                        The basis tells readers what your numbers mean. Always
                        pick one <em>before</em> entering component shares — the
                        unit label on each value comes from this choice (never a
                        bare “%”).
                    </p>
                    <dl class="mt-3 space-y-2.5">
                        <div
                            v-for="option in basisOptions"
                            :key="option.value"
                            class="rounded-md border border-gray-100 bg-gray-50/80 px-3 py-2 dark:border-gray-700 dark:bg-slate-900/40"
                        >
                            <dt
                                class="font-medium text-teal-800 dark:text-teal-200"
                            >
                                {{ option.label }}
                                <span
                                    class="font-normal text-gray-500 dark:text-slate-400"
                                >
                                    — {{ option.displayLabel }}
                                </span>
                            </dt>
                            <dd
                                class="mt-0.5 text-xs text-gray-600 dark:text-slate-400"
                            >
                                {{ option.helper }}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section>
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Component share
                    </h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">
                        For each structure you add, enter how much of the
                        mixture that component represents on the selected basis.
                        Example: ethanol <strong>62.4 mol %</strong> means 62.4
                        mole percent of the mixture is ethanol.
                    </p>
                </section>

                <section>
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Running total
                    </h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">
                        The total sums all entered component shares and updates
                        as you type. For mol %, wt %, and vol %, values are
                        usually normalized to <strong>100</strong> on that
                        basis.
                    </p>
                    <ul
                        class="mt-2 list-disc space-y-1 pl-5 text-gray-600 dark:text-slate-400"
                    >
                        <li>
                            <strong>Within ±0.5</strong> of 100 — no warning;
                            the bar turns green.
                        </li>
                        <li>
                            <strong>Outside that range</strong> — an amber note
                            appears. Use <strong>Mark residual</strong> if the
                            gap is an intentional unquantified fraction (solvent
                            residue, unknown impurity, etc.).
                        </li>
                        <li>
                            <strong>Molar ratio</strong> — no fixed 100 target;
                            values are relative amounts.
                        </li>
                    </ul>
                </section>

                <section>
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                    >
                        Spectrum details
                        <span class="font-normal text-gray-500"
                            >(optional)</span
                        >
                    </h3>
                    <p class="mt-1 text-gray-600 dark:text-slate-400">
                        Expand this section when your composition comes from
                        (quantitative) NMR and you want the record to be
                        auditable against the spectrum.
                    </p>
                    <dl class="mt-3 space-y-2.5">
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Integrated signal
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                The resonance used for the integral, e.g.
                                <code class="text-xs">1.18 ppm (CH₃, t)</code>.
                                Links the reported share to a specific peak in
                                the spectrum.
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                N
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                Number of nuclei contributing to that integral
                                (e.g. 3 for a methyl group). Used with the
                                integral ratio when normalizing mole fractions.
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Nucleus
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                The observed nucleus, typically
                                <sup>1</sup>H for routine qNMR mixture work.
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Relaxation delay (d₁)
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                Recycle delay in seconds. For quantitative
                                integrals, this should be long enough for full
                                relaxation (often ≥ 5× the longest T₁), e.g. 30
                                s.
                            </dd>
                        </div>
                        <div>
                            <dt
                                class="font-medium text-gray-900 dark:text-slate-100"
                            >
                                Determination method
                            </dt>
                            <dd
                                class="mt-0.5 text-gray-600 dark:text-slate-400"
                            >
                                How the composition was obtained:
                                <strong>qNMR</strong> (integral ratios),
                                <strong>gravimetric</strong> (weighing),
                                <strong>supplier stated</strong>, or
                                <strong>other</strong>.
                            </dd>
                        </div>
                    </dl>
                </section>

                <section
                    class="rounded-lg border border-emerald-200 bg-emerald-50/60 px-3 py-3 dark:border-emerald-900/40 dark:bg-emerald-950/30"
                >
                    <h3
                        class="flex items-center gap-1.5 text-sm font-semibold text-emerald-900 dark:text-emerald-100"
                    >
                        <ShieldCheckIcon class="h-4 w-4" aria-hidden="true" />
                        Spectrum-verifiable
                    </h3>
                    <p class="mt-1 text-emerald-800 dark:text-emerald-200/90">
                        A deposited mixture is marked
                        <strong>spectrum-verifiable</strong> when a basis is set
                        and every component has an integrated signal recorded.
                        That lets readers trace each share back to a specific
                        NMR resonance. Nucleus, method, and relaxation delay
                        further strengthen the record but are not required for
                        the badge.
                    </p>
                </section>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click="$emit('close')">
                Close
            </jet-secondary-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { ShieldCheckIcon } from "@heroicons/vue/24/outline";
import { MIXTURE_BASIS_OPTIONS } from "@/Utils/mixtureComposition";

export default {
    name: "MixtureCompositionHelpModal",

    components: {
        JetDialogModal,
        JetSecondaryButton,
        ShieldCheckIcon,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["close"],

    data() {
        return {
            basisOptions: MIXTURE_BASIS_OPTIONS,
        };
    },
};
</script>
