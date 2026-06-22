<template>
    <app-layout :title="pageTitle">
        <template #header>
            <div class="relative border-b border-zinc-900/5 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"
                ></div>
                <div class="relative mx-8 py-10 sm:py-12">
                    <h1
                        class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900"
                    >
                        Search results
                    </h1>
                    <p v-if="query" class="mt-2 text-gray-600">
                        Results for
                        <span class="font-semibold text-gray-900"
                            >"{{ query }}"</span
                        >
                    </p>
                    <form
                        class="mt-6 flex w-full max-w-none items-center gap-2 rounded-full border border-gray-200 bg-white py-1 pl-4 pr-2 shadow-sm focus-within:ring-2 focus-within:ring-gray-900"
                        @submit.prevent="submitSearch"
                    >
                        <MagnifyingGlassIcon
                            class="h-5 w-5 shrink-0 text-gray-400"
                            aria-hidden="true"
                        />
                        <input
                            v-model="searchInput"
                            type="search"
                            class="min-w-0 flex-1 border-0 bg-transparent py-2 text-base text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-0"
                            placeholder="Search projects, samples, and spectra..."
                            aria-label="Search query"
                        />
                        <button
                            type="submit"
                            class="shrink-0 rounded-full bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800"
                            :disabled="loading"
                        >
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </template>

        <div
            class="min-h-[calc(100vh-400px)] w-full px-6 sm:px-6 lg:px-8 mb-24"
        >
            <div
                v-if="loading"
                class="pt-16 text-center text-sm text-gray-500"
                role="status"
                aria-live="polite"
            >
                Searching…
            </div>

            <div v-else-if="errorMessage" class="pt-12">
                <EmptySearchState
                    entity-type="projects"
                    :search-query="searchInput"
                    title="Search could not be completed"
                    :message="errorMessage"
                    :show-clear-button="false"
                />
            </div>

            <div v-else-if="query && !hasAnyResults" class="w-full pt-12">
                <EmptySearchState
                    entity-type="projects"
                    :search-query="query"
                    title="No results found"
                    :message="emptyMessage"
                    :show-clear-button="true"
                    @clear-search="clearSearch"
                />
            </div>

            <template v-else-if="query">
                <section
                    v-if="projects.meta.total > 0"
                    id="projects"
                    class="border-t border-gray-100 pt-10"
                >
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                Projects
                            </h2>
                            <p class="text-sm text-gray-600">
                                {{ projects.meta.total }}
                                {{
                                    projects.meta.total === 1
                                        ? "project"
                                        : "projects"
                                }}
                            </p>
                        </div>
                        <InertiaLink
                            v-if="projects.meta.total > 1"
                            :href="projectsBrowseUrl"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                        >
                            View all matching projects
                        </InertiaLink>
                    </div>
                    <div
                        class="mx-auto mt-6 grid w-full gap-6 sm:gap-8 lg:grid-cols-4 2xl:grid-cols-6"
                    >
                        <span
                            v-for="project in projects.data"
                            :key="project.id"
                            class="block min-w-0 overflow-visible"
                        >
                            <ProjectCard :project="project" mode="grid" />
                        </span>
                    </div>
                    <TextSearchSectionPagination
                        :meta="projects.meta"
                        navigation-label="Projects pagination"
                        @page-change="(page) => changeProjectsPage(page)"
                    />
                </section>

                <section
                    v-if="studies.meta.total > 0"
                    id="samples"
                    class="border-t border-gray-100 pt-10 mt-10"
                >
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                Samples
                            </h2>
                            <p class="text-sm text-gray-600">
                                {{ studies.meta.total }}
                                {{
                                    studies.meta.total === 1
                                        ? "sample"
                                        : "samples"
                                }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="mx-auto mt-6 grid w-full gap-6 sm:gap-8 lg:grid-cols-4 2xl:grid-cols-6"
                    >
                        <span
                            v-for="study in studies.data"
                            :key="study.id"
                            class="block min-w-0 overflow-visible"
                        >
                            <StudyCardPublic :study="study" />
                        </span>
                    </div>
                    <TextSearchSectionPagination
                        :meta="studies.meta"
                        navigation-label="Samples pagination"
                        @page-change="(page) => changeStudiesPage(page)"
                    />
                </section>

                <section
                    v-if="datasets.meta.total > 0"
                    id="spectra"
                    class="border-t border-gray-100 pt-10 mt-10"
                >
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                Spectra
                            </h2>
                            <p class="text-sm text-gray-600">
                                {{ datasets.meta.total }}
                                {{
                                    datasets.meta.total === 1
                                        ? "spectrum"
                                        : "spectra"
                                }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="mx-auto mt-6 grid w-full gap-6 sm:gap-8 lg:grid-cols-4 2xl:grid-cols-6"
                    >
                        <span
                            v-for="dataset in datasets.data"
                            :key="dataset.id"
                            class="block min-w-0 overflow-visible"
                        >
                            <DatasetCard :dataset="dataset" mode="grid" />
                        </span>
                    </div>
                    <TextSearchSectionPagination
                        :meta="datasets.meta"
                        navigation-label="Spectra pagination"
                        @page-change="(page) => changeDatasetsPage(page)"
                    />
                </section>
            </template>
        </div>
    </app-layout>
</template>

<script>
import { computed, ref, watch } from "vue";
import { Link as InertiaLink, router } from "@inertiajs/vue3";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProjectCard from "@/Shared/ProjectCard.vue";
import StudyCardPublic from "@/Shared/StudyCardPublic.vue";
import DatasetCard from "@/Shared/DatasetCard.vue";
import TextSearchSectionPagination from "@/Shared/TextSearchSectionPagination.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import {
    SEARCH_SCOPE,
    emptyCatalogResults,
    fetchCatalogSearch,
    readCatalogParamsFromUrl,
    syncCatalogBrowserUrl,
} from "@/Utils/unifiedSearchApi.js";

export default {
    components: {
        AppLayout,
        InertiaLink,
        MagnifyingGlassIcon,
        ProjectCard,
        StudyCardPublic,
        DatasetCard,
        TextSearchSectionPagination,
        EmptySearchState,
    },
    props: {
        initialQuery: {
            type: String,
            default: "",
        },
        perPage: {
            type: Number,
            default: 12,
        },
    },
    setup(props) {
        const searchInput = ref(props.initialQuery || "");
        const query = ref("");
        const loading = ref(false);
        const errorMessage = ref(null);
        const results = ref(emptyCatalogResults());
        const pageState = ref({
            per_page: props.perPage,
            projects_page: 1,
            studies_page: 1,
            datasets_page: 1,
        });

        const projects = computed(() => results.value.projects);
        const studies = computed(() => results.value.studies);
        const datasets = computed(() => results.value.datasets);

        const pageTitle = computed(() =>
            query.value ? `Search: ${query.value}` : "Search"
        );

        const hasAnyResults = computed(
            () =>
                projects.value.meta.total > 0 ||
                studies.value.meta.total > 0 ||
                datasets.value.meta.total > 0
        );

        const projectsBrowseUrl = computed(() => {
            const params = new URLSearchParams({ search: query.value });
            return `/projects?${params.toString()}`;
        });

        const emptyMessage = computed(
            () =>
                `We could not find public projects, samples, or spectra matching "${query.value}". Try different keywords or fewer terms.`
        );

        const applyApiResponse = (data) => {
            query.value = data.query || "";
            results.value = {
                query: data.query || "",
                tokens: data.tokens || [],
                projects: data.projects || emptyCatalogResults().projects,
                studies: data.studies || emptyCatalogResults().studies,
                datasets: data.datasets || emptyCatalogResults().datasets,
            };
        };

        const fetchResults = async (overrides = {}) => {
            const q = (overrides.q ?? searchInput.value).trim();
            if (!q) {
                query.value = "";
                results.value = emptyCatalogResults();
                errorMessage.value = null;
                return;
            }

            const params = {
                q,
                per_page: props.perPage,
                projects_page: pageState.value.projects_page,
                studies_page: pageState.value.studies_page,
                datasets_page: pageState.value.datasets_page,
                ...overrides,
            };

            loading.value = true;
            errorMessage.value = null;

            try {
                const data = await fetchCatalogSearch(params);
                applyApiResponse(data);
                pageState.value = {
                    per_page: params.per_page,
                    projects_page: params.projects_page,
                    studies_page: params.studies_page,
                    datasets_page: params.datasets_page,
                };
                syncCatalogBrowserUrl(params);
            } catch (error) {
                const validationMessage = error?.response?.data?.errors?.q?.[0];
                errorMessage.value =
                    validationMessage ||
                    error?.response?.data?.message ||
                    "Something went wrong while searching. Please try again.";
                results.value = emptyCatalogResults();
                query.value = q;
            } finally {
                loading.value = false;
            }
        };

        watch(
            () => props.initialQuery,
            (newQuery, oldQuery) => {
                const q = (newQuery || "").trim();
                searchInput.value = q;

                if (!q) {
                    query.value = "";
                    results.value = emptyCatalogResults();
                    errorMessage.value = null;
                    return;
                }

                const fromUrl = readCatalogParamsFromUrl();
                const resetPages = newQuery !== oldQuery;

                fetchResults({
                    q,
                    per_page: fromUrl.per_page || props.perPage,
                    projects_page: resetPages ? 1 : fromUrl.projects_page,
                    studies_page: resetPages ? 1 : fromUrl.studies_page,
                    datasets_page: resetPages ? 1 : fromUrl.datasets_page,
                });
            },
            { immediate: true }
        );

        const clearSearch = () => {
            searchInput.value = "";
            router.visit("/");
        };

        const submitSearch = () => {
            const q = searchInput.value.trim();
            if (!q) {
                return;
            }

            router.get(
                "/search",
                { scope: SEARCH_SCOPE.CATALOG, q },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                }
            );
        };

        const changeProjectsPage = (page) => {
            fetchResults({ projects_page: page });
        };

        const changeStudiesPage = (page) => {
            fetchResults({ studies_page: page });
        };

        const changeDatasetsPage = (page) => {
            fetchResults({ datasets_page: page });
        };

        return {
            searchInput,
            query,
            loading,
            errorMessage,
            projects,
            studies,
            datasets,
            pageTitle,
            hasAnyResults,
            projectsBrowseUrl,
            emptyMessage,
            clearSearch,
            submitSearch,
            changeProjectsPage,
            changeStudiesPage,
            changeDatasetsPage,
        };
    },
};
</script>
