<template>
    <jet-action-section>
        <template #title> Delete Study </template>

        <template #description> Permanently delete your study. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once your sample is deleted, all of its resources and data will
                be permanently deleted. Before deleting your study, please
                download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                <jet-danger-button @click="confirmStudyDeletion">
                    Delete Study
                </jet-danger-button>
            </div>

            <!-- Delete Study Confirmation Modal -->
            <jet-dialog-modal
                :show="confirmingStudyDeletion"
                @close="closeModal"
            >
                <template #title> Delete Study </template>

                <template #content>
                    Are you sure you want to delete your study? Once your study
                    is deleted, all of its resources and data will be
                    permanently deleted. Please enter your password to confirm
                    you would like to permanently delete your study.

                    <div class="mt-4">
                        <jet-input
                            ref="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            @keyup.enter="deleteStudy"
                        />

                        <jet-input-error
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteStudy"
                    >
                        Delete Study
                    </jet-danger-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";

export default {
    components: {
        JetActionSection,
        JetDangerButton,
        JetDialogModal,
        JetInput,
        JetInputError,
        JetSecondaryButton,
    },

    props: ["study"],

    data() {
        return {
            confirmingStudyDeletion: false,

            form: this.$inertia.form({
                password: "",
            }),
        };
    },

    methods: {
        confirmStudyDeletion() {
            this.confirmingStudyDeletion = true;

            setTimeout(() => this.$refs.password.focus(), 250);
        },

        deleteStudy() {
            this.form.delete(route("dashboard.study.destroy", this.study.id), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.password.focus(),
                onFinish: () => this.form.reset(),
            });
        },

        closeModal() {
            this.confirmingStudyDeletion = false;

            this.form.reset();
        },
    },
};
</script>
