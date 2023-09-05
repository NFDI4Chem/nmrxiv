<template>
    <div>
        <div v-if="userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Add Team Member -->
            <jet-form-section @submitted="addTeamMember">
                <template #title> Add Team Member </template>

                <template #description>
                    Add a new team member to your team, allowing them to
                    collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide the email address of the person you
                            would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="email" value="Email" />
                        <jet-input
                            id="email"
                            v-model="addTeamMemberForm.email"
                            type="email"
                            class="mt-1 block w-full"
                        />
                        <jet-input-error
                            :message="addTeamMemberForm.errors.email"
                            class="mt-2"
                        />
                    </div>

                    <!-- Role -->
                    <div
                        v-if="availableRoles.length > 0"
                        class="col-span-6 lg:col-span-4"
                    >
                        <jet-label for="roles" value="Role" />
                        <jet-input-error
                            :message="addTeamMemberForm.errors.role"
                            class="mt-2"
                        />

                        <div
                            class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer text-left"
                        >
                            <button
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                :class="{
                                    'border-t border-gray-200 rounded-t-none':
                                        i > 0,
                                    'rounded-b-none':
                                        i !=
                                        Object.keys(availableRoles).length - 1,
                                }"
                                @click="addTeamMemberForm.role = role.key"
                            >
                                <div
                                    :class="{
                                        'opacity-50':
                                            addTeamMemberForm.role &&
                                            addTeamMemberForm.role != role.key,
                                    }"
                                >
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm text-gray-600"
                                            :class="{
                                                'font-semibold':
                                                    addTeamMemberForm.role ==
                                                    role.key,
                                            }"
                                        >
                                            {{ role.name }}
                                        </div>

                                        <CheckCircleIcon
                                            v-if="
                                                addTeamMemberForm.role ==
                                                role.key
                                            "
                                            class="ml-2 h-5 w-5 text-green-400"
                                        />
                                    </div>

                                    <!-- Role Description -->
                                    <div
                                        class="mt-2 text-xs text-left text-gray-600"
                                    >
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <jet-action-message
                        :on="addTeamMemberForm.recentlySuccessful"
                        class="mr-3"
                    >
                        Added.
                    </jet-action-message>

                    <jet-button
                        :class="{ 'opacity-25': addTeamMemberForm.processing }"
                        :disabled="addTeamMemberForm.processing"
                    >
                        Add
                    </jet-button>
                </template>
            </jet-form-section>
        </div>

        <div
            v-if="
                team.team_invitations.length > 0 &&
                userPermissions.canAddTeamMembers
            "
        >
            <jet-section-border />

            <!-- Team Member Invitations -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title> Pending Team Invitations </template>

                <template #description>
                    These people have been invited to your team and have been
                    sent an invitation email. They may join the team by
                    accepting the email invitation.
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            v-for="invitation in team.team_invitations"
                            :key="invitation.id"
                            class="flex items-center justify-between"
                        >
                            <div class="text-gray-600">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelTeamInvitation(invitation)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <div v-if="team.users.length > 0">
            <jet-section-border />

            <!-- Manage Team Members -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title> Team Members </template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            v-for="user in team.users"
                            :key="user.id"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center">
                                <img
                                    class="w-8 h-8 rounded-full"
                                    :src="user.profile_photo_url"
                                    :alt="
                                        user.first_name + ' ' + user.last_name
                                    "
                                />
                                <div class="ml-4">
                                    {{ user.first_name }} {{ user.last_name }}
                                </div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button
                                    v-if="
                                        userPermissions.canAddTeamMembers &&
                                        availableRoles.length
                                    "
                                    class="ml-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div
                                    v-else-if="availableRoles.length"
                                    class="ml-2 text-sm text-gray-400"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Team -->
                                <button
                                    v-if="$page.props.user.id === user.id"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingTeam"
                                >
                                    Leave
                                </button>

                                <!-- Remove Team Member -->
                                <button
                                    v-if="userPermissions.canRemoveTeamMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmTeamMemberRemoval(user)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <!-- Role Management Modal -->
        <jet-dialog-modal
            :show="currentlyManagingRole"
            @close="currentlyManagingRole = false"
        >
            <template #title> Manage Role </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div
                        class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"
                    >
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                            :class="{
                                'border-t border-gray-200 rounded-t-none':
                                    i > 0,
                                'rounded-b-none':
                                    i !==
                                    Object.keys(availableRoles).length - 1,
                            }"
                            @click="updateRoleForm.role = role.key"
                        >
                            <div
                                :class="{
                                    'opacity-50':
                                        updateRoleForm.role &&
                                        updateRoleForm.role !== role.key,
                                }"
                            >
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div
                                        class="text-sm text-gray-600"
                                        :class="{
                                            'font-semibold':
                                                updateRoleForm.role ===
                                                role.key,
                                        }"
                                    >
                                        {{ role.name }}
                                    </div>

                                    <CheckCircleIcon
                                        v-if="updateRoleForm.role === role.key"
                                        class="ml-2 h-5 w-5 text-green-400"
                                    />
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="currentlyManagingRole = false">
                    Cancel
                </jet-secondary-button>

                <jet-button
                    class="ml-2"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                    @click="updateRole"
                >
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>

        <!-- Leave Team Confirmation Modal -->
        <jet-confirmation-modal
            :show="confirmingLeavingTeam"
            @close="confirmingLeavingTeam = false"
        >
            <template #title> Leave Team </template>

            <template #content>
                Are you sure you would like to leave this team?
            </template>

            <template #footer>
                <jet-secondary-button @click="confirmingLeavingTeam = false">
                    Cancel
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    :class="{ 'opacity-25': leaveTeamForm.processing }"
                    :disabled="leaveTeamForm.processing"
                    @click="leaveTeam"
                >
                    Leave
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>

        <!-- Remove Team Member Confirmation Modal -->
        <jet-confirmation-modal
            :show="teamMemberBeingRemoved"
            @close="teamMemberBeingRemoved = null"
        >
            <template #title> Remove Team Member </template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <jet-secondary-button @click="teamMemberBeingRemoved = null">
                    Cancel
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    :class="{ 'opacity-25': removeTeamMemberForm.processing }"
                    :disabled="removeTeamMemberForm.processing"
                    @click="removeTeamMember"
                >
                    Remove
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import { CheckCircleIcon } from "@heroicons/vue/24/outline";
export default {
    components: {
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        CheckCircleIcon,
    },

    props: ["team", "availableRoles", "userPermissions"],

    data() {
        return {
            addTeamMemberForm: this.$inertia.form({
                email: "",
                role: null,
            }),

            updateRoleForm: this.$inertia.form({
                role: null,
            }),

            leaveTeamForm: this.$inertia.form(),
            removeTeamMemberForm: this.$inertia.form(),

            currentlyManagingRole: false,
            managingRoleFor: null,
            confirmingLeavingTeam: false,
            teamMemberBeingRemoved: null,
        };
    },

    methods: {
        addTeamMember() {
            this.addTeamMemberForm.post(
                route("team-members.store", this.team),
                {
                    errorBag: "addTeamMember",
                    preserveScroll: true,
                    onSuccess: () => this.addTeamMemberForm.reset(),
                }
            );
        },

        cancelTeamInvitation(invitation) {
            this.$inertia.delete(
                route("team-invitations.destroy", invitation),
                {
                    preserveScroll: true,
                }
            );
        },

        manageRole(teamMember) {
            this.managingRoleFor = teamMember;
            this.updateRoleForm.role = teamMember.membership.role;
            this.currentlyManagingRole = true;
        },

        updateRole() {
            this.updateRoleForm.put(
                route("team-members.update", [this.team, this.managingRoleFor]),
                {
                    preserveScroll: true,
                    onSuccess: () => (this.currentlyManagingRole = false),
                }
            );
        },

        confirmLeavingTeam() {
            this.confirmingLeavingTeam = true;
        },

        leaveTeam() {
            this.leaveTeamForm.delete(
                route("team-members.destroy", [
                    this.team,
                    this.$page.props.user,
                ])
            );
        },

        confirmTeamMemberRemoval(teamMember) {
            this.teamMemberBeingRemoved = teamMember;
        },

        removeTeamMember() {
            this.removeTeamMemberForm.delete(
                route("team-members.destroy", [
                    this.team,
                    this.teamMemberBeingRemoved,
                ]),
                {
                    errorBag: "removeTeamMember",
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => (this.teamMemberBeingRemoved = null),
                }
            );
        },

        displayableRole(role) {
            return this.availableRoles.find((r) => r.key === role).name;
        },
    },
};
</script>
