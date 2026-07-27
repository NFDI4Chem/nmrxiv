<script setup>
import { computed } from "vue";
import { Head } from "@inertiajs/vue3";
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

const piePanelsBeforeSunbursts = computed(() =>
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
    ])
);

const piePanelsAfterSunbursts = computed(() =>
    buildPiePanels(distributions.value, missing.value, [
        {
            key: "manufacturer",
            title: "Manufacturer",
            labelFormat: "manufacturer",
        },
        {
            key: "temperature_k",
            title: "Temperature (K)",
            labelFormat: "temperature",
        },
        {
            key: "experiment_category",
            title: "Experiment category",
        },
        {
            key: "pulse_sequence",
            title: "Pulse sequence",
        },
        {
            key: "tube_diameter_mm",
            title: "Tube diameter (mm)",
        },
        {
            key: "number_of_scans",
            title: "Number of scans",
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
</script>

<template>
    <div class="bg-white">
        <Head title="Statistics - nmrXiv" />
        <FlashMessages />

        <main>
            <PublicSiteHeader />

            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
                <div class="mb-10 border-b border-gray-200 pb-10">
                    <dl
                        class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div
                            v-for="stat in summaryStats"
                            :key="stat.key"
                            class="flex flex-col-reverse gap-y-2"
                        >
                            <dt class="text-base leading-7 text-gray-600">
                                {{ stat.label }}
                            </dt>
                            <dd
                                class="text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl"
                            >
                                {{ formatStatsNumber(stat.value) }}
                            </dd>
                        </div>
                    </dl>
                    <p class="mt-6 max-w-3xl text-sm leading-6 text-gray-600">
                        nmrXiv archives only experimental, instrument-acquired
                        NMR data. It does not contain predicted (simulated)
                        spectra.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <StatsPieChart
                        v-for="panel in piePanelsBeforeSunbursts"
                        :key="panel.key"
                        :title="panel.title"
                        :filter-key="panel.key"
                        :rows="panel.rows"
                        :missing="panel.missing"
                        :label-format="panel.labelFormat ?? 'default'"
                    />

                    <StatsDimensionExperimentSunburst
                        v-if="showExperimentSunburst"
                        :groups="
                            distributions.dimension_experiment_breakdown ?? []
                        "
                        :missing="missing.dimension_experiment_breakdown ?? 0"
                    />

                    <StatsNucleusFrequencySunburst
                        v-if="showFrequencySunburst"
                        :groups="
                            distributions.nucleus_measuring_frequency_mhz ?? []
                        "
                        :missing="missing.nucleus_measuring_frequency_mhz ?? 0"
                    />

                    <StatsPieChart
                        v-for="panel in piePanelsAfterSunbursts"
                        :key="panel.key"
                        :title="panel.title"
                        :filter-key="panel.key"
                        :rows="panel.rows"
                        :missing="panel.missing"
                        :label-format="panel.labelFormat ?? 'default'"
                    />
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
