<template>
    <app-layout title="Dashboard">
        <template #header>
            <div class="bg-white border-b">
                <div class="px-12">
                    <div class="flex flex-nowrap justify-between py-6">
                        <div>
                            <div
                                class="flex items-center text-sm text-gray-700 uppercase font-bold tracking-widest"
                            >
                                <div v-if="team.personal_team">Your</div>
                                <div v-else>
                                    {{ user.current_team.name }}
                                </div>
                                &nbsp;Dashboard
                            </div>
                            <div
                                v-if="team.users"
                                class="flex mt-3 flex-row-reverse justify-end"
                            >
                                <img
                                    v-for="user in team.users"
                                    :key="user.id"
                                    class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                                <img
                                    class="w-8 h-8 -mr-2 rounded-full border-2 border-white"
                                    :src="team.owner.profile_photo_url"
                                    :alt="team.owner.name"
                                />
                            </div>
                        </div>
                        <div v-if="!team.personal_team">
                            <Link
                                :href="'/teams/' + user.current_team.id"
                                class="text-sm text-gray-800 font-bold"
                            >
                                Team Settings
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div
            v-if="projects.length > 0 || samples.length > 0"
            class="px-8 py-8 mx-auto max-w-4xl"
        >
            <div>
                <div class="sm:hidden">
                    <label for="tabs" class="sr-only">Select a tab</label>
                    <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
                    <select
                        id="tabs"
                        name="tabs"
                        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option
                            :selected="selectedTab === 'projects'"
                            value="projects"
                        >
                            Projects
                        </option>
                        <option
                            :selected="selectedTab === 'samples'"
                            value="samples"
                        >
                            Samples
                        </option>
                    </select>
                </div>
                <div class="hidden sm:block">
                    <nav class="flex space-x-4" aria-label="Tabs">
                        <!-- Current: "bg-indigo-100 text-indigo-700", Default: "text-gray-500 hover:text-gray-700" -->
                        <a
                            href="?tab=projects"
                            :class="[
                                selectedTab == 'projects'
                                    ? 'bg-indigo-100 text-indigo-700'
                                    : '',
                                'rounded-md px-3 py-2 text-sm font-medium',
                            ]"
                            aria-current="page"
                            >Projects</a
                        >
                        <a
                            href="?tab=samples"
                            :class="[
                                selectedTab == 'samples'
                                    ? 'bg-indigo-100 text-indigo-700'
                                    : '',
                                'rounded-md px-3 py-2 text-sm font-medium',
                            ]"
                            >Samples</a
                        >
                    </nav>
                </div>
            </div>
            <div v-if="selectedTab == 'projects'">
                <div v-if="projects.length > 0" class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm text-gray-600">
                            <span v-if="filteredProjects.length > 0">
                                Showing {{ ((currentProjectsPage - 1) * projectsPerPage) + 1 }} to 
                                {{ Math.min(currentProjectsPage * projectsPerPage, filteredProjects.length) }} 
                                of {{ filteredProjects.length }} 
                                {{ selectedProjectStatus !== 'all' ? selectedProjectStatus + ' ' : '' }}projects
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Status Filter Dropdown -->
                            <StatusFilter v-model="selectedProjectStatus" />
                            
                            <!-- Search Input -->
                            <div class="w-72">
                                <SearchInput
                                    v-model="searchProjectQuery"
                                    name="project-search"
                                    placeholder="Search projects..."
                                    @reset="searchProjectQuery = ''"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="projects.length > 0 && filteredProjects.length > 0">
                    <team-projects
                        :team="team"
                        :team-role="teamRole"
                        :mode="'create'"
                        :projects="paginatedProjects"
                    ></team-projects>
                </div>
                
                <!-- Show original TeamProjects empty state only when there are genuinely no projects and no filters applied -->
                <div v-if="projects.length === 0">
                    <team-projects
                        :team="team"
                        :team-role="teamRole"
                        :mode="'create'"
                        :projects="[]"
                    ></team-projects>
                </div>
                
                <!-- Empty search/filter results message for projects -->
                <div v-if="projects.length > 0 && (searchProjectQuery || selectedProjectStatus !== 'all') && filteredProjects.length === 0" class="text-center py-12">
                    <EmptySearchState
                        entity-type="projects"
                        :search-query="searchProjectQuery || (selectedProjectStatus !== 'all' ? `status: ${selectedProjectStatus}` : '')"
                        :title="searchProjectQuery ? 'No matching projects found' : `No ${selectedProjectStatus} projects found`"
                        @clear-search="clearProjectFilters"
                    />
                </div>
                
                <div v-if="projects.length > 0 && totalProjectPages > 1" class="flex items-center justify-between px-6 py-3 border-t bg-white mt-4">
                    <div class="text-sm text-gray-600">
                        Page {{ currentProjectsPage }} of {{ totalProjectPages }}
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded border text-sm disabled:opacity-50"
                            :disabled="currentProjectsPage === 1"
                            @click="currentProjectsPage = Math.max(1, currentProjectsPage - 1)"
                        >
                            Previous
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded border text-sm disabled:opacity-50"
                            :disabled="currentProjectsPage === totalProjectPages"
                            @click="currentProjectsPage = Math.min(totalProjectPages, currentProjectsPage + 1)"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="selectedTab == 'samples'">
                <div v-if="samples.length > 0" class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm text-gray-600">
                            <span v-if="filteredSamples.length > 0">
                                Showing {{ ((currentSamplesPage - 1) * samplesPerPage) + 1 }} to 
                                {{ Math.min(currentSamplesPage * samplesPerPage, filteredSamples.length) }} 
                                of {{ filteredSamples.length }} 
                                {{ selectedSampleStatus !== 'all' ? selectedSampleStatus + ' ' : '' }}samples
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Status Filter Dropdown for Samples -->
                            <StatusFilter v-model="selectedSampleStatus" />
                            
                            <!-- Search Input -->
                            <div class="w-72">
                                <SearchInput
                                    v-model="searchSampleQuery"
                                    name="sample-search"
                                    placeholder="Search samples..."
                                    @reset="searchSampleQuery = ''"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="samples.length > 0 && filteredSamples.length > 0">
                    <team-samples
                        :team="team"
                        :team-role="teamRole"
                        :mode="'create'"
                        :studies="paginatedSamples"
                    ></team-samples>
                </div>
                
                <!-- Show original TeamSamples empty state only when there are genuinely no samples -->
                <div v-if="samples.length === 0">
                    <team-samples
                        :team="team"
                        :team-role="teamRole"
                        :mode="'create'"
                        :studies="[]"
                    ></team-samples>
                </div>
                
                <!-- Empty search/filter results message for samples -->
                <div v-if="samples.length > 0 && (searchSampleQuery || selectedSampleStatus !== 'all') && filteredSamples.length === 0" class="text-center py-12">
                    <EmptySearchState
                        entity-type="samples"
                        :search-query="searchSampleQuery || (selectedSampleStatus !== 'all' ? `status: ${selectedSampleStatus}` : '')"
                        :title="searchSampleQuery ? 'No matching samples found' : `No ${selectedSampleStatus} samples found`"
                        @clear-search="clearSampleFilters"
                    />
                </div>
                
                <div v-if="samples.length > 0 && totalSamplePages > 1" class="flex items-center justify-between px-6 py-3 border-t bg-white mt-4">
                    <div class="text-sm text-gray-600">
                        Page {{ currentSamplesPage }} of {{ totalSamplePages }}
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded border text-sm disabled:opacity-50"
                            :disabled="currentSamplesPage === 1"
                            @click="currentSamplesPage = Math.max(1, currentSamplesPage - 1)"
                        >
                            Previous
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded border text-sm disabled:opacity-50"
                            :disabled="currentSamplesPage === totalSamplePages"
                            @click="currentSamplesPage = Math.min(totalSamplePages, currentSamplesPage + 1)"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="max-w-lg my-6 py-6 mx-auto">
                <div class="text-center">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            vector-effect="non-scaling-stroke"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                        />
                    </svg>
                    <h3 class="mt-2 text-md font-medium text-gray-900">
                        You have no <b>projects</b> or <b>samples</b> yet
                    </h3>
                    <div v-if="editableTeamRole" class="mt-2">
                        <p class="mb-1 text-sm text-gray-500">
                            Get started by uploading your data.
                        </p>
                        <create class="mt-5" mode="button"></create>
                        <span
                            class="float-center text-xs cursor-pointer hover:text-blue-700 mt-2"
                        >
                            <a
                                href="https://docs.nmrxiv.org/submission-guides/submission-process.html"
                                target="_blank"
                                >Need Help?
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-12 py-8 mx-auto max-w-4xl">
            <ul
                role="list"
                class="mt-6 border-b border-gray-200 divide-y divide-gray-200"
            >
                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-pink-500"
                            >
                                <!-- Heroicon name: outline/speakerphone -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-submission-guide"
                                    href="https://docs.nmrxiv.org/introduction/intro"
                                    target="_blank"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Get started! How to use nmrXiv?
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Documentation for using nmrXiv. Explore, learn
                                and archive NMR datasets.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-purple-500"
                            >
                                <!-- Heroicon name: outline/terminal -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a
                                    id="tour-step-api"
                                    href="https://docs.nmrxiv.org/developer-guides/api.html"
                                    target="_blank"
                                >
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Public API Documentation
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Search, interact and download NMR Datasets as a
                                part of your software or your data science
                                workflow.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="relative group py-4 flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <span
                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-yellow-500"
                            >
                                <!-- Heroicon name: outline/calendar -->
                                <svg
                                    class="h-6 w-6 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-medium text-gray-900">
                                <a id="tour-step-spectra-challenge" href="/">
                                    <span
                                        class="absolute inset-0"
                                        aria-hidden="true"
                                    ></span>
                                    Challenges
                                </a>
                            </div>
                            <p class="text-sm text-gray-500">
                                Structure elucidation challenges are designed
                                for researchers at all different stages of their
                                careers.
                            </p>
                        </div>
                        <div class="flex-shrink-0 self-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg
                                class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="mt-6 flex">
                <a
                    id="tour-step-get-in-touch"
                    :href="mailTo"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >Or get in touch<span aria-hidden="true"> &rarr;</span></a
                >
            </div>
        </div>
        <onboarding></onboarding>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import TeamProjects from "@/Pages/Project/Index.vue";
import TeamSamples from "@/Shared/Samples.vue";
import Create from "@/Shared/CreateButton.vue";
import Onboarding from "@/App/Onboarding.vue";
import SearchInput from "@/Shared/SearchInput.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import StatusFilter from "@/Shared/StatusFilter.vue";
import { useMagicKeys } from "@vueuse/core";
import { getCurrentInstance } from "vue";
import { watchEffect } from "vue";
import { Link } from "@inertiajs/vue3";

const { meta, u } = useMagicKeys();

export default {
    components: {
        AppLayout,
        TeamProjects,
        TeamSamples,
        Create,
        Onboarding,
        SearchInput,
        EmptySearchState,
        StatusFilter,
        Link,
    },
    props: ["user", "team", "projects", "samples", "teamRole", "filters"],
    setup() {
        const app = getCurrentInstance();
        const openDatasetCreateDialog = (data) => {
            app.appContext.config.globalProperties.emitter.emit(
                "openDatasetCreateDialog",
                data
            );
        };
        watchEffect(() => {
            if (meta.value && u.value) {
                openDatasetCreateDialog({
                    draft_id: null,
                });
            }
        });

        return {
            openDatasetCreateDialog,
        };
    },
    data() {
        return {
            selectedTab: "projects",
            searchProjectQuery: "",
            currentProjectsPage: 1,
            projectsPerPage: 10,
            searchSampleQuery: "",
            currentSamplesPage: 1,
            samplesPerPage: 10,
            searchDebounceTimer: null,
            selectedProjectStatus: "all",
            selectedSampleStatus: "all",
        };
    },

    computed: {
        mailFromAddress() {
            return String(this.$page.props.mailFromAddress);
        },

        mailTo() {
            return "mailto:" + String(this.$page.props.mailFromAddress);
        },

        filteredProjects() {
            let filtered = this.projects;

            // Apply status filter first
            if (this.selectedProjectStatus !== "all") {
                filtered = filtered.filter((project) => {
                    return project.status === this.selectedProjectStatus;
                });
            }

            // Apply search filter
            if (this.searchProjectQuery) {
                const q = this.searchProjectQuery.toLowerCase().trim();
                filtered = filtered.filter((project) => {
                    const name = (project.name || "").toLowerCase();
                    const description = (project.description || "").toLowerCase();
                    const idText = String(project.id || "").toLowerCase();
                    const uuid = String(project.uuid || "").toLowerCase();
                    return (
                        name.includes(q) ||
                        description.includes(q) ||
                        idText.includes(q) ||
                        uuid.includes(q)
                    );
                });
            }

            return filtered;
        },

        paginatedProjects() {
            const start = (this.currentProjectsPage - 1) * this.projectsPerPage;
            return this.filteredProjects.slice(start, start + this.projectsPerPage);
        },

        totalProjectPages() {
            return Math.max(
                1,
                Math.ceil(this.filteredProjects.length / this.projectsPerPage)
            );
        },

        editableTeamRole() {
            return (
                this.teamRole &&
                (this.teamRole == "owner" || this.teamRole == "admin")
            );
        },

        filteredSamples() {
            let filtered = this.samples;

            // Apply status filter first
            if (this.selectedSampleStatus !== "all") {
                filtered = filtered.filter((sample) => {
                    return sample.status === this.selectedSampleStatus;
                });
            }

            // Apply search filter
            if (this.searchSampleQuery) {
                const q = this.searchSampleQuery.toLowerCase().trim();
                filtered = filtered.filter((sample) => {
                    const name = (sample.name || "").toLowerCase();
                    const description = (sample.description || "").toLowerCase();
                    const idText = String(sample.id || "").toLowerCase();
                    const uuid = String(sample.uuid || "").toLowerCase();
                    return (
                        name.includes(q) ||
                        description.includes(q) ||
                        idText.includes(q) ||
                        uuid.includes(q)
                    );
                });
            }

            return filtered;
        },

        paginatedSamples() {
            const start = (this.currentSamplesPage - 1) * this.samplesPerPage;
            return this.filteredSamples.slice(start, start + this.samplesPerPage);
        },

        totalSamplePages() {
            return Math.max(
                1,
                Math.ceil(this.filteredSamples.length / this.samplesPerPage)
            );
        },
    },

    watch: {
        searchProjectQuery() {
            // Debounce the search query to avoid excessive pagination resets
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
            this.searchDebounceTimer = setTimeout(() => {
                this.currentProjectsPage = 1;
            }, 300); // 300ms debounce delay
        },
        selectedProjectStatus() {
            // Reset to first page when status filter changes
            this.currentProjectsPage = 1;
        },
        selectedSampleStatus() {
            // Reset to first page when status filter changes
            this.currentSamplesPage = 1;
        },
        searchSampleQuery() {
            // Debounce the search query to avoid excessive pagination resets
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }
            this.searchDebounceTimer = setTimeout(() => {
                this.currentSamplesPage = 1;
            }, 300); // 300ms debounce delay
        },
    },

    mounted() {
        if (this.filters.action == "submission") {
            this.emitter.emit("openDatasetCreateDialog", {
                draft_id: this.filters.draft_id,
            });
        }

        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        this.selectedTab = params["tab"] ? params["tab"] : "projects";
    },

    beforeUnmount() {
        // Clean up the search debounce timer to prevent memory leaks
        if (this.searchDebounceTimer) {
            clearTimeout(this.searchDebounceTimer);
        }
    },
    methods: {
        clearProjectFilters() {
            this.searchProjectQuery = "";
            this.selectedProjectStatus = "all";
        },
        clearSampleFilters() {
            this.searchSampleQuery = "";
            this.selectedSampleStatus = "all";
        },
    },
};
</script>
