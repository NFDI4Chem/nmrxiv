<template>
    <jet-dialog-modal :show="show" max-width="2xl" @close="emit('close')">
        <template #title>Submit samples for publication</template>

        <template #content>
            <p class="text-sm text-gray-600">
                Selected samples will be published as independent public
                entries. Your community contribution draft and any unselected
                folders will remain available for further work.
            </p>

            <div class="mt-4">
                <p
                    class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                >
                    Ready to publish
                </p>
                <ul
                    class="mt-2 max-h-48 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3"
                >
                    <li
                        v-for="study in publishableStudies"
                        :key="study.id"
                        class="flex items-start gap-2 text-sm text-gray-800"
                    >
                        <input
                            :id="`community-submit-study-${study.id}`"
                            v-model="selectedStudyIds"
                            type="checkbox"
                            class="mt-0.5 rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                            :value="study.id"
                        />
                        <label
                            :for="`community-submit-study-${study.id}`"
                            class="min-w-0 flex-1 cursor-pointer"
                        >
                            {{ study.name }}
                        </label>
                    </li>
                </ul>
                <p
                    v-if="selectedStudyIds.length === 0"
                    class="mt-2 text-sm text-amber-700"
                >
                    Select at least one sample to continue.
                </p>
            </div>

            <div class="mt-5">
                <h3 class="text-lg font-bold text-gray-400">
                    Terms &amp; Conditions
                </h3>

                <div class="mt-3">
                    <div class="ml-2">
                        <div class="flex items-top">
                            <input
                                id="community-submit-conditions"
                                v-model="form.conditions"
                                type="checkbox"
                                class="mt-1 rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                name="conditions"
                            />
                            <div class="ml-2 text-sm text-gray-700">
                                I understand that publishing makes all
                                underlying data publicly available on the nmrXiv
                                platform after the set release date.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="ml-2">
                        <div class="flex items-center">
                            <input
                                id="community-submit-terms"
                                v-model="form.terms"
                                type="checkbox"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                name="terms"
                            />
                            <div class="ml-2 text-sm text-gray-700">
                                I agree to the
                                <a
                                    target="_blank"
                                    :href="route('terms.show')"
                                    class="text-sm font-medium text-primary-700 underline decoration-primary-300 underline-offset-2 hover:text-primary-900 hover:decoration-primary-500"
                                    >Terms of Service</a
                                >
                                and
                                <a
                                    target="_blank"
                                    :href="route('policy.show')"
                                    class="text-sm font-medium text-primary-700 underline decoration-primary-300 underline-offset-2 hover:text-primary-900 hover:decoration-primary-500"
                                    >Privacy Policy</a
                                >
                                and hereby also grant nmrXiv permissions to
                                distribute the datasets (and meta-data) under
                                the specified license.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p
                v-if="submitError"
                class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-900"
                role="alert"
            >
                {{ submitError }}
            </p>
        </template>

        <template #footer>
            <jet-secondary-button :disabled="submitting" @click="emit('close')">
                Cancel
            </jet-secondary-button>
            <jet-success-button
                class="ml-2"
                type="button"
                :class="{
                    'cursor-not-allowed bg-gray-200 opacity-50': !canSubmit,
                }"
                :disabled="!canSubmit"
                @click="submit"
            >
                {{ submitting ? "Submitting…" : "Submit selected samples" }}
            </jet-success-button>
        </template>
    </jet-dialog-modal>
</template>

<script setup>
import { computed, reactive, ref, watch } from "vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import { publishCommunityStudies } from "@/Composables/useDraftProcessing";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    draftId: {
        type: [Number, String],
        required: true,
    },
    publishableStudies: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["close", "submitted"]);

const form = reactive({
    terms: false,
    conditions: false,
});

const selectedStudyIds = ref([]);
const submitting = ref(false);
const submitError = ref(null);

const canSubmit = computed(
    () =>
        selectedStudyIds.value.length > 0 &&
        form.terms &&
        form.conditions &&
        !submitting.value
);

watch(
    () => props.show,
    (visible) => {
        if (!visible) {
            return;
        }

        submitError.value = null;
        form.terms = false;
        form.conditions = false;
        selectedStudyIds.value = props.publishableStudies.map(
            (study) => study.id
        );
    }
);

async function submit() {
    if (!canSubmit.value) {
        return;
    }

    submitting.value = true;
    submitError.value = null;

    try {
        const result = await publishCommunityStudies(props.draftId, {
            study_ids: selectedStudyIds.value,
            terms: form.terms,
            conditions: form.conditions,
        });

        emit("submitted", result);
        emit("close");
    } catch (error) {
        const data = error.response?.data;

        if (data?.errors) {
            const messages = Object.values(data.errors).flat();

            submitError.value =
                messages[0] ||
                "Could not submit the selected samples. Please try again.";
        } else {
            submitError.value =
                data?.message ||
                "Could not submit the selected samples. Please try again.";
        }
    } finally {
        submitting.value = false;
    }
}
</script>
