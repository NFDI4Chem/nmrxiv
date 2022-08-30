<template>
    <jet-action-section>
        <template #title> Restore Project </template>

        <template #description> Restore your project. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once your project is restored, all of its resources and data
                will be put back to their original state. Before restoring your
                project, please make sure your project access rights are
                reviewed.
            </div>

            <div class="mt-5">
                <jet-success-button @click="confirmProjectDeletion">
                    Restore Project
                </jet-success-button>
            </div>

            <!-- Restore Project Confirmation Modal  -->
            <jet-dialog-modal
                :show="confirmingProjectDeletion"
                @close="closeModal"
            >
                <template #title> Restore Project </template>
                <template #content>
                    <loading-button :loading="loading" />
                    <!-- header -->
                    <template v-if="!loading">
                        <span v-if="hasPassword">
                            Are you sure you want to restore your project? Once
                            your project is restored, all of its resources and
                            data will be set back to the original state. Please
                            enter your password to confirm you would like to
                            restore your project.

                            <div class="mt-4">
                                <jet-input
                                    ref="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-3/4"
                                    placeholder="Password"
                                    @keyup.enter="restoreProject"
                                />
                                <jet-input-error
                                    :message="form.errors.password"
                                    class="mt-2"
                                />
                            </div>
                        </span>
                        <span v-else>
                            You need to set your password first before deleting
                            the project.
                        </span>
                    </template>
                </template>
                <!-- footer -->
                <template v-if="!loading" #footer>
                    <jet-secondary-button @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <jet-success-button
                        v-if="hasPassword"
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="restoreProject"
                    >
                        Restore Project
                    </jet-success-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetSuccessButton from "@/Jetstream/SuccessButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        JetActionSection,
        JetSuccessButton,
        JetDialogModal,
        JetInput,
        JetInputError,
        JetSecondaryButton,
        LoadingButton,
        Link,
    },

    props: ["project"],

    data() {
        return {
            loading: false,
            confirmingProjectDeletion: false,
            confirmingProjectDeletionWithoutPassword: false,

            form: this.$inertia.form({
                password: "",
            }),

            hasPassword: false,
        };
    },

    methods: {
        confirmProjectDeletion() {
            this.loading = true;
            this.confirmingProjectDeletion = true;
            axios
                .get(route("auth.checkPassword", this.project.id))
                .then((res) => {
                    this.hasPassword = res.data.hasPassword;
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        restoreProject() {
            this.form.put(route("dashboard.project.destroy", this.project.id), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.password.focus(),
                onFinish: () => this.form.reset(),
            });
        },

        closeModal() {
            this.confirmingProjectDeletion = false;
            this.confirmingProjectDeletionWithoutPassword = false;
            this.form.reset();
        },
    },
};
</script>
