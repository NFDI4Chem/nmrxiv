<template>
    <app-layout title="Create Team">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <h2
                            class="font-semibold text-xl text-gray-800 leading-tight"
                        >
                            Create Team
                        </h2>
                    </div>
                </div>
            </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <create-team-form />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import CreateTeamForm from "@/Pages/Teams/Partials/CreateTeamForm.vue";

export default {
    components: {
        AppLayout,
        CreateTeamForm,
    },
};
</script>
