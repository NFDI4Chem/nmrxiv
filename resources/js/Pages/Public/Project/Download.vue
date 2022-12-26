<template>
    <project-layout :project="project" :selectedTab="download">
        <template #project-content>
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
            >
                <div class="mt-3 space-y-8 divide-y divide-y-blue-gray-200">
                    <div>
                        <h3 class="text-xl font-extrabold text-blue-gray-900">
                            Download - {{ project.data.name }}
                        </h3>
                    </div>
                    <div class="pt-8 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="text-medium font-medium text-gray-900"
                            >
                                <p>
                                    Download files of <b>{{ project.data.name }}</b> in
                                    compressed zip format
                                </p>
                            </div>
                            <div class="float-left md:float-right">
                                <a
                                    class="md:ml-4 cursor-pointer relative inline-flex items-center px-4 py-1 rounded-full border border-gray-300 bg-white text-sm font-black text-dark hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                    :href="downloadURL"
                                >
                                    Download
                                </a>
                            </div>
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
    computed: {
        downloadURL() {
            return (
                this.url +
                "/" +
                this.project.data.owner.username +
                "/datasets/" +
                this.project.data.slug
            );
        },
        url() {
            return String(this.$page.props.url);
        },
    },
    mounted() {},
    methods: {},
};
</script>
