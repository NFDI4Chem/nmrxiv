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
import { groupsWithDistributionData } from "@/Utils/statsChart";

const props = defineProps({
    statistics: {
        type: Object,
        default: null,
    },
});

const distributions = computed(() => props.statistics?.distributions ?? {});
const missing = computed(() => props.statistics?.missing ?? {});

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
        },
        {
            key: "temperature_k",
            title: "Temperature (K)",
            labelFormat: "temperature",
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
