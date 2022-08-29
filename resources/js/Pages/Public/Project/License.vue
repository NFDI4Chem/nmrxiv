<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <h3 class="text-xl font-extrabold text-blue-gray-900">
                    {{ project.data.license.title }}
                </h3>

                <div class="mt-3 space-y-8 divide-y divide-y-blue-gray-200">
                    <div
                        class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6"
                    >
                        <div class="sm:col-span-6">
                            <p
                                style="max-width: 100ch !important"
                                class="prose mt-1 text-sm text-blue-gray-500"
                                v-html="project.data.license.body"
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";

export default {
    components: {
        ProjectLayout,
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
