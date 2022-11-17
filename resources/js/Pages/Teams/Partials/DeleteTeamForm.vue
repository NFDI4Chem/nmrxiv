<template>
    <jet-action-section>
        <template #title> Delete Team </template>

        <template #description> Permanently delete this team. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once a team is deleted, all of its resources and data will be
                permanently deleted. Before deleting this team, please download
                any data or information regarding this team that you wish to
                retain.
            </div>

            <div class="mt-5">
                <jet-danger-button @click="confirmTeamDeletion">
                    Delete Team
                </jet-danger-button>
            </div>

            <!-- Delete Team Confirmation Modal -->
            <jet-confirmation-modal
                :show="confirmingTeamDeletion"
                @close="confirmingTeamDeletion = false"
            >
                <template #title> Delete Team </template>

                <template #content>
                    Are you sure you want to delete this team? Once a team is
                    deleted, all of its resources and data will be permanently
                    deleted.
                    <div class="mt-4">
                        <jet-input
                            ref="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            @keyup.enter="deleteTeam"
                        />
                        <jet-input-error
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                    <jet-input-error
                        :message="form.errors.hasProject"
                        class="mt-2"
                    />
                </template>

                <template #footer>
                    <jet-secondary-button
                        @click="confirmingTeamDeletion = false"
                    >
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button
                        class="ml-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteTeam"
                    >
                        Delete Team
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </jet-action-section>
</template>

<script>
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetInput from "@/Jetstream/Input.vue";

export default {
    components: {
        JetActionSection,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetInputError,
        JetInput,
    },
    props: ["team"],

    data() {
        return {
            confirmingTeamDeletion: false,
            deleting: false,

            form: this.$inertia.form({
                password: "",
            }),
        };
    },

    methods: {
        confirmTeamDeletion() {
            this.confirmingTeamDeletion = true;
        },

        deleteTeam() {
            this.form.delete(route("app.teams.destroy", this.team), {
                onError: () => this.$refs.password.focus(),
                errorBag: "deleteTeam",
                onFinish: () => this.form.reset(),
            });
        },
    },
};
</script>
