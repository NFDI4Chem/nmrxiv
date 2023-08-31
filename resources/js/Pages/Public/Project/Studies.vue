<template>
    <project-layout :project="project" :selectedTab="tab">
        <template #project-content>
            <div class="p-8">
                <div class="flex items-baseline justify-between">
                    <div>
                        <h2 class="text-lg mb-3 font-bold">Studies</h2>
                        <div
                            v-if="!loading"
                            class="flex items-center mr-4 w-full"
                        >
                            <div
                                class="flex w-full bg-white shadow rounded-full"
                            >
                                <input
                                    v-model="query"
                                    class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline"
                                    autocomplete="off"
                                    type="text"
                                    name="search"
                                    placeholder="Search…"
                                />
                            </div>
                            <button
                                type="button"
                                class="ml-2 inline-flex items-center rounded-full px-6 py-3 shadow rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="update()"
                            >
                                GO
                            </button>
                            <button
                                @click="reset()"
                                class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
                                type="button"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="!loading && studies.data">
                    <div v-if="studies.data.length <= 0">
                        <div class="mt-4 px-12 py-8 mx-auto max-w-4xl">
                            <div
                                class="px-6 py-4 bg-white shadow-md rounded-lg"
                            >
                                <div class="flex items-center">
                                    <CubeTransparentIcon class="h-6 w-6" />

                                    <div
                                        class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider"
                                    >
                                        No studies
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div
                            class="mt-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-3 lg:max-w-7xl"
                        >
                            <div
                                v-for="study in studies.data"
                                :key="study.uuid"
                            >
                                <study-card
                                    :project="project.data"
                                    :study="study"
                                />
                            </div>
                        </div>
                        <div
                            v-if="studies.meta.total > studies.meta.per_page"
                            class="block w-100 mt-10"
                        >
                            <nav
                                class="border-t border-gray-200 px-4 flex items-center justify-between sm:px-0"
                            >
                                <div class="-mt-px w-0 flex-1 flex">&nbsp;</div>
                                <div class="hidden md:-mt-px md:flex">
                                    <div
                                        v-for="link in studies.meta.links"
                                        :key="link.url"
                                        @click="update(link)"
                                        v-html="link.label"
                                        :class="[
                                            link.active
                                                ? 'text-teal-600 border-teal-500'
                                                : '',
                                            'cursor-pointer border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium',
                                        ]"
                                    ></div>
                                </div>
                                <div class="-mt-px w-0 flex-1 flex justify-end">
                                    &nbsp;
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="text-gray-400 mt-10" v-else>
                    <svg
                        class="animate-spin inline -ml-1 mr-2 h-5 w-5 text-dark"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Loading...
                </div>
            </div>
        </template>
    </project-layout>
</template>

<script>
import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import StudyCard from "@/Shared/StudyCardPublic.vue";
import { CubeTransparentIcon } from "@heroicons/vue/24/outline";

export default {
    components: {
        ProjectLayout,
        StudyCard,
        CubeTransparentIcon,
    },
    props: ["project", "tab"],
    data() {
        return {
            loading: false,
            studies: [],
            query: "",
        };
    },
    computed: {},
    mounted() {
        if (this.project) {
            this.fetchStudies(route("project.studies", this.project.data.id));
        }
    },
    methods: {
        fetchStudies(url) {
            axios.get(url).then((response) => {
                this.loading = false;
                this.studies = response.data;
            });
        },
        update(link) {
            if (!link && this.query != "") {
                link = {};
                link["url"] =
                    route("project.studies", this.project.data.id) + "?page=1";
            }
            if (link.url) {
                this.loading = true;
                this.executeQuery(link);
            }
        },
        reset() {
            let link = {};
            link["url"] =
                route("project.studies", this.project.data.id) + "?page=1";
            this.query = "";
            this.loading = true;
            this.executeQuery(link);
        },
        executeQuery(link) {
            this.fetchStudies(link.url + "&search=" + this.query);
        },
    },
};
</script>
