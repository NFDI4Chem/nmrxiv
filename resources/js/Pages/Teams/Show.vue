<template>
    <app-layout title="Team Settings">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <h2
                            class="font-semibold text-xl text-gray-800 leading-tight"
                        >
                            Team Settings
                        </h2>
                    </div>
                </div>
            </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-team-name-form
                    :team="team"
                    :permissions="permissions"
                />

                <team-member-manager
                    class="mt-10 sm:mt-0"
                    :team="team"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                />

                <template
                    v-if="permissions.canDeleteTeam && !team.personal_team"
                >
                    <jet-section-border />

                    <delete-team-form class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager.vue";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm.vue";

export default {
    components: {
        AppLayout,
        DeleteTeamForm,
        JetSectionBorder,
        TeamMemberManager,
        UpdateTeamNameForm,
    },
    props: ["team", "availableRoles", "permissions"],
};
</script>
