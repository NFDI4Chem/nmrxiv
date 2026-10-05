<template>
    <app-layout :title="pageTitle">
        <template #header>
            <div class="relative overflow-hidden border-b border-zinc-900/5">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-indigo-50/30 to-purple-50/30"
                ></div>
                <div class="relative mx-8 py-10 sm:py-12">
                    <h1
                        class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                    >
                        Spectra search results
                    </h1>
                    <p
                        v-if="summary"
                        class="mt-2 font-mono text-sm text-gray-700"
                    >
                        {{ summary }}
                    </p>
                    <p v-if="optionsSummary" class="mt-1 text-sm text-gray-600">
                        {{ optionsSummary }}
                    </p>
                    <p v-if="ignoredSummary" class="mt-1 text-xs text-gray-500">
                        {{ ignoredSummary }}
                    </p>
                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            :aria-expanded="refineOpen"
                            @click="refineOpen = !refineOpen"
                        >
                            <AdjustmentsHorizontalIcon
                                class="h-4 w-4"
                                aria-hidden="true"
                            />
                            {{ refineOpen ? "Hide peaks" : "Edit peaks" }}
                        </button>
                        <InertiaLink
                            href="/?tab=spectra"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900"
                        >
                            New search
                        </InertiaLink>
                    </div>
                </div>
            </div>
        </template>

        <div class="mb-24 min-h-[calc(100vh-400px)] w-full px-6 lg:px-8">
            <section
                v-if="refineOpen"
                class="mt-8 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm"
                aria-label="Edit peaks"
            >
                <div class="px-5 py-5 sm:px-6">
                    <PeakListSearchContent
                        :key="refineKey"
                        compact
                        :initial-params="searchParams"
                        @search-params-updated="refineState = $event"
                    />
                </div>
                <div
                    class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50/50 px-5 py-4 sm:px-6"
                >
                    <button
                        type="button"
                        class="rounded-full px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                        @click="refineOpen = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:bg-gray-300"
                        :disabled="!refineState?.canSearch"
                        @click="applyRefine"
                    >
                        <MagnifyingGlassIcon
                            class="h-4 w-4"
                            aria-hidden="true"
                        />
                        Search
                    </button>
                </div>
            </section>

            <div
                v-if="hasPeaks"
                class="mt-8 flex flex-wrap items-center justify-between gap-4"
            >
                <p class="text-sm text-gray-600" aria-live="polite">
                    <template v-if="loading">Searching…</template>
                    <template v-else-if="meta.total > 0">
                        {{ meta.total }}
                        {{ resultNoun }}
                    </template>
                </p>
                <div
                    class="inline-flex rounded-lg bg-gray-100 p-1"
                    role="radiogroup"
                    aria-label="Show results"
                >
                    <button
                        v-for="option in groupOptions"
                        :key="option.value"
                        type="button"
                        role="radio"
                        :aria-checked="group === option.value"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            group === option.value
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-600 hover:text-gray-900'
                        "
                        @click="changeGroup(option.value)"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>

            <div
                v-if="loading"
                class="mt-6 grid gap-6 lg:grid-cols-2"
                role="status"
                aria-label="Searching"
            >
                <div
                    v-for="index in 4"
                    :key="index"
                    class="animate-pulse rounded-2xl border border-gray-200 bg-white p-5"
                >
                    <div class="flex gap-4">
                        <div class="h-28 w-28 rounded-xl bg-gray-100" />
                        <div class="flex-1 space-y-3">
                            <div class="h-4 w-2/3 rounded bg-gray-100" />
                            <div class="h-3 w-1/3 rounded bg-gray-100" />
                            <div class="h-3 w-1/2 rounded bg-gray-100" />
                        </div>
                    </div>
                    <div class="mt-5 h-20 rounded-lg bg-gray-50" />
                </div>
            </div>

            <div v-else-if="!hasPeaks" class="pt-12">
                <EmptySearchState
                    entity-type="datasets"
                    search-query=""
                    title="No peaks to search for"
                    message="Add the peaks you are looking for, or drop a spectrum, and search again."
                    :show-clear-button="false"
                >
                    <template #actions>
                        <InertiaLink
                            href="/?tab=spectra"
                            class="inline-flex items-center rounded-full bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800"
                        >
                            Start a spectra search
                        </InertiaLink>
                    </template>
                </EmptySearchState>
            </div>

            <div v-else-if="errorMessage" class="pt-12">
                <EmptySearchState
                    entity-type="datasets"
                    :search-query="summary"
                    title="Search could not be completed"
                    :message="errorMessage"
                    :show-clear-button="false"
                />
            </div>

            <div v-else-if="cards.length === 0" class="pt-12">
                <EmptySearchState
                    entity-type="datasets"
                    :search-query="summary"
                    title="No matching spectra"
                    :message="emptyMessage"
                    :show-clear-button="false"
                >
                    <template #actions>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-full bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800"
                            @click="refineOpen = true"
                        >
                            Edit peaks
                        </button>
                    </template>
                </EmptySearchState>
            </div>

            <template v-else>
                <div class="mt-6 grid gap-6 lg:grid-cols-2">
                    <article
                        v-for="card in cards"
                        :key="card.key"
                        class="flex min-w-0 flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
                    >
                        <div class="flex gap-4">
                            <a
                                :href="card.url"
                                class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-white"
                                :aria-label="`Open ${card.title}`"
                            >
                                <Depictor2D
                                    v-if="card.smiles"
                                    :molecule="card.smiles"
                                    :width="220"
                                    :height="220"
                                    :show-download="false"
                                />
                                <BeakerIcon
                                    v-else
                                    class="h-8 w-8 text-gray-300"
                                    aria-hidden="true"
                                />
                            </a>
                            <div class="min-w-0 flex-1">
                                <div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <div class="min-w-0">
                                        <a
                                            :href="card.url"
                                            class="line-clamp-2 font-semibold text-gray-900 hover:underline"
                                        >
                                            {{ card.title }}
                                        </a>
                                        <div
                                            v-if="card.formula"
                                            class="mt-0.5 text-sm text-gray-600"
                                        >
                                            <MolecularFormula
                                                :formula="card.formula"
                                            />
                                        </div>
                                    </div>
                                    <span
                                        class="shrink-0 rounded-full px-2.5 py-1 text-sm font-semibold"
                                        :class="matchClass(card.similarity)"
                                        :title="`Match ${percent(
                                            card.similarity
                                        )}`"
                                    >
                                        {{ percent(card.similarity) }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-700">
                                    {{ card.matched_count }} of
                                    {{ card.query_count }}
                                    {{
                                        card.query_count === 1
                                            ? "peak"
                                            : "peaks"
                                    }}
                                    found
                                </p>
                                <p
                                    v-if="card.foundIn"
                                    class="mt-0.5 text-xs text-gray-500"
                                >
                                    {{ card.foundIn }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-5">
                            <div
                                v-for="spectrum in card.spectra"
                                :key="`${spectrum.dataset.id}-${spectrum.nucleus}`"
                            >
                                <div
                                    class="flex flex-wrap items-baseline justify-between gap-2 text-xs"
                                >
                                    <p class="min-w-0 truncate text-gray-700">
                                        <span
                                            class="mr-1 rounded bg-gray-100 px-1.5 py-0.5 font-semibold text-gray-700"
                                            >{{
                                                nucleusLabels[spectrum.nucleus]
                                            }}</span
                                        >
                                        {{ spectrum.matched_count }} of
                                        {{ spectrum.query_count }} found
                                        <template
                                            v-if="
                                                spectrum.mean_difference !==
                                                null
                                            "
                                        >
                                            · average difference
                                            {{
                                                formatDifference(
                                                    spectrum.mean_difference
                                                )
                                            }}
                                            ppm
                                        </template>
                                        · {{ spectrum.record_count }} peaks in
                                        this spectrum
                                    </p>
                                    <a
                                        v-if="spectrum.dataset.public_url"
                                        :href="spectrum.dataset.public_url"
                                        class="shrink-0 font-medium text-gray-900 underline-offset-2 hover:underline"
                                    >
                                        Open spectrum
                                    </a>
                                </div>
                                <PeakMatchPlot
                                    class="mt-2"
                                    :nucleus="spectrum.nucleus"
                                    :peaks="queryPeaks[spectrum.nucleus] || []"
                                    :matches="spectrum.matches"
                                    :extra="spectrum.extra"
                                    :offset="spectrum.offset"
                                />
                                <p
                                    v-if="hasNotableOffset(spectrum)"
                                    class="mt-1 text-xs text-gray-500"
                                >
                                    This spectrum sits
                                    {{
                                        formatDifference(
                                            Math.abs(spectrum.offset)
                                        )
                                    }}
                                    ppm
                                    {{
                                        spectrum.offset > 0 ? "higher" : "lower"
                                    }}
                                    than your peaks overall, probably a
                                    calibration difference.
                                </p>
                                <p
                                    v-if="missingPeaks(spectrum).length"
                                    class="mt-1 text-xs text-amber-700"
                                >
                                    Not found:
                                    {{ missingPeaks(spectrum).join(", ") }}
                                </p>
                            </div>
                        </div>
                    </article>
                </div>

                <TextSearchSectionPagination
                    :meta="meta"
                    navigation-label="Spectra search pagination"
                    @page-change="changePage"
                />
            </template>
        </div>
    </app-layout>
</template>

<script>
import { computed, ref, watch } from "vue";
import { Link as InertiaLink } from "@inertiajs/vue3";
import {
    AdjustmentsHorizontalIcon,
    BeakerIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue";
import Depictor2D from "@/Shared/Depictor2D.vue";
import MolecularFormula from "@/Shared/MolecularFormula.vue";
import PeakListSearchContent from "@/Shared/PeakListSearchContent.vue";
import PeakMatchPlot from "@/Shared/PeakMatchPlot.vue";
import TextSearchSectionPagination from "@/Shared/TextSearchSectionPagination.vue";
import EmptySearchState from "@/Shared/EmptySearchState.vue";
import {
    emptySpectraResults,
    fetchSpectrumSearch,
    syncSpectraBrowserUrl,
} from "@/Utils/unifiedSearchApi.js";
import {
    CLOSENESS,
    NUCLEUS_LABELS,
    formatShift,
    peaksSummary,
} from "@/Utils/spectrumQuery.js";

export default {
    components: {
        AppLayout,
        InertiaLink,
        AdjustmentsHorizontalIcon,
        BeakerIcon,
        MagnifyingGlassIcon,
        Depictor2D,
        MolecularFormula,
        PeakListSearchContent,
        PeakMatchPlot,
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
        const response = ref(emptySpectraResults());
        const searchParams = ref({});
        const refineOpen = ref(false);
        const refineKey = ref(0);
        const refineState = ref(null);

        const hasPeaks = computed(
            () => Object.keys(searchParams.value.peaks ?? {}).length > 0
        );
        const group = computed(() =>
            searchParams.value.group === "dataset" ? "dataset" : "compound"
        );
        const meta = computed(() => response.value.results.meta);
        const queryPeaks = computed(() => response.value.query.peaks ?? {});

        const summary = computed(() => peaksSummary(searchParams.value));

        const optionsSummary = computed(() => {
            const params = searchParams.value;
            const closeness = CLOSENESS[params.closeness] ?? CLOSENESS.normal;
            const parts = [
                params.mode === "whole"
                    ? "Looks like my whole spectrum"
                    : "Contains my peaks",
                `${closeness.label} (¹H ±${closeness["1H"]}, ¹³C ±${closeness["13C"]} ppm)`,
            ];
            if (params.solvent) {
                parts.push(
                    params.same_solvent
                        ? `only in ${params.solvent}`
                        : `measured in ${params.solvent}`
                );
            }

            return hasPeaks.value ? parts.join(" · ") : "";
        });

        const ignoredSummary = computed(() => {
            const ignored = Object.entries(
                response.value.query.ignored_peaks ?? {}
            ).flatMap(([nucleus, peaks]) =>
                peaks.map(
                    (peak) =>
                        `${NUCLEUS_LABELS[nucleus]} ${formatShift(peak.from)}`
                )
            );

            return ignored.length
                ? `Ignored as solvent or water: ${ignored.join(", ")}`
                : "";
        });

        const pageTitle = computed(() =>
            summary.value
                ? `Spectra search: ${summary.value}`
                : "Spectra search"
        );

        const resultNoun = computed(() => {
            const total = meta.value.total;
            if (group.value === "dataset") {
                return total === 1 ? "matching spectrum" : "matching spectra";
            }

            return total === 1 ? "matching compound" : "matching compounds";
        });

        const groupOptions = [
            { value: "compound", label: "By compound" },
            { value: "dataset", label: "All spectra" },
        ];

        const cards = computed(() =>
            response.value.results.data.map((item) => {
                const molecule = item.molecules?.[0];
                const isDataset = group.value === "dataset";
                const title =
                    molecule?.name ||
                    molecule?.iupac_name ||
                    item.sample?.name ||
                    (isDataset ? item.dataset?.name : null) ||
                    "Unnamed sample";

                return {
                    key: isDataset
                        ? `${item.dataset.id}-${item.nucleus}`
                        : item.key,
                    title,
                    url:
                        molecule?.public_url ||
                        item.sample?.public_url ||
                        item.dataset?.public_url ||
                        "#",
                    smiles: molecule?.canonical_smiles || null,
                    formula: molecule?.molecular_formula || null,
                    similarity: item.similarity,
                    matched_count: item.matched_count,
                    query_count: item.query_count,
                    foundIn: isDataset
                        ? item.sample?.name
                            ? `Sample: ${item.sample.name}`
                            : null
                        : foundInText(item),
                    spectra: isDataset ? [item] : item.spectra,
                };
            })
        );

        const foundInText = (item) => {
            const samples = item.found_in_samples ?? 1;
            const datasets = item.found_in_datasets ?? 1;
            const sampleText = `${samples} ${
                samples === 1 ? "sample" : "samples"
            }`;
            const datasetText = `${datasets} ${
                datasets === 1 ? "spectrum" : "spectra"
            }`;

            return `Matches in ${sampleText} · ${datasetText}`;
        };

        const emptyMessage = computed(() => {
            const hasExclusions = Object.values(
                searchParams.value.peaks ?? {}
            ).some((encoded) => /(^|;)!/.test(encoded));
            const base =
                searchParams.value.mode === "whole"
                    ? "No public spectrum looks like yours. Try “Contains my peaks”, a relaxed closeness, or mark fewer peaks as “Must have”."
                    : "No public spectrum has all of your “Must have” peaks. Try fewer “Must have” peaks or a relaxed closeness.";

            return hasExclusions
                ? `${base} Your “Must not have” regions may also rule out close matches.`
                : base;
        });

        const percent = (value) => `${Math.round((value ?? 0) * 100)}%`;

        const matchClass = (value) =>
            value >= 0.8
                ? "bg-emerald-50 text-emerald-700"
                : value >= 0.5
                ? "bg-amber-50 text-amber-700"
                : "bg-gray-100 text-gray-600";

        const formatDifference = (value) =>
            value < 0.001
                ? "< 0.001"
                : Number(value).toFixed(value < 0.1 ? 3 : 2);

        const hasNotableOffset = (spectrum) =>
            Math.abs(spectrum.offset ?? 0) >=
            (spectrum.nucleus === "13C" ? 0.1 : 0.01);

        const missingPeaks = (spectrum) =>
            (spectrum.missing ?? [])
                .map((index) => queryPeaks.value[spectrum.nucleus]?.[index])
                .filter(Boolean)
                .map((peak) =>
                    peak.to > peak.from
                        ? `${formatShift(peak.from)}–${formatShift(peak.to)}`
                        : formatShift(peak.from)
                );

        const runSearch = async (params) => {
            searchParams.value = params;

            if (!hasPeaks.value) {
                response.value = emptySpectraResults();
                errorMessage.value = null;
                return;
            }

            loading.value = true;
            errorMessage.value = null;
            syncSpectraBrowserUrl(params);

            try {
                response.value = await fetchSpectrumSearch({
                    ...params,
                    per_page: props.perPage,
                });
            } catch (error) {
                const errors = error?.response?.data?.errors ?? {};
                errorMessage.value =
                    Object.values(errors)?.[0]?.[0] ||
                    error?.response?.data?.message ||
                    "Something went wrong while searching. Please try again.";
                response.value = emptySpectraResults();
            } finally {
                loading.value = false;
            }
        };

        const changePage = (page) => {
            runSearch({ ...searchParams.value, page });
            window.scrollTo({ top: 0, behavior: "smooth" });
        };

        const changeGroup = (value) => {
            if (value === group.value) {
                return;
            }

            const params = { ...searchParams.value, page: 1 };
            if (value === "dataset") {
                params.group = "dataset";
            } else {
                delete params.group;
            }
            runSearch(params);
        };

        const applyRefine = () => {
            if (!refineState.value?.canSearch) {
                return;
            }

            const params = { ...refineState.value.params, page: 1 };
            if (searchParams.value.group) {
                params.group = searchParams.value.group;
            }
            refineOpen.value = false;
            runSearch(params);
        };

        watch(refineOpen, (open) => {
            if (open) {
                refineKey.value++;
            }
        });

        watch(
            () => props.initialParams,
            (params) => {
                const page = parseInt(params.page ?? "1", 10);
                runSearch({
                    ...params,
                    peaks: { ...(params.peaks ?? {}) },
                    page: Number.isFinite(page) && page > 0 ? page : 1,
                });
            },
            { immediate: true, deep: true }
        );

        return {
            loading,
            errorMessage,
            searchParams,
            refineOpen,
            refineKey,
            refineState,
            hasPeaks,
            group,
            meta,
            queryPeaks,
            summary,
            optionsSummary,
            ignoredSummary,
            pageTitle,
            resultNoun,
            groupOptions,
            cards,
            emptyMessage,
            nucleusLabels: NUCLEUS_LABELS,
            percent,
            matchClass,
            formatDifference,
            hasNotableOffset,
            missingPeaks,
            changePage,
            changeGroup,
            applyRefine,
        };
    },
};
</script>
