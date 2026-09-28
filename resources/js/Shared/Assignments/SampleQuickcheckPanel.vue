<template>
    <section
        class="rounded-lg border border-gray-200 bg-white dark:border-slate-700 dark:bg-slate-900/40"
        aria-labelledby="sample-quickcheck-heading"
    >
        <header
            class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-slate-700"
        >
            <div class="min-w-0">
                <h3
                    id="sample-quickcheck-heading"
                    class="text-sm font-semibold text-gray-900 dark:text-slate-100"
                >
                    Assignment Quickcheck
                </h3>
                <p
                    class="mt-0.5 max-w-2xl text-xs text-gray-600 dark:text-slate-300"
                >
                    Compares your ¹³C and ¹H assignments with nmrshiftdb2
                    predictions for the sample structure, like the nmrshiftdb2
                    Quickcheck. Reviewers see the result and your confirmation
                    on the published sample.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="busy || isPending || !nmrium.assigned"
                    :title="
                        nmrium.assigned
                            ? ''
                            : 'Link NMRium ranges to atoms first'
                    "
                    @click="run({ source: 'nmrium' })"
                >
                    Check NMRium assignments
                    <span
                        v-if="nmrium.assigned"
                        class="ml-1 font-normal opacity-80"
                        >({{ nmrium.assigned }} assigned)</span
                    >
                </button>
                <label
                    class="inline-flex cursor-pointer items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    :class="{
                        'pointer-events-none opacity-50': busy || isPending,
                    }"
                >
                    Upload SDF / NMReDATA
                    <input
                        ref="file"
                        type="file"
                        accept=".sdf,.sd,.nmredata,.mol,.txt"
                        class="sr-only"
                        @change="uploadFile"
                    />
                </label>
            </div>
        </header>

        <div class="space-y-3 px-4 py-3">
            <p v-if="error" class="text-xs text-red-600 dark:text-red-400">
                {{ error }}
            </p>

            <div v-if="loading" class="animate-pulse space-y-2">
                <div
                    class="h-4 w-1/3 rounded bg-gray-200 dark:bg-slate-700"
                ></div>
                <div
                    class="h-3 w-2/3 rounded bg-gray-200 dark:bg-slate-700"
                ></div>
            </div>

            <p
                v-else-if="!validation"
                class="text-xs text-gray-500 dark:text-slate-400"
            >
                No Quickcheck has been run for this sample yet. Assign the
                spectra in NMRium above, or upload the SD file exported from
                Mnova (Copy special → SDF with assignments) or an NMReDATA file.
            </p>

            <div v-else-if="isPending" class="space-y-2" aria-live="polite">
                <p
                    class="text-xs font-medium text-gray-700 dark:text-slate-200"
                >
                    {{
                        validation.status === "running"
                            ? "Predicting spectra with nmrshiftdb2…"
                            : "Quickcheck queued…"
                    }}
                    <span class="font-normal text-gray-500 dark:text-slate-400"
                        >This usually takes under a minute.</span
                    >
                </p>
                <p
                    v-if="validation.error"
                    class="text-xs text-amber-700 dark:text-amber-300"
                >
                    {{ validation.error }}
                </p>
                <div class="animate-pulse space-y-2">
                    <div
                        class="h-10 rounded-lg bg-gray-100 dark:bg-slate-800"
                    ></div>
                    <div
                        class="h-24 rounded-lg bg-gray-100 dark:bg-slate-800"
                    ></div>
                </div>
            </div>

            <div
                v-else-if="validation.status === 'failed'"
                class="rounded-md bg-red-50 px-3 py-2 text-xs text-red-800 ring-1 ring-red-200 dark:bg-red-950/40 dark:text-red-100 dark:ring-red-900/50"
            >
                The Quickcheck failed: {{ validation.error || "unknown error" }}
            </div>

            <template v-else>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span
                        class="rounded-full px-2.5 py-1 font-semibold"
                        :class="verdictClass"
                        >{{ verdictLabel }}</span
                    >
                    <span
                        v-for="(mark, nucleus) in presentMarks"
                        :key="nucleus"
                        class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-800 dark:bg-slate-800 dark:text-slate-100"
                        >{{ nucleus === "13C" ? "¹³C" : "¹H" }}
                        {{ mark }}/10</span
                    >
                    <span class="text-gray-500 dark:text-slate-400"
                        >{{ validation.source_label }} ·
                        {{ formatDate(validation.completed_at) }}</span
                    >
                    <span
                        v-if="validation.stale"
                        class="rounded-full bg-amber-100 px-2 py-0.5 font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-200"
                        >Out of date: the NMRium assignments changed</span
                    >
                    <button
                        type="button"
                        class="ml-auto font-medium text-teal-700 hover:underline dark:text-teal-300"
                        @click="showReport = !showReport"
                    >
                        {{ showReport ? "Hide report" : "Show full report" }}
                    </button>
                </div>

                <QuickcheckReport
                    v-if="showReport && validation.report"
                    :report="validation.report"
                    :molfile="validation.molfile || ''"
                />

                <div
                    v-if="validation.confirmed"
                    class="rounded-md bg-emerald-50 px-3 py-2 text-xs text-emerald-900 ring-1 ring-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-100 dark:ring-emerald-900/50"
                >
                    Assignments confirmed by
                    <strong>{{
                        validation.confirmed_by || "the author"
                    }}</strong>
                    on {{ formatDate(validation.confirmed_at) }}.
                    <span
                        v-if="validation.confirmation_note"
                        class="block pt-1 italic"
                        >“{{ validation.confirmation_note }}”</span
                    >
                </div>

                <div
                    v-else-if="!validation.stale"
                    class="space-y-2 rounded-md border border-gray-200 p-3 dark:border-slate-700"
                >
                    <p class="text-xs text-gray-700 dark:text-slate-200">
                        <span class="font-semibold"
                            >Confirm the assignments.</span
                        >
                        <span v-if="validation.requires_confirmation_note">
                            The check disagrees with some assignments. Explain
                            the evidence you rely on (e.g. HMBC, NOE, X-ray,
                            literature) so reviewers can follow your reasoning.
                        </span>
                        <span v-else>
                            Your confirmation is shown with the Quickcheck on
                            the published sample.
                        </span>
                    </p>
                    <textarea
                        v-model="note"
                        rows="2"
                        maxlength="2000"
                        class="block w-full resize-y rounded-md border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-600 dark:bg-slate-800 dark:text-slate-100"
                        :placeholder="
                            validation.requires_confirmation_note
                                ? 'e.g. C-3/C-5 assigned from HMBC correlations to H-2/H-6'
                                : 'Optional note for reviewers'
                        "
                    ></textarea>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white"
                        :disabled="
                            busy ||
                            (validation.requires_confirmation_note &&
                                !note.trim())
                        "
                        @click="confirm"
                    >
                        I confirm these assignments are correct
                    </button>
                </div>
            </template>
        </div>
    </section>
</template>

<script>
import QuickcheckReport from "@/Shared/Assignments/QuickcheckReport.vue";

const POLL_INTERVAL_MS = 3000;

const VERDICT_STYLES = {
    accept: [
        "Fits prediction",
        "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200",
    ],
    review: [
        "Needs review",
        "bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200",
    ],
    reject: [
        "Disagrees with prediction",
        "bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200",
    ],
    not_assessable: [
        "Not assessable",
        "bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-300",
    ],
};

export default {
    components: {
        QuickcheckReport,
    },
    props: {
        studyId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            loading: true,
            busy: false,
            error: null,
            nmrium: { available: false, assigned: 0, nuclei: [] },
            validation: null,
            showReport: false,
            note: "",
            pollTimer: null,
        };
    },
    computed: {
        endpoint() {
            return `/dashboard/studies/${this.studyId}/assignment-validation`;
        },
        isPending() {
            return ["queued", "running"].includes(this.validation?.status);
        },
        presentMarks() {
            return Object.fromEntries(
                Object.entries(this.validation?.marks || {}).filter(
                    ([, mark]) => mark !== null
                )
            );
        },
        verdictLabel() {
            return (VERDICT_STYLES[this.validation?.verdict] ||
                VERDICT_STYLES.not_assessable)[0];
        },
        verdictClass() {
            return (VERDICT_STYLES[this.validation?.verdict] ||
                VERDICT_STYLES.not_assessable)[1];
        },
    },
    watch: {
        studyId() {
            this.stopPolling();
            this.validation = null;
            this.loading = true;
            this.load();
        },
    },
    mounted() {
        this.load();
    },
    beforeUnmount() {
        this.stopPolling();
    },
    methods: {
        load() {
            return axios
                .get(this.endpoint)
                .then(({ data }) => {
                    this.nmrium = data.nmrium;
                    this.setValidation(data.validation);
                })
                .catch(() => {
                    this.error = "Could not load the Quickcheck status.";
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        setValidation(validation) {
            const wasPending = this.isPending;
            this.validation = validation;
            this.stopPolling();
            if (this.isPending) {
                this.pollTimer = setTimeout(
                    () => this.load(),
                    POLL_INTERVAL_MS
                );
            } else if (wasPending && validation?.status === "completed") {
                this.showReport = true;
            }
        },
        stopPolling() {
            if (this.pollTimer) {
                clearTimeout(this.pollTimer);
                this.pollTimer = null;
            }
        },
        run(payload) {
            this.busy = true;
            this.error = null;
            this.note = "";

            return axios
                .post(this.endpoint, payload)
                .then(({ data }) => {
                    this.showReport = false;
                    this.setValidation(data.validation);
                })
                .catch((error) => {
                    this.error = this.errorMessage(error);
                })
                .finally(() => {
                    this.busy = false;
                });
        },
        uploadFile(event) {
            const file = event.target.files?.[0];
            if (!file) {
                return;
            }
            const form = new FormData();
            form.append("source", "file");
            form.append("file", file);
            this.run(form).finally(() => {
                this.$refs.file.value = "";
            });
        },
        confirm() {
            this.busy = true;
            this.error = null;

            axios
                .post(`${this.endpoint}/${this.validation.id}/confirm`, {
                    note: this.note,
                })
                .then(({ data }) => {
                    this.validation = data.validation;
                })
                .catch((error) => {
                    this.error = this.errorMessage(error);
                })
                .finally(() => {
                    this.busy = false;
                });
        },
        errorMessage(error) {
            const errors = error.response?.data?.errors;
            if (errors) {
                return Object.values(errors).flat()[0];
            }
            return (
                error.response?.data?.message ||
                "Something went wrong. Please try again."
            );
        },
        formatDate(value) {
            return value ? new Date(value).toLocaleString() : "";
        },
    },
};
</script>
