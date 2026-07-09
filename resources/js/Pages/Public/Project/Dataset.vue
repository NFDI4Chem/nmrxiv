<template>
    <Head :title="dataset.data.name + ' - ' + study.data.name" />
    <project-layout
        :project="project"
        :selected-tab="tab"
        :current-study="study"
    >
        <template #project-content>
            <div class="pb-10 mb-10 pt-4 pb-6">
                <PublicDatasetBody
                    :study="study"
                    :dataset="dataset"
                    :project="project"
                />
            </div>
        </template>
    </project-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import PublicDatasetBody from "@/Shared/Public/PublicDatasetBody.vue";
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        ProjectLayout,
        PublicDatasetBody,
        Head,
    },
    props: ["project", "tab", "study", "dataset"],
    data() {
        return {
            schema: {},
        };
    },
    mounted() {
        axios
            .get(route("bioschemas.id", this.dataset.data.identifier))
            .then((response) => {
                this.schema = response.data;
            });
    },
};
</script>
