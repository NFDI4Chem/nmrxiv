<template>
    <span v-if="validation" class="inline-flex">
        <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium"
            :class="toneClass"
            :title="'nmrshiftdb2 Quickcheck of the assignments'"
            @click="open = true"
        >
            <span class="font-semibold">Quickcheck</span>
            <span v-for="(mark, nucleus) in presentMarks" :key="nucleus"
                >{{ nucleus === "13C" ? "¹³C" : "¹H" }} {{ mark }}/10</span
            >
            <span v-if="validation.confirmed" class="text-emerald-700"
                >· confirmed by author</span
            >
            <span v-if="validation.stale" class="text-amber-700"
                >· outdated</span
            >
        </button>

        <JetDialogModal :show="open" max-width="6xl" @close="open = false">
            <template #title>
                Assignment Quickcheck
                <span class="block text-xs font-normal text-gray-500">
                    {{ validation.source_label }} ·
                    {{ formatDate(validation.completed_at) }}
                </span>
            </template>
            <template #content>
                <div class="max-h-[70vh] space-y-4 overflow-y-auto pr-1">
                    <p
                        v-if="validation.stale"
                        class="rounded-md bg-amber-50 px-3 py-2 text-xs text-amber-900 ring-1 ring-amber-200"
                    >
                        The NMRium assignments of this sample changed after this
                        check was run.
                    </p>
                    <div
                        v-if="validation.confirmed"
                        class="rounded-md bg-emerald-50 px-3 py-2 text-xs text-emerald-900 ring-1 ring-emerald-200"
                    >
                        The authors confirmed these assignments
                        <span v-if="validation.confirmed_by"
                            >({{ validation.confirmed_by }})</span
                        >
                        on {{ formatDate(validation.confirmed_at) }}.
                        <span
                            v-if="validation.confirmation_note"
                            class="block pt-1 italic"
                            >“{{ validation.confirmation_note }}”</span
                        >
                    </div>
                    <QuickcheckReport
                        v-if="validation.report"
                        :report="validation.report"
                        :molfile="validation.molfile || ''"
                    />
                </div>
            </template>
            <template #footer>
                <JetSecondaryButton @click="open = false"
                    >Close</JetSecondaryButton
                >
            </template>
        </JetDialogModal>
    </span>
</template>

<script>
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import QuickcheckReport from "@/Shared/Assignments/QuickcheckReport.vue";

export default {
    components: {
        JetDialogModal,
        JetSecondaryButton,
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
            validation: null,
            open: false,
        };
    },
    computed: {
        presentMarks() {
            return Object.fromEntries(
                Object.entries(this.validation?.marks || {}).filter(
                    ([, mark]) => mark !== null
                )
            );
        },
        toneClass() {
            return (
                {
                    accept: "border-emerald-200 bg-emerald-50 text-emerald-900 hover:bg-emerald-100",
                    review: "border-amber-200 bg-amber-50 text-amber-900 hover:bg-amber-100",
                    reject: "border-red-200 bg-red-50 text-red-900 hover:bg-red-100",
                }[this.validation?.verdict] ||
                "border-gray-200 bg-gray-50 text-gray-800 hover:bg-gray-100"
            );
        },
    },
    mounted() {
        axios
            .get(`/api/v1/samples/${this.studyId}/assignment-validation`)
            .then(({ data }) => {
                this.validation = data.validation;
            })
            .catch(() => {
                this.validation = null;
            });
    },
    methods: {
        formatDate(value) {
            return value ? new Date(value).toLocaleDateString() : "";
        },
    },
};
</script>
