<template>
    <div class="flex flex-col text-left">
        <header
            v-if="!compact"
            class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-gray-100 pb-5"
        >
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-900">
                    Advanced metadata search
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Filter public samples and spectra by indexed NMRium fields.
                </p>
            </div>
            <div v-if="hasAnyValue" class="flex items-center gap-3">
                <span class="text-xs text-gray-500">
                    {{ activeFilterCount }}
                    {{ activeFilterCount === 1 ? "filter" : "filters" }}
                </span>
                <button
                    type="button"
                    class="text-xs font-medium text-gray-600 underline-offset-2 hover:text-gray-900 hover:underline"
                    @click="clearAll"
                >
                    Clear all
                </button>
            </div>
        </header>

        <div v-else class="mb-4 flex items-center justify-between gap-3">
            <p class="text-sm text-gray-600">
                Refine by solvent, nucleus, experiment, and instrument metadata.
            </p>
            <button
                v-if="hasAnyValue"
                type="button"
                class="shrink-0 text-xs font-medium text-gray-600 underline-offset-2 hover:text-gray-900 hover:underline"
                @click="clearAll"
            >
                Clear
            </button>
        </div>

        <div class="relative">
            <MagnifyingGlassIcon
                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                aria-hidden="true"
            />
            <label for="metadata-free-text" class="sr-only"> Keywords </label>
            <input
                id="metadata-free-text"
                v-model="freeText"
                type="search"
                placeholder="Keywords across all metadata fields"
                class="w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
            />
        </div>

        <div
            class="mt-5 grid grid-cols-1 gap-6"
            :class="
                compact
                    ? 'mx-auto w-full max-w-3xl md:grid-cols-2'
                    : 'lg:grid-cols-2 xl:grid-cols-4'
            "
        >
            <section :class="sectionClass">
                <h3 :class="sectionTitleClass">Sample</h3>
                <div class="space-y-3">
                    <MetadataFacetField
                        v-model="solvent"
                        label="Solvent"
                        compact
                        :options="solventOptions"
                        :loading="!facetsLoaded"
                    />
                    <MetadataFacetField
                        v-model="temperature"
                        label="Temperature"
                        compact
                        :options="temperatureOptions"
                        :loading="!facetsLoaded"
                    />
                    <MetadataFacetField
                        v-model="tubeDiameter"
                        label="Tube diameter"
                        compact
                        :options="tubeDiameterOptions"
                        :disabled-values="disabledTubeDiameters"
                        :loading="!facetsLoaded"
                        :pill-threshold="5"
                    />
                </div>
            </section>

            <section :class="sectionClass">
                <h3 :class="sectionTitleClass">Nucleus &amp; frequency</h3>
                <div class="space-y-3">
                    <MetadataFacetField
                        v-model="acquisitionNucleus"
                        label="Nucleus"
                        compact
                        :options="nucleusOptions"
                        :disabled-values="disabledNuclei"
                        :loading="!facetsLoaded"
                        :pill-threshold="7"
                    />
                    <MetadataFacetField
                        v-model="protonFrequency"
                        label="Observed frequency"
                        compact
                        :options="protonFrequencyOptions"
                        :disabled-values="disabledProtonFrequencies"
                        :loading="!facetsLoaded"
                        :pill-threshold="5"
                    />
                </div>
            </section>

            <section :class="sectionClass">
                <h3 :class="sectionTitleClass">Experiment</h3>
                <div class="space-y-3">
                    <MetadataFacetField
                        v-model="nmrMethod"
                        label="Method"
                        compact
                        :options="nmrMethodOptions"
                        :loading="!facetsLoaded"
                    />
                    <MetadataFacetField
                        v-model="pulseSequence"
                        label="Pulse sequence"
                        compact
                        :options="pulseSequenceOptions"
                        :loading="!facetsLoaded"
                    />
                    <MetadataFacetField
                        v-model="numberOfScans"
                        label="Scans"
                        compact
                        :options="numberOfScansOptions"
                        :loading="!facetsLoaded"
                        :pill-threshold="5"
                    />
                </div>
            </section>

            <section :class="sectionClass">
                <h3 :class="sectionTitleClass">Instrument</h3>
                <div class="space-y-3">
                    <MetadataFacetField
                        v-model="manufacturer"
                        label="Manufacturer"
                        compact
                        :options="manufacturerOptions"
                        :loading="!facetsLoaded"
                    />
                    <MetadataFacetField
                        v-model="instrumentModel"
                        label="Probe"
                        compact
                        :options="instrumentModelOptions"
                        :loading="!facetsLoaded"
                    />
                </div>
            </section>
        </div>
    </div>
</template>

<script>
import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
import MetadataFacetField from "@/Shared/MetadataFacetField.vue";
import {
    metadataParamsFromForm,
    metadataParamsToForm,
    readAdvancedFormParamsFromUrl,
    syncAdvancedFormBrowserUrl,
    fetchMetadataFacets,
} from "@/Utils/unifiedSearchApi.js";
import { formatSolventLabel } from "@/Utils/nmrLabels.js";

const tubeDiameterStaticOptions = [
    { label: "Any", value: "" },
    { label: "3 mm", value: "3" },
    { label: "5 mm", value: "5" },
    { label: "10 mm", value: "10" },
];

const nucleusStaticOptions = [
    { label: "Any", value: "" },
    { label: "¹H", value: "1H" },
    { label: "¹³C", value: "13C" },
    { label: "¹⁵N", value: "15N" },
    { label: "¹⁹F", value: "19F" },
    { label: "³¹P", value: "31P" },
];

function buildFacetOptions(values, labelFn = (value) => value) {
    return [
        { label: "Any", value: "" },
        ...values.map((value) => ({
            label: labelFn(value),
            value: String(value),
        })),
    ];
}

function buildStaticFacetOptions(staticOptions, availableValues, facetsLoaded) {
    if (!facetsLoaded) {
        return staticOptions;
    }

    const available = new Set(availableValues.map(String));

    return staticOptions.filter(
        (option) => option.value === "" || available.has(String(option.value))
    );
}

export default {
    components: {
        MetadataFacetField,
        MagnifyingGlassIcon,
    },
    props: {
        compact: {
            type: Boolean,
            default: false,
        },
        persistInUrl: {
            type: Boolean,
            default: false,
        },
        initialParams: {
            type: Object,
            default: () => ({}),
        },
    },
    emits: ["search-params-updated"],
    setup(props, { emit }) {
        const freeText = ref("");
        const solvent = ref("");
        const temperature = ref("");
        const tubeDiameter = ref("");
        const acquisitionNucleus = ref("");
        const protonFrequency = ref("");
        const nmrMethod = ref("");
        const pulseSequence = ref("");
        const numberOfScans = ref("");
        const manufacturer = ref("");
        const instrumentModel = ref("");
        const hydrated = ref(false);
        const facetsLoaded = ref(false);
        const facetAvailability = ref({
            solvent: [],
            temperature: [],
            tube_diameter: [],
            nucleus: [],
            proton_frequency: [],
            nmr_method: [],
            pulse_sequence: [],
            number_of_scans: [],
            manufacturer: [],
            instrument_model: [],
        });
        let facetRequestId = 0;
        let facetDebounceTimeout = null;

        const sectionClass = computed(() =>
            props.compact
                ? "min-w-0"
                : "min-w-0 rounded-xl border border-gray-200 bg-gray-50/40 p-4"
        );

        const sectionTitleClass = computed(() =>
            props.compact
                ? "mb-3 text-[11px] font-semibold uppercase tracking-wide text-gray-500"
                : "mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500"
        );

        const applyInitialParams = (params = {}) => {
            const form = metadataParamsToForm(params);

            freeText.value = form.freeText ?? "";
            solvent.value = form.solvent ?? "";
            temperature.value = form.temperature ?? "";
            tubeDiameter.value = form.tubeDiameter ?? "";
            acquisitionNucleus.value = form.acquisitionNucleus ?? "";
            protonFrequency.value = form.protonFrequency ?? "";
            nmrMethod.value = form.nmrMethod ?? "";
            pulseSequence.value = form.pulseSequence ?? "";
            numberOfScans.value = form.numberOfScans ?? "";
            manufacturer.value = form.manufacturer ?? "";
            instrumentModel.value = form.instrumentModel ?? "";
        };

        const buildPayload = () => ({
            freeText: freeText.value,
            solvent: solvent.value,
            temperature: temperature.value,
            tubeDiameter: tubeDiameter.value,
            acquisitionNucleus: acquisitionNucleus.value,
            protonFrequency: protonFrequency.value,
            nmrMethod: nmrMethod.value,
            pulseSequence: pulseSequence.value,
            numberOfScans: numberOfScans.value,
            manufacturer: manufacturer.value,
            instrumentModel: instrumentModel.value,
        });

        const emitParams = () => {
            const payload = buildPayload();

            emit("search-params-updated", payload);

            if (props.persistInUrl) {
                syncAdvancedFormBrowserUrl(payload);
            }
        };

        const applyFacetConstraints = () => {
            if (!facetsLoaded.value) {
                return;
            }

            const facets = facetAvailability.value;
            const constraints = [
                ["solvent", solvent],
                ["temperature", temperature],
                ["tube_diameter", tubeDiameter],
                ["nucleus", acquisitionNucleus],
                ["proton_frequency", protonFrequency],
                ["nmr_method", nmrMethod],
                ["pulse_sequence", pulseSequence],
                ["number_of_scans", numberOfScans],
                ["manufacturer", manufacturer],
                ["instrument_model", instrumentModel],
            ];

            for (const [field, fieldRef] of constraints) {
                if (
                    fieldRef.value &&
                    !facets[field].includes(String(fieldRef.value))
                ) {
                    fieldRef.value = "";
                }
            }
        };

        const refreshFacets = async () => {
            const requestId = ++facetRequestId;

            try {
                const facets = await fetchMetadataFacets(
                    metadataParamsFromForm(buildPayload())
                );

                if (requestId !== facetRequestId) {
                    return;
                }

                facetAvailability.value = {
                    solvent: facets.solvent ?? [],
                    temperature: facets.temperature ?? [],
                    tube_diameter: facets.tube_diameter ?? [],
                    nucleus: facets.nucleus ?? [],
                    proton_frequency: facets.proton_frequency ?? [],
                    nmr_method: facets.nmr_method ?? [],
                    pulse_sequence: facets.pulse_sequence ?? [],
                    number_of_scans: facets.number_of_scans ?? [],
                    manufacturer: facets.manufacturer ?? [],
                    instrument_model: facets.instrument_model ?? [],
                };
                facetsLoaded.value = true;
                applyFacetConstraints();
            } catch {
                if (requestId !== facetRequestId) {
                    return;
                }

                facetsLoaded.value = false;
            }
        };

        const scheduleFacetRefresh = () => {
            if (!hydrated.value) {
                return;
            }

            if (facetDebounceTimeout) {
                clearTimeout(facetDebounceTimeout);
            }

            facetDebounceTimeout = setTimeout(() => {
                refreshFacets();
            }, 300);
        };

        onMounted(() => {
            if (props.persistInUrl) {
                applyInitialParams(readAdvancedFormParamsFromUrl());
            } else if (Object.keys(props.initialParams).length > 0) {
                applyInitialParams(props.initialParams);
            }

            hydrated.value = true;
            emitParams();
            scheduleFacetRefresh();
        });

        onBeforeUnmount(() => {
            if (facetDebounceTimeout) {
                clearTimeout(facetDebounceTimeout);
            }
        });

        watch(
            () => props.initialParams,
            (params) => {
                if (props.persistInUrl || !hydrated.value) {
                    return;
                }

                applyInitialParams(params);
                emitParams();
                scheduleFacetRefresh();
            },
            { deep: true }
        );

        const solventOptions = computed(() =>
            buildFacetOptions(
                facetAvailability.value.solvent,
                formatSolventLabel
            )
        );

        const temperatureOptions = computed(() =>
            buildFacetOptions(
                facetAvailability.value.temperature,
                (value) => `${value} K`
            )
        );

        const tubeDiameterOptionsComputed = computed(() =>
            buildStaticFacetOptions(
                tubeDiameterStaticOptions,
                facetAvailability.value.tube_diameter,
                facetsLoaded.value
            )
        );

        const nucleusOptionsComputed = computed(() =>
            buildStaticFacetOptions(
                nucleusStaticOptions,
                facetAvailability.value.nucleus,
                facetsLoaded.value
            )
        );

        const protonFrequencyOptions = computed(() =>
            buildFacetOptions(
                facetAvailability.value.proton_frequency,
                (value) => `${value} MHz`
            )
        );

        const nmrMethodOptions = computed(() =>
            buildFacetOptions(facetAvailability.value.nmr_method)
        );

        const pulseSequenceOptions = computed(() =>
            buildFacetOptions(facetAvailability.value.pulse_sequence)
        );

        const numberOfScansOptions = computed(() =>
            buildFacetOptions(facetAvailability.value.number_of_scans)
        );

        const manufacturerOptions = computed(() =>
            buildFacetOptions(facetAvailability.value.manufacturer)
        );

        const instrumentModelOptions = computed(() =>
            buildFacetOptions(facetAvailability.value.instrument_model)
        );

        const disabledTubeDiameters = computed(() => []);
        const disabledNuclei = computed(() => []);
        const disabledProtonFrequencies = computed(() => []);

        const activeFilterCount = computed(() => {
            let count = 0;

            if (freeText.value?.trim()) {
                count++;
            }
            if (solvent.value) {
                count++;
            }
            if (temperature.value) {
                count++;
            }
            if (tubeDiameter.value) {
                count++;
            }
            if (acquisitionNucleus.value) {
                count++;
            }
            if (protonFrequency.value) {
                count++;
            }
            if (nmrMethod.value) {
                count++;
            }
            if (pulseSequence.value) {
                count++;
            }
            if (numberOfScans.value) {
                count++;
            }
            if (manufacturer.value) {
                count++;
            }
            if (instrumentModel.value) {
                count++;
            }

            return count;
        });

        const hasAnyValue = computed(() => activeFilterCount.value > 0);

        const clearAll = () => {
            freeText.value = "";
            solvent.value = "";
            temperature.value = "";
            tubeDiameter.value = "";
            acquisitionNucleus.value = "";
            protonFrequency.value = "";
            nmrMethod.value = "";
            pulseSequence.value = "";
            numberOfScans.value = "";
            manufacturer.value = "";
            instrumentModel.value = "";
        };

        watch(
            [
                freeText,
                solvent,
                temperature,
                tubeDiameter,
                acquisitionNucleus,
                protonFrequency,
                nmrMethod,
                pulseSequence,
                numberOfScans,
                manufacturer,
                instrumentModel,
            ],
            () => {
                if (!hydrated.value) {
                    return;
                }

                emitParams();
                scheduleFacetRefresh();
            }
        );

        return {
            freeText,
            solvent,
            temperature,
            tubeDiameter,
            acquisitionNucleus,
            protonFrequency,
            nmrMethod,
            pulseSequence,
            numberOfScans,
            manufacturer,
            instrumentModel,
            hasAnyValue,
            activeFilterCount,
            clearAll,
            solventOptions,
            temperatureOptions,
            tubeDiameterOptions: tubeDiameterOptionsComputed,
            nucleusOptions: nucleusOptionsComputed,
            protonFrequencyOptions,
            nmrMethodOptions,
            pulseSequenceOptions,
            numberOfScansOptions,
            manufacturerOptions,
            instrumentModelOptions,
            facetsLoaded,
            disabledTubeDiameters,
            disabledNuclei,
            disabledProtonFrequencies,
            sectionClass,
            sectionTitleClass,
        };
    },
};
</script>
