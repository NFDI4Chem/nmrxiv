<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <div class="gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                    <div class="sm:col-span-6">
                        <h2 class="text-xl font-extrabold text-blue-gray-900">
                            Studies ({{ project.data.studies.length }})
                        </h2>
                        <!-- <p class="mt-1 text-sm text-blue-gray-500 mb-4">
                            This information will be displayed publicly so be
                            careful what you share.
                        </p> -->
                    </div>
                    <div class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div
                            v-for="study in project.data.studies.sort((a, b) =>
                                a.name > b.name ? 1 : -1
                            )"
                            :key="study.uuid"
                        >
                            <study-card
                                :project="project.data"
                                :study="study"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import StudyCard from "@/Shared/StudyCardPublic.vue";

export default {
    components: {
        ProjectLayout,
        StudyCard,
    },
    props: ["project", "tab"],
    data() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {},
};
</script>
