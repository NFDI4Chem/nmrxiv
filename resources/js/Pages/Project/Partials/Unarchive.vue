<template>
    <jet-action-section>
        <template #title> Unarchive Project </template>

        <template #description> Permanently unarchive your project. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once your project is unarchived, all of its resources and data
                will be permanently unarchived. Before deleting your project,
                please download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                <jet-danger-button @click="confirmProjectDeletion">
                    Unarchive Project
                </jet-danger-button>
            </div>

            <!-- Unarchive Project Confirmation Modal  -->
            <jet-dialog-modal
                :show="confirmingProjectUnarchival"
                @close="closeModal"
            >
                <template #title> Unarchive Project </template>
                <template #content>
                    <loading-button :loading="loading" />
                    <!-- header -->
                    <template v-if="!loading">
                        <span v-if="hasPassword">
                            Are you sure you want to unarchive your project?
                            Once your project is unarchived, all of its
                            resources and data will be permanently unarchived.
                            Please enter your password to confirm you would like
                            to permanently unarchive your project.

                            <div class="mt-4">
                                <jet-input
                                    ref="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-3/4"
                                    placeholder="Password"
                                    @keyup.enter="unarchiveProject"
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

                    <jet-danger-button
                        v-if="hasPassword"
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="unarchiveProject"
                    >
                        Unarchive Project
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
import LoadingButton from "@/Shared/LoadingButton.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default {
    components: {
        JetActionSection,
        JetDangerButton,
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
            confirmingProjectUnarchival: false,
            confirmingProjectUnarchivalWithoutPassword: false,

            form: this.$inertia.form({
                password: "",
            }),

            hasPassword: false,
        };
    },

    methods: {
        confirmProjectDeletion() {
            this.loading = true;
            this.confirmingProjectUnarchival = true;
            axios
                .get(route("auth.checkPassword", this.project.id))
                .then((res) => {
                    this.hasPassword = res.data.hasPassword;
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        unarchiveProject() {
            this.form.put(
                route("dashboard.project.toggle-archive", this.project.id),
                {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                    onError: () => this.$refs.password.focus(),
                    onFinish: () => this.form.reset(),
                }
            );
        },

        closeModal() {
            this.confirmingProjectUnarchival = false;
            this.confirmingProjectUnarchivalWithoutPassword = false;
            this.form.reset();
        },
    },
};
</script>
