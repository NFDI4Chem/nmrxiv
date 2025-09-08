<template>
    <!-- Page header with dynamic title -->
    <Head :title="'Samples - ' + project.data.name" />
    
    <!-- Main layout wrapper -->
    <project-layout :project="project" :selected-tab="tab">
        <template #project-content>
            <!-- Main content container -->
            <div
                class="pb-10 mb-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6"
            >
                <!-- Search controls section -->
                <div class="flex items-baseline justify-between">
                    <div>
                        <!-- Search bar - only shown when pagination is needed -->
                        <div
                            v-if="!loading && studies.meta && studies.meta.total > studies.meta.per_page"
                            class="flex items-center mr-4 w-full"
                        >
                            <!-- Search input container -->
                            <div
                                class="flex w-full bg-white shadow rounded-full"
                            >
                                <!-- Search input field -->
                                <input
                                    v-model="query"
                                    class="relative w-full border-0 px-6 py-3 rounded-full focus:shadow-outline"
                                    autocomplete="off"
                                    type="text"
                                    name="samples-search"
                                    placeholder="Search…"
                                />
                            </div>
                            
                            <!-- Search action button -->
                            <button
                                type="button"
                                class="ml-2 inline-flex items-center rounded-full px-6 py-3 shadow rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                                @click="update()"
                            >
                                GO
                            </button>
                            
                            <!-- Reset search button -->
                            <button
                                class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-indigo-500"
                                type="button"
                                @click="reset()"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Main content area -->
                <div v-if="!loading && studies.data">
                    <!-- Empty state when no studies found -->
                    <div v-if="studies.data.length <= 0">
                        <div class="mt-4 px-12 py-8 mx-auto max-w-4xl">
                            <div
                                class="px-6 py-4 bg-white shadow-md rounded-lg"
                            >
                                <div class="flex items-center">
                                    <!-- Empty state icon -->
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        class="h-6 w-6"
                                    >
                                        <path
                                            d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z"
                                            class="fill-current text-gray-400"
                                        ></path>
                                        <polygon
                                            points="21 6 12 10 12 22 21 18"
                                            class="fill-current text-gray-600"
                                        ></polygon>
                                    </svg>
                                    <!-- Empty state message -->
                                    <div
                                        class="ml-3 font-semibold text-sm text-gray-600 uppercase tracking-wider"
                                    >
                                        No samples
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Studies grid display -->
                    <div v-else>
                        <!-- Responsive grid layout for study cards -->
                        <div
                            class="mt-8 mx-auto max-w-md grid gap-8 sm:max-w-lg lg:grid-cols-4 lg:max-w-7xl"
                        >
                            <!-- Individual study card -->
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
                        
                        <!-- Pagination controls -->
                        <div
                            v-if="
                                studies.meta &&
                                studies.meta.total > studies.meta.per_page
                            "
                            class="block w-100 mt-10"
                        >
                            <!-- Pagination navigation -->
                            <nav
                                class="border-t border-gray-200 px-4 flex items-center justify-between sm:px-0"
                            >
                                <!-- Left spacer -->
                                <div class="-mt-px w-0 flex-1 flex">&nbsp;</div>
                                
                                <!-- Pagination links (desktop only) -->
                                <div class="hidden md:-mt-px md:flex">
                                    <!-- Individual pagination link -->
                                    <div
                                        v-for="link in studies.meta.links"
                                        :key="link.url"
                                        :class="[
                                            link.active
                                                ? 'text-teal-600 border-teal-500'
                                                : '',
                                            'cursor-pointer border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium',
                                        ]"
                                        @click="update(link)"
                                        v-html="link.label"
                                    ></div>
                                </div>
                                
                                <!-- Right spacer -->
                                <div class="-mt-px w-0 flex-1 flex justify-end">
                                    &nbsp;
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                
                <!-- Loading state -->
                <div v-else class="text-gray-400 mt-10">
                    <!-- Loading spinner -->
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
/**
 * Project Samples Page Component
 * 
 * Displays a paginated list of studies (samples) for a specific project.
 * Includes search functionality and loading states.
 */

import ProjectLayout from "@/Pages/Public/Project/Layout.vue";
import StudyCard from "@/Shared/StudyCardPublic.vue";
import { Head } from "@inertiajs/vue3";

export default {
    name: 'ProjectSamples',
    
    components: {
        ProjectLayout,
        StudyCard,
        Head,
    },
    
    props: {
        /** Project data object */
        project: {
            type: Object,
            required: true
        },
        /** Active tab identifier */
        tab: {
            type: String,
            required: true
        }
    },
    
    data() {
        return {
            /** Loading state indicator */
            loading: false,
            /** Studies/samples data with pagination meta */
            studies: [],
            /** Search query string */
            query: "",
        };
    },
    
    mounted() {
        // Initialize studies data on component mount
        if (this.project) {
            this.fetchStudies(route("project.studies", this.project.data.id));
        }
    },
    
    methods: {
        /**
         * Fetch studies data from API
         * @param {string} url - API endpoint URL
         */
        fetchStudies(url) {
            axios.get(url).then((response) => {
                this.loading = false;
                this.studies = response.data;
            });
        },
        
        /**
         * Update studies list with search or pagination
         * @param {Object|null} link - Pagination link object
         */
        update(link) {
            // Create search link if query exists but no link provided
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
        
        /**
         * Reset search and return to first page
         */
        reset() {
            let link = {};
            link["url"] =
                route("project.studies", this.project.data.id) + "?page=1";
            this.query = "";
            this.loading = true;
            this.executeQuery(link);
        },
        
        /**
         * Execute search query with current search term
         * @param {Object} link - Link object containing URL
         */
        executeQuery(link) {
            this.fetchStudies(link.url + "&search=" + this.query);
        },
    },
};
</script>
