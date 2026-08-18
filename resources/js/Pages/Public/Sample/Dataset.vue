<template>
    <Head :title="dataset.data.name + ' - ' + study.data.name" />
    <sample-layout :study="study.data">
        <template #sample-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <PublicDatasetBody :study="study" :dataset="dataset" />
            </div>
        </template>
    </sample-layout>
    <component :is="'script'" type="application/ld+json">{{
        schema
    }}</component>
</template>

<script>
import SampleLayout from "@/Pages/Public/Sample/Layout.vue";
import PublicDatasetBody from "@/Shared/Public/PublicDatasetBody.vue";
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        SampleLayout,
        PublicDatasetBody,
        Head,
    },
    props: ["tab", "study", "dataset"],
    data() {
        return {
            schema: {},
        };
    },
    mounted() {
        if (this.dataset?.data?.identifier) {
            axios
                .get(route("bioschemas.id", this.dataset.data.identifier))
                .then((response) => {
                    this.schema = response.data;
                });
        }
    },
};
</script>
