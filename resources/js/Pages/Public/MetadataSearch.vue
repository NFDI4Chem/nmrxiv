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
                        Metadata search results
                    </h1>
                    <p v-if="criteriaSummary" class="mt-2 text-gray-600">
                        {{ criteriaSummary }}
                    </p>
                    <div class="mt-6">
                        <InertiaLink
                            :href="refineSearchUrl"
                            class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Refine search
                        </InertiaLink>
                    </div>
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
                    entity-type="datasets"
                    :search-query="criteriaSummary"
                    title="Search could not be completed"
                    :message="errorMessage"
                    :show-clear-button="false"
                />
            </div>

            <div v-else-if="hasCriteria && !hasAnyResults" class="w-full pt-12">
                <EmptySearchState
                    entity-type="datasets"
                    :search-query="criteriaSummary"
                    title="No results found"
                    :message="emptyMessage"
                    :show-clear-button="true"
                    @clear-search="clearSearch"
                />
            </div>

            <template v-else-if="hasCriteria">
                <section
                    v-if="studies.meta.total > 0"
                    id="samples"
                    class="border-t border-gray-100 pt-10"
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
import AppLayout from "@/Layouts/AppLayout.vue";
import StudyCardPublic from "@/Shared/StudyCardPublic.vue";
import DatasetCard from "@/Shared/DatasetCard.vue";
import TextSearchSectionPagination from "@/Shared/TextSearchSectionPagination.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import {
    emptyMetadataResults,
    fetchMetadataSearch,
    readMetadataParamsFromUrl,
    syncMetadataBrowserUrl,
    buildAdvancedFormPath,
} from "@/Utils/unifiedSearchApi.js";

const LABELS = {
    q: "Keywords",
    solvent: "Solvent",
    temperature: "Temperature",
    tube_diameter: "Tube diameter",
    nucleus: "Nucleus",
    proton_frequency: "Proton frequency",
    nmr_method: "NMR method",
    pulse_sequence: "Pulse sequence",
    number_of_scans: "Number of scans",
    manufacturer: "Manufacturer",
    instrument_model: "Instrument model",
};

export default {
    components: {
        AppLayout,
        InertiaLink,
        StudyCardPublic,
        DatasetCard,
        TextSearchSectionPagination,
        EmptySearchState,
    },
    props: {
        initialParams: {
            type: Object,
            default: () => ({}),
        },
        perPage: {
            type: Number,
            default: 12,
        },
    },
    setup(props) {
        const loading = ref(false);
        const errorMessage = ref(null);
        const results = ref(emptyMetadataResults());
        const searchParams = ref({ ...props.initialParams });
        const pageState = ref({
            per_page: props.perPage,
            studies_page: 1,
            datasets_page: 1,
        });

        const studies = computed(() => results.value.studies);
        const datasets = computed(() => results.value.datasets);

        const hasCriteria = computed(() =>
            Object.values(searchParams.value).some(
                (value) => value !== null && value !== undefined && value !== ""
            )
        );

        const criteriaSummary = computed(() => {
            const parts = Object.entries(searchParams.value)
                .filter(
                    ([, value]) =>
                        value !== null && value !== undefined && value !== ""
                )
                .map(([key, value]) => `${LABELS[key] ?? key}: ${value}`);

            return parts.length > 0 ? parts.join(" · ") : "";
        });

        const pageTitle = computed(() =>
            criteriaSummary.value
                ? `Metadata search: ${criteriaSummary.value}`
                : "Metadata search"
        );

        const hasAnyResults = computed(
            () => studies.value.meta.total > 0 || datasets.value.meta.total > 0
        );

        const emptyMessage = computed(
            () =>
                `We could not find public samples or spectra matching your metadata criteria. Try broader filters or fewer fields.`
        );

        const refineSearchUrl = computed(() =>
            buildAdvancedFormPath(searchParams.value)
        );

        const buildRequestParams = (overrides = {}) => ({
            ...searchParams.value,
            per_page: pageState.value.per_page,
            studies_page: pageState.value.studies_page,
            datasets_page: pageState.value.datasets_page,
            ...overrides,
        });

        const fetchResults = async (overrides = {}) => {
            if (!hasCriteria.value) {
                results.value = emptyMetadataResults();
                errorMessage.value = null;
                return;
            }

            const params = buildRequestParams(overrides);

            loading.value = true;
            errorMessage.value = null;

            try {
                const data = await fetchMetadataSearch(params);
                results.value = {
                    query: data.query || {},
                    studies: data.studies || emptyMetadataResults().studies,
                    datasets: data.datasets || emptyMetadataResults().datasets,
                };
                pageState.value = {
                    per_page: params.per_page,
                    studies_page: params.studies_page,
                    datasets_page: params.datasets_page,
                };
                syncMetadataBrowserUrl(params);
            } catch (error) {
                const validationMessage =
                    error?.response?.data?.errors?.q?.[0] ||
                    Object.values(
                        error?.response?.data?.errors || {}
                    )?.[0]?.[0];
                errorMessage.value =
                    validationMessage ||
                    error?.response?.data?.message ||
                    "Something went wrong while searching. Please try again.";
                results.value = emptyMetadataResults();
            } finally {
                loading.value = false;
            }
        };

        watch(
            () => props.initialParams,
            (newParams) => {
                searchParams.value = { ...newParams };
                const fromUrl = readMetadataParamsFromUrl();

                fetchResults({
                    ...searchParams.value,
                    per_page: fromUrl.per_page || props.perPage,
                    studies_page: fromUrl.studies_page,
                    datasets_page: fromUrl.datasets_page,
                });
            },
            { immediate: true, deep: true }
        );

        const clearSearch = () => {
            router.visit("/?tab=advanced");
        };

        const changeStudiesPage = (page) => {
            fetchResults({ studies_page: page });
        };

        const changeDatasetsPage = (page) => {
            fetchResults({ datasets_page: page });
        };

        return {
            loading,
            errorMessage,
            studies,
            datasets,
            hasCriteria,
            criteriaSummary,
            pageTitle,
            hasAnyResults,
            emptyMessage,
            refineSearchUrl,
            clearSearch,
            changeStudiesPage,
            changeDatasetsPage,
        };
    },
};
</script>
