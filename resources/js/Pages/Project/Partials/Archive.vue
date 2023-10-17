<template>
    <jet-action-section>
        <template #title> Archive Project </template>

        <template #description>
            Would you like to deprecate your project.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once your project is archived, all of its resources and data
                will be permanently uneditable and unversionable. A message will
                be displayed indicating that the project is archived and no
                further updates are expected.
            </div>

            <div class="mt-5">
                <jet-danger-button @click="confirmProjectDeletion">
                    Archive Project
                </jet-danger-button>
            </div>

            <!-- Archive Project Confirmation Modal  -->
            <jet-dialog-modal
                :show="confirmingProjectArchival"
                @close="closeModal"
            >
                <template #title> Archive Project </template>
                <template #content>
                    <loading-button :loading="loading" />
                    <!-- header -->
                    <template v-if="!loading">
                        <span v-if="hasPassword">
                            Are you sure you want to archive your project? Once
                            your project is archived, all of its resources and
                            data will be permanently archived. Please enter your
                            password to confirm you would like to permanently
                            archive your project.

                            <div class="mt-4">
                                <jet-input
                                    ref="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-3/4"
                                    placeholder="Password"
                                    @keyup.enter="archiveProject"
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
                        @click="archiveProject"
                    >
                        Archive Project
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
import { Link } from "@inertiajs/vue3";

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
            confirmingProjectArchival: false,
            confirmingProjectArchivalWithoutPassword: false,

            form: this.$inertia.form({
                password: "",
            }),

            hasPassword: false,
        };
    },

    methods: {
        confirmProjectDeletion() {
            this.loading = true;
            this.confirmingProjectArchival = true;
            axios
                .get(route("auth.checkPassword", this.project.id))
                .then((res) => {
                    this.hasPassword = res.data.hasPassword;
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        archiveProject() {
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
            this.confirmingProjectArchival = false;
            this.confirmingProjectArchivalWithoutPassword = false;
            this.form.reset();
        },
    },
};
</script>
