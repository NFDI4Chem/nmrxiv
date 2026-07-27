<script setup>
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
import {
    ArrowTopRightOnSquareIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";
import PublicSiteHeader from "@/Shared/PublicSiteHeader.vue";
import Footer from "@/Shared/Footer.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
import StatsPieChart from "@/Shared/Stats/StatsPieChart.vue";
import StatsNucleusFrequencySunburst from "@/Shared/Stats/StatsNucleusFrequencySunburst.vue";
import StatsDimensionExperimentSunburst from "@/Shared/Stats/StatsDimensionExperimentSunburst.vue";
import { buildPiePanels } from "@/Utils/statsPanels";
import {
    formatStatsNumber,
    groupsWithDistributionData,
} from "@/Utils/statsChart";

const props = defineProps({
    statistics: {
        type: Object,
        default: null,
    },
    compoundsWithSpectra: {
        type: Number,
        default: 0,
    },
});

const distributions = computed(() => props.statistics?.distributions ?? {});
const missing = computed(() => props.statistics?.missing ?? {});
const totals = computed(() => props.statistics?.totals ?? {});

const summaryStats = computed(() => [
    {
        key: "compounds",
        label: "Compounds with spectra",
        value: props.compoundsWithSpectra ?? 0,
    },
    {
        key: "samples",
        label: "Samples with spectra",
        value: totals.value.samples_with_indexed_spectra ?? 0,
    },
    {
        key: "experimental",
        label: "Experimental spectra",
        value: totals.value.public_spectra ?? 0,
    },
    {
        key: "predicted",
        label: "Predicted spectra",
        value: 0,
    },
]);

const samplePanels = computed(() =>
    buildPiePanels(distributions.value, missing.value, [
        {
            key: "nucleus",
            title: "Nucleus",
            labelFormat: "nucleus",
        },
        {
            key: "solvent",
            title: "Solvent",
            labelFormat: "solvent",
        },
        {
            key: "temperature_k",
            title: "Temperature (K)",
            labelFormat: "temperature",
        },
        {
            key: "tube_diameter_mm",
            title: "Tube diameter (mm)",
        },
    ])
);

const experimentPanels = computed(() =>
    buildPiePanels(distributions.value, missing.value, [
        {
            key: "experiment_category",
            title: "Experiment category",
        },
        {
            key: "pulse_sequence",
            title: "Pulse sequence",
        },
        {
            key: "number_of_scans",
            title: "Number of scans",
        },
    ])
);

const instrumentPanels = computed(() =>
    buildPiePanels(distributions.value, missing.value, [
        {
            key: "manufacturer",
            title: "Manufacturer",
            labelFormat: "manufacturer",
        },
        {
            key: "probe_type",
            title: "Probe type",
        },
        {
            key: "instrument_model",
            title: "Probe / instrument model",
        },
    ])
);

const showExperimentSunburst = computed(() =>
    groupsWithDistributionData(
        distributions.value.dimension_experiment_breakdown ?? []
    )
);

const showFrequencySunburst = computed(() =>
    groupsWithDistributionData(
        distributions.value.nucleus_measuring_frequency_mhz ?? []
    )
);

const sections = computed(() =>
    [
        {
            id: "samples",
            index: "01",
            title: "Samples & conditions",
            description:
                "What was measured — the nuclei observed, the solvents samples were dissolved in, and the conditions they were acquired under.",
            visible: samplePanels.value.length > 0,
        },
        {
            id: "experiments",
            index: "02",
            title: "Experiments",
            description:
                "How spectra were acquired — experiment dimensions and types, pulse sequences, and scan counts.",
            visible:
                showExperimentSunburst.value ||
                experimentPanels.value.length > 0,
        },
        {
            id: "instrumentation",
            index: "03",
            title: "Instrumentation",
            description:
                "The spectrometers behind the data — measuring frequencies, manufacturers, and probe hardware.",
            visible:
                showFrequencySunburst.value ||
                instrumentPanels.value.length > 0,
        },
    ].filter((section) => section.visible)
);

const hasAnyDistribution = computed(() => sections.value.length > 0);

const navItems = computed(() => [
    ...sections.value.map((section) => ({
        id: section.id,
        title: section.title,
    })),
    { id: "api", title: "API access" },
]);
</script>

<template>
    <div class="bg-white">
        <Head title="Statistics - nmrXiv" />
        <FlashMessages />

        <main>
            <!-- Page header -->
            <div class="relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-50/40 via-white to-purple-50/40"
                    aria-hidden="true"
                ></div>
                <div
                    class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-b from-transparent to-white"
                    aria-hidden="true"
                ></div>

                <PublicSiteHeader variant="hero" />

                <div
                    class="relative mx-auto max-w-7xl px-4 pb-14 pt-12 sm:px-6 sm:pt-16 lg:px-8"
                >
                    <p
                        class="text-xs font-medium uppercase tracking-wide text-gray-400"
                    >
                        Repository insights
                    </p>
                    <h1
                        class="mt-2 text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl"
                    >
                        Statistics
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-gray-600">
                        A live overview of the open NMR data archived on nmrXiv
                        — what was measured, how, and on which instruments.
                        Every chart segment links to the matching search
                        results.
                    </p>

                    <dl
                        class="mt-10 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div
                            v-for="stat in summaryStats"
                            :key="stat.key"
                            class="rounded-2xl border border-gray-100 bg-white/80 p-5 backdrop-blur"
                        >
                            <dd
                                class="text-3xl font-semibold tabular-nums tracking-tight text-gray-900 sm:text-4xl"
                            >
                                {{ formatStatsNumber(stat.value) }}
                            </dd>
                            <dt class="mt-2 text-sm leading-6 text-gray-500">
                                {{ stat.label }}
                            </dt>
                        </div>
                    </dl>

                    <p
                        class="mt-5 flex max-w-3xl items-start gap-1.5 text-xs leading-5 text-gray-500"
                    >
                        <InformationCircleIcon
                            class="mt-0.5 size-4 shrink-0 text-gray-400"
                            aria-hidden="true"
                        />
                        <span>
                            nmrXiv archives only experimental,
                            instrument-acquired NMR data. It does not contain
                            predicted (simulated) spectra.
                        </span>
                    </p>
                </div>
            </div>

            <!-- Section navigation -->
            <nav
                v-if="sections.length > 1"
                aria-label="Statistics sections"
                class="sticky top-0 z-20 border-y border-gray-100 bg-white/90 backdrop-blur"
            >
                <div
                    class="mx-auto flex max-w-7xl gap-1 overflow-x-auto px-4 py-2.5 sm:px-6 lg:px-8"
                >
                    <a
                        v-for="item in navItems"
                        :key="`nav-${item.id}`"
                        :href="`#${item.id}`"
                        class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900"
                    >
                        {{ item.title }}
                    </a>
                </div>
            </nav>

            <!-- Chart sections -->
            <div
                v-if="hasAnyDistribution"
                class="mx-auto max-w-7xl space-y-16 px-4 py-12 sm:px-6 lg:space-y-20 lg:px-8 lg:py-16"
            >
                <template v-for="section in sections" :key="section.id">
                    <section :id="section.id" class="scroll-mt-20">
                        <div class="flex items-baseline gap-3">
                            <span
                                class="text-sm font-semibold tabular-nums text-gray-300"
                                aria-hidden="true"
                            >
                                {{ section.index }}
                            </span>
                            <h2
                                class="text-xl font-semibold tracking-tight text-gray-900 sm:text-2xl"
                            >
                                {{ section.title }}
                            </h2>
                        </div>
                        <p
                            class="mt-2 max-w-2xl pl-8 text-sm leading-6 text-gray-500"
                        >
                            {{ section.description }}
                        </p>

                        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <template v-if="section.id === 'samples'">
                                <StatsPieChart
                                    v-for="panel in samplePanels"
                                    :key="panel.key"
                                    :title="panel.title"
                                    :filter-key="panel.key"
                                    :rows="panel.rows"
                                    :missing="panel.missing"
                                    :label-format="
                                        panel.labelFormat ?? 'default'
                                    "
                                />
                            </template>

                            <template v-if="section.id === 'experiments'">
                                <StatsDimensionExperimentSunburst
                                    v-if="showExperimentSunburst"
                                    :groups="
                                        distributions.dimension_experiment_breakdown ??
                                        []
                                    "
                                    :missing="
                                        missing.dimension_experiment_breakdown ??
                                        0
                                    "
                                />
                                <StatsPieChart
                                    v-for="panel in experimentPanels"
                                    :key="panel.key"
                                    :title="panel.title"
                                    :filter-key="panel.key"
                                    :rows="panel.rows"
                                    :missing="panel.missing"
                                    :label-format="
                                        panel.labelFormat ?? 'default'
                                    "
                                />
                            </template>

                            <template v-if="section.id === 'instrumentation'">
                                <StatsNucleusFrequencySunburst
                                    v-if="showFrequencySunburst"
                                    :groups="
                                        distributions.nucleus_measuring_frequency_mhz ??
                                        []
                                    "
                                    :missing="
                                        missing.nucleus_measuring_frequency_mhz ??
                                        0
                                    "
                                />
                                <StatsPieChart
                                    v-for="panel in instrumentPanels"
                                    :key="panel.key"
                                    :title="panel.title"
                                    :filter-key="panel.key"
                                    :rows="panel.rows"
                                    :missing="panel.missing"
                                    :label-format="
                                        panel.labelFormat ?? 'default'
                                    "
                                />
                            </template>
                        </div>
                    </section>
                </template>
            </div>

            <!-- Empty state -->
            <div v-else class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div
                    class="rounded-2xl border border-dashed border-gray-200 bg-gray-50/50 px-6 py-16 text-center"
                >
                    <h2 class="text-base font-semibold text-gray-900">
                        No statistics available yet
                    </h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                        Distribution statistics are computed from the public
                        spectra metadata index and will appear here once data
                        has been indexed.
                    </p>
                </div>
            </div>

            <!-- API access -->
            <section
                id="api"
                aria-labelledby="stats-api-heading"
                class="mx-auto max-w-7xl scroll-mt-20 px-4 pb-16 sm:px-6 lg:px-8 lg:pb-20"
            >
                <div
                    class="flex flex-col gap-8 rounded-2xl border border-gray-100 bg-gray-50/60 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div class="max-w-2xl">
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            For developers
                        </p>
                        <h2
                            id="stats-api-heading"
                            class="mt-2 text-xl font-semibold tracking-tight text-gray-900"
                        >
                            Statistics via the REST API
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            All statistics on this page are also available via
                            the REST API. Filter by nucleus, solvent, and other
                            metadata to get statistics for just the spectra you
                            care about.
                        </p>
                    </div>

                    <div class="shrink-0 lg:w-96">
                        <div
                            class="flex items-center gap-3 overflow-x-auto rounded-xl border border-gray-200 bg-white px-4 py-3"
                        >
                            <span
                                class="rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700"
                            >
                                GET
                            </span>
                            <code
                                class="whitespace-nowrap font-mono text-sm text-gray-800"
                            >
                                /api/v1/search/metadata/stats
                            </code>
                        </div>
                        <a
                            href="/api/documentation#tag/search/GET/api/v1/search/metadata/stats"
                            class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-gray-700 underline-offset-2 transition-colors hover:text-gray-900 hover:underline"
                        >
                            View the API reference
                            <ArrowTopRightOnSquareIcon
                                class="size-4"
                                aria-hidden="true"
                            />
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <Footer />
    </div>
</template>
